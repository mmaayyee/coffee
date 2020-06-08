<?php

namespace backend\controllers;

use backend\models\DistributionSparePackets;
use backend\models\Manager;
use backend\models\ManagerLog;
use backend\models\ScmMaterial;
use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmWarehouse;
use backend\models\ScmWarehouseOut;
use backend\models\ScmWarehouseOutGram;
use backend\models\ScmWarehouseOutSearch;
use common\models\WxMember;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * ScmWarehouseOutController implements the CRUD actions for ScmWarehouseOut model.
 */
class ScmWarehouseOutController extends Controller
{
    /** 出库单状态 */
    const SEND        = 1; // 发送状态
    const SURE        = 2; // 确认状态
    const RECEIVE_NO  = 3; // 领料状态
    const RECEIVE_YES = 4; // 已领料

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'check-status', 'spare-packets', 'get-surplus-material', 'get-receive-material', 'update-save', 'get-warehouse', 'confirm', 'stock-out-details', 'estimates-single'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmWarehouseOut models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('出库单管理')) {
            return $this->redirect(['site/login']);
        }
        // 按照人和日期分组分页查询
        $params      = Yii::$app->request->queryParams;
        $searchModel = new ScmWarehouseOutSearch();
        $query       = $searchModel->search($params);
        $pages       = new \yii\data\Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $outList     = $query->offset($pages->offset)->limit($pages->limit)
            ->all();
        // 根据分组查询结果查询详细信息
        $packetArr = [];
        foreach ($outList as $outObj) {
            $outArr = ScmWarehouseOut::getWarehouseOutList(['author' => $outObj->author, 'date' => $outObj->date]);
            foreach ($outArr as $out) {
                $packetArr[$out->date][$out->author][$out->status]['warehouseName']          = isset($out->warehouse->name) ? $out->warehouse->name : '';
                $packetArr[$out->date][$out->author][$out->status]['distribution_user_name'] = $out->user ? $out->user->name : $out->author;
                if (!isset($out->material)) {
                    continue;
                }

                if ($out->material->weight > 0) {
                    $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['material_name'] = $out->material->materialType->material_type_name . '-' . $out->material->supplier->name . '：' . $out->material->weight . $out->material->materialType->spec_unit;
                } else {
                    $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['material_name'] = $out->material->materialType->material_type_name . '-' . $out->material->supplier->name . '：';
                }
                $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['unit']               = $out->material->materialType->unit;
                $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['material_out_num'][] = $out->material_out_num;
                $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['weight_unit']        = $out->material->materialType->weight_unit;
                //查询散料的重量
                $gram                                                                                                = ScmWarehouseOutGram::getMaterialGram('material_out_gram', ['warehouse_out_id' => $out->id]);
                $packetArr[$out->date][$out->author][$out->status]['data'][$out->material_id]['material_out_gram'][] = $gram['material_out_gram'];
            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'packetArr'   => $packetArr,
            'pages'       => $pages,
        ]);
    }

    /**
     * 设置备用料包
     * @author  zgw
     * @version 2016-08-17
     * @return  [type]     [description]
     */
    public function actionSparePackets()
    {
        $data   = Yii::$app->request->post('data');
        $addRes = DistributionSparePackets::addAll($data);
        if ($addRes === false) {
            Yii::$app->getSession()->setFlash('error', '备用料包设置失败');
        }
        return $this->redirect(['/distribution-task/index']);
    }

    /**
     * 发送出库单保存操作
     * @author  zgw
     * @version 2016-08-23
     * @param   string     $date    日期
     * @param   string     $author  作者
     * @param   intege     $stockId 仓库id
     * @return  boole
     */
    private function saveSendOutBill($date, $author, $stockId)
    {
        // 计算配送员要领取的物料总数（日常任务所需物料加备用料包物料减去配送员当天的剩余物料）
        $receiveMaterialArr = $this->getSurplusSpareTaskMaterial($date, $author);

        // 修改出库单状态
        $saveRes = true;
        // 查询条件
        $where = ['date' => $date, 'author' => $author, 'status' => 1];
        foreach ($receiveMaterialArr as $materialId => $receiveMaterial) {
            // 增加物料id查询条件
            $where['material_id'] = $materialId;
            // 查询出库单中是否有该物料
            $outModel = ScmWarehouseOut::getWarehouseOutDetail($where);
            // 如果物料不存在出库单中则新增
            if (!$outModel) {
                if ($receiveMaterial == 0) {
                    continue;
                }
                $warehouseOutObj                   = new ScmWarehouseOut();
                $warehouseOutObj->author           = $author;
                $warehouseOutObj->date             = $date;
                $warehouseOutObj->warehouse_id     = $stockId;
                $warehouseOutObj->status           = 2;
                $warehouseOutObj->material_out_num = $receiveMaterial['packets'];
                $warehouseOutObj->material_id      = $materialId;
                $warehouseOutObj->material_type_id = $receiveMaterial['material_type_id'];
                $warehouseOutObj->time             = time();
                if (!$warehouseOutObj->save()) {
                    $saveRes = false;
                }
            } else if ($receiveMaterial == 0) {
                // 如果物料存在出库单中但是包数为0则删除
                if (!$outModel->delete()) {
                    $saveRes = false;
                }
            } else {
                // 如果物料存在且包数不为0则修改
                $outModel->material_out_num = $receiveMaterial['packets'];
                $outModel->status           = 2;
                $outModel->warehouse_id     = $stockId;
                $outModel->material_type_id = $receiveMaterial['material_type_id'];
                if (!$outModel->save()) {
                    $saveRes = false;
                }
            }
        }
        return $saveRes;
    }

    /**
     * 修改出库单
     * @author  zgw
     * @version 2016-10-18
     * @param   string     $date   日期
     * @param   string     $author 领料人
     * @return                     渲染修改页面
     */
    public function actionUpdate($date, $author)
    {
        if (!Yii::$app->user->can('编辑出库单')) {
            return $this->redirect(['site/login']);
        }
        // 获取已发送未确认的出库单
        $warehouseOutList = ScmWarehouseOut::getWarehouseOutList(['date' => $date, 'author' => $author, 'status' => ScmWarehouseOut::RECEIVE_NO]);
        $warehouseOutArr  = $materialList  = [];
        $warehouse_id     = $warehouseOutList[0]->warehouse_id;
        foreach ($warehouseOutList as $warehouseOut) {
            $materialArr = $warehouseOut->material->materialType->material;
            foreach ($materialArr as $material) {
                $materialList[$material->id] = $material;
            }
        }

        foreach ($materialList as $materialId => $material) {
            $warehouseOutArr[$materialId]['id']                = '';
            $warehouseOutArr[$materialId]['material_id']       = $material->id;
            $warehouseOutArr[$materialId]['material_out_num']  = '';
            $warehouseOutArr[$materialId]['material_out_gram'] = '';
            $warehouseOutArr[$materialId]['warehouse_id']      = $warehouse_id;
            $warehouseOutArr[$materialId]['material_type_id']  = $material->material_type;
            $warehouseOutArr[$materialId]['unit']              = $material->materialType->unit;
            $warehouseOutArr[$materialId]['weight_unit']       = $material->materialType->weight_unit;

            foreach ($warehouseOutList as $warehouseOut) {
                if ($material->weight) {
                    $warehouseOutArr[$materialId]['label'] = $material->materialType->material_type_name . '：' . $material->supplier->name . ' ' . $material->weight . $material->materialType->spec_unit;
                } else {
                    $warehouseOutArr[$materialId]['label'] = $material->materialType->material_type_name . '：' . $material->supplier->name;
                }
                if ($material->id == $warehouseOut->material_id) {
                    $warehouseOutArr[$materialId]['id']               = $warehouseOut->id;
                    $warehouseOutArr[$materialId]['material_out_num'] = $warehouseOut->material_out_num;
                    //查询散料数量
                    $gram                                              = ScmWarehouseOutGram::getMaterialGram('material_out_gram', ['warehouse_out_id' => $warehouseOut->id]);
                    $warehouseOutArr[$materialId]['material_out_gram'] = $gram['material_out_gram'];
                }
            }
        }
        return $this->render('update', ['warehouseOutList' => $warehouseOutArr, 'date' => $date, 'author' => $author]);
    }

    /**
     * 修改保存
     * @author  zgw
     * @version 2016-08-23
     * @return  [type]     [description]
     */
    public function actionUpdateSave()
    {
        $data             = Yii::$app->request->post();
        $date             = $data['date'];
        $author           = $data['author'];
        $warehouseOutList = $data['data'];
        $saveRes          = true;
        $transaction      = Yii::$app->db->beginTransaction();
        foreach ($warehouseOutList as $warehouseOut) {
            if ($warehouseOut['id']) {
                $packets  = $warehouseOut['packets'] ? $warehouseOut['packets'] : 0;
                $outModel = ScmWarehouseOut::updateAll(['material_out_num' => $packets, 'warehouse_id' => $warehouseOut['warehouse_id']], ['id' => $warehouseOut['id']]);
                $logres   = ManagerLog::saveLog(Yii::$app->user->id, '修改出库单', \backend\models\ManagerLog::UPDATE, '修改了id为' . $warehouseOut['id'] . '的出库单');
                //处理散料数据
                $gramRes = true;
                if (isset($warehouseOut['material_out_gram']) && $warehouseOut['material_out_gram'] > 0) {
                    //清除存在的散料
                    ScmWarehouseOutGram::deleteAll('warehouse_out_id = ' . $warehouseOut['id']);
                    //添加散料
                    $gramRes = $this->actionAddGramMaterial($warehouseOut, $warehouseOut['id']);
                }

                if ($logres === false || $outModel === false || $gramRes === false) {
                    $saveRes = false;
                }
            } else if (isset($warehouseOut['packets']) || isset($warehouseOut['material_out_gram'])) {

                $outModel                   = new ScmWarehouseOut();
                $outModel->date             = $date;
                $outModel->author           = $author;
                $outModel->material_id      = $warehouseOut['material_id'];
                $outModel->warehouse_id     = $warehouseOut['warehouse_id'] ? $warehouseOut['warehouse_id'] : 0;
                $outModel->status           = 2;
                $outModel->material_out_num = !empty($warehouseOut['packets']) ? $warehouseOut['packets'] : 0;
                $outModel->material_type_id = $warehouseOut['material_type_id'];

                $outModel->time = time();

                $logres    = ManagerLog::saveLog(Yii::$app->user->id, '修改出库单', \backend\models\ManagerLog::UPDATE, '修改了物料id为' . $warehouseOut['material_id'] . '的出库单');
                $outResult = $outModel->save();

                $gramRes = true;
                if (isset($warehouseOut['material_out_gram']) && $warehouseOut['material_out_gram'] > 0) {
                    //处理散料数据
                    $gramRes = $this->actionAddGramMaterial($warehouseOut, $outModel->id);
                }

                if ($logres === false || !$outResult || !$gramRes) {
                    $saveRes = false;
                }
            }
        }
        if ($saveRes) {
            $transaction->commit();
            return $this->redirect('index');
        } else {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '修改失败');
            return $this->redirect('index');
        }
    }

    /**
     * 插入散料数据
     * @param $warehouseOut
     * @param int $warehouseOutId
     * @return bool
     */
    private function actionAddGramMaterial($warehouseOut, $warehouseOutId = 0)
    {
        if ($warehouseOutId > 0) {
            $gramModel                   = new ScmWarehouseOutGram();
            $gramModel->warehouse_out_id = $warehouseOutId;
            //根据物料查出供应商
            $supplierId                   = ScmMaterial::getMaterialDetail('supplier_id', ['id' => $warehouseOut['material_id']]);
            $gramModel->supplier_id       = $supplierId['supplier_id'];
            $gramModel->material_out_gram = $warehouseOut['material_out_gram'];
            $gramModel->material_type_id  = $warehouseOut['material_type_id'];
            return $gramModel->save();
        } else {
            return false;
        }

    }

    /**
     * 确认领料
     * @author  zgw
     * @version 2016-08-23
     * @return  [type]     [description]
     */
    public function actionConfirm()
    {
        $data   = Yii::$app->request->get();
        $date   = $data['date']; //领料日期
        $author = $data['author']; //领料人
        // 获取要确认的出库单列表
        $warehouseOutList = ScmWarehouseOut::getWarehouseOutList(['date' => $date, 'author' => $author, 'status' => ScmWarehouseOut::RECEIVE_NO]);
        $saveRes          = true;
        $transaction      = Yii::$app->db->beginTransaction();
        //遍历出库单
        foreach ($warehouseOutList as $warehouseOut) {
            // 修改出库单状态
            $warehouseOut->status = ScmWarehouseOut::CONFIRMED;
            $outRes               = $warehouseOut->save();
            //添加操作日志
            $logres = ManagerLog::saveLog(Yii::$app->user->id, '确认领料', \backend\models\ManagerLog::UPDATE, '修改了id为' . $warehouseOut->id . '的出库单');

            if ($logres === false || $outRes === false) {
                $saveRes = false;
                break;
            }
        }
        if ($saveRes) {
            $transaction->commit();
            return $this->redirect('index');
        } else {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '确认领料失败');
            return $this->redirect('index');
        }
    }

    /**
     * 获取配送员剩余物料
     * @author  zgw
     * @version 2016-08-22
     * @return  array     剩余物料数组
     */
    public function actionGetSurplusMaterial()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // 配送员id
        $distributionUserId = Yii::$app->request->get('distributionUserId');
        //剩余物料
        return $this->getInOutMaterial($distributionUserId);
    }

    /**
     * 获取配送员实际需要领取的
     * @author  zgw
     * @version 2016-08-22
     * @return  array     剩余物料数组
     */
    public function actionGetReceiveMaterial()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // 配送员id
        $distributionUserId = Yii::$app->request->get('distributionUserId');
        // 领料日期
        $date           = Yii::$app->request->get('date');
        $recivematerial = $this->getSurplusSpareTaskMaterial($date, $distributionUserId);
        return $recivematerial;
    }

    /**
     * 根据配送员所在分公司获取分库信息
     * @author  zgw
     * @version 2016-10-21
     * @return  [type]     [description]
     */
    public function actionGetWarehouse()
    {
        // 获取配送员的id
        $userid = Yii::$app->request->get('userid');
        if ($userid) {
            // 根据配送员id获取分公司id
            $orgId = WxMember::getOrgId($userid);
        } else {
            $orgId = Manager::getManagerBranchID();
        }
        $where = ['use' => ScmWarehouse::MATERIAL_USE];
        if ($orgId > 1) {
            $where['organization_id'] = $orgId;
        }
        // 根据分公司id获取物料分库
        $warehouseList = ScmWarehouse::getWarehouseNameArray($where);
        return json_encode($warehouseList);
    }

    /**
     * 获取剩余、备用、任务所需物料，并根据它们获取实际应该领取的物料
     * @author  zgw
     * @version 2016-08-22
     * @param   [type]     $date           [description]
     * @param   [type]     $recivematerial [description]
     * @return  [type]                     [description]
     */
    private function getSurplusSpareTaskMaterial($date, $distributionUserId)
    {
        if (!$date || !$distributionUserId) {
            return [];
        }
        // 备用物料
        $sparePacketList = DistributionSparePackets::getMaterialArr();
        // 任务所需物料
        $where            = ['date' => $date, 'author' => $distributionUserId, 'status' => 1];
        $taskMaterialList = ScmWarehouseOut::getTaskMaterial($where);
        // 剩余物料
        $surplusMaterialList = $this->getInOutMaterial($distributionUserId);
        // 计算配送员要领取的物料总数（日常任务所需物料加备用料包物料减去配送员当天的剩余物料）
        return $this->getReceiveMaterial($surplusMaterialList, $sparePacketList, $taskMaterialList);
    }

    /**
     * 计算配送员实际需要领取的物料
     * @author  zgw
     * @version 2016-08-22
     * @param   array     $surplusMaterialList 剩余物料
     * @param   array     $sparePacketList     备用物料
     * @param   array     $taskMaterialList    任务所需物料
     * @return  array                          实际需要领取的物料
     */
    private function getReceiveMaterial($surplusMaterialList, $sparePacketList, $taskMaterialList)
    {
        $recivematerial = [];
        // 备用物料和任务所需物料为空返回空
        if (!$taskMaterialList) {
            return [];
        }
        // 获取备用料包的物料分类id
        $spareMaterialIdArr = array_keys($sparePacketList);
        // 获取任务所需物料id
        $taskMaterialIdArr = array_keys($taskMaterialList);
        // 初始化要领取的物料数组
        $addArr = [];
        // 将任务所需物料和备用物料整合
        foreach ($taskMaterialList as $materialId => $taskMaterialArr) {
            $addArr[$materialId] = $taskMaterialArr;
            // 如果任务所需物料也在备用料包中则两个的包数相加
            if (in_array($materialId, $spareMaterialIdArr)) {
                $addArr[$materialId]['packets'] = $taskMaterialArr['packets'] + $sparePacketList[$materialId]['packets'];
            } else {
                $addArr[$materialId] = $taskMaterialArr;
            }
        }
        // 将备用料包中有的但是任务中没有的物料加入到需要两区的物料数组中
        if ($sparePacketList) {
            foreach ($sparePacketList as $materialId => $sparePacketArr) {
                if (!in_array($materialId, $taskMaterialIdArr)) {
                    $addArr[$materialId] = $sparePacketArr;
                }
            }
        }
        // 如果剩余物料不存在则返回要领取的物料数组
        if (!$surplusMaterialList) {
            return $addArr;
        }
        // 剩余物料存在则计算要领取的物料（要领取的物料-剩余的物料）
        foreach ($addArr as $materialId => $materialArr) {
            if (isset($surplusMaterialList[$materialId])) {
                if ($materialArr['packets'] > $surplusMaterialList[$materialId]['packets']) {
                    $addArr[$materialId]['packets'] = $materialArr['packets'] - $surplusMaterialList[$materialId]['packets'];
                } else {
                    $addArr[$materialId] = 0;
                }
            }
        }

        return $addArr;

    }

    /**
     * 获取配送员的领取量和配送量并获取剩余量
     * @author  zgw
     * @version 2016-08-22
     * @param   string     $date               领料日期
     * @param   string     $distributionUserId 配送员id
     * @return  array                          剩余量
     */
    private function getInOutMaterial($distributionUserId)
    {
        $where               = ['author' => $distributionUserId];
        $surplusMaterialList = ScmUserSurplusMaterial::getSurplusMaterial($where);
        $surplusMaterialArr  = [];
        if ($surplusMaterialList) {
            foreach ($surplusMaterialList as $surplusMaterialObj) {
                $surplusMaterialArr[$surplusMaterialObj->material_id]['packets']          = $surplusMaterialObj->material_num;
                $surplusMaterialArr[$surplusMaterialObj->material_id]['content']          = $surplusMaterialObj->material->weight ? $surplusMaterialObj->material->materialType->material_type_name . '：' . $surplusMaterialObj->material->weight . $surplusMaterialObj->material->materialType->spec_unit : $surplusMaterialObj->material->materialType->material_type_name . '：';
                $surplusMaterialArr[$surplusMaterialObj->material_id]['material_type_id'] = $surplusMaterialObj->material->material_type;
                $surplusMaterialArr[$surplusMaterialObj->material_id]['unit']             = $surplusMaterialObj->material->materialType->unit;
            }
        }
        return $surplusMaterialArr;
    }

    /**
     * 获取配送员剩余物料
     * @author  wxz
     * @version 2018-05-22
     * @return  array     运维出库单详情
     */
    public function actionStockOutDetails()
    {
        return $this->render('stock-out-details');
    }
    /**
     * 获取配送员剩余物料
     * @author  wxz
     * @version 2018-05-22
     * @return  array    日常任务出库预估单修改
     */
    public function actionEstimatesSingle()
    {
        return $this->render('estimates-single');
    }
}
