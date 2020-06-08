<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "scm_warehouse_out".
 * @property string $id
 * @property integer $material_out_num
 * @property string $author
 * @property integer $status
 * @property string $date
 */
class ScmWarehouseOut extends \yii\db\ActiveRecord
{
    public $startTime;
    public $endTime;
    public $projectType;
    public $material_num;
    public $orgId;

    // 待确认
    const NO_CONFIRM = 1;
    // 正在出库
    const OUTTING = 2;
    // 出库完成
    const OUTTED = 3;
    // 审核成功
    const AUDIT_SUCCESS = 4;
    // 审核失败
    const AUDIT_FAILURE = 5;
    // 复审完成
    const RETRIAL_COMPLETION = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_warehouse_out';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'material_id', 'material_out_num', 'status', 'material_type_id'], 'integer'],
            [['confirm_date'], 'string'],
            [['date'], 'safe'],
            [['author'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'material_id'      => '出库物料',
            'material_out_num' => '物料数量/包',
            'author'           => '领料人',
            'status'           => '是否领料',
            'date'             => '创建时间',
            'confirm_date'     => '确认领料日期',
            'startTime'        => '开始时间',
            'endTime'          => '结束时间',
            'warehouse_id'     => '分库',
            'projectType'      => '项目类型',
            'orgId'            => '分公司',
        ];
    }

    /**
     * 获取出库单状态数组
     * @return array 获取出库单状态数组
     */
    public function getStatusArray()
    {
        return array(
            '1' => '确认',
            '2' => '已领料',
        );
    }

    /**
     * 获取出库单状态数组
     * @return string 产品状态
     */
    public function getStatus()
    {
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->status];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(ScmWarehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * 批量添加数据
     * @author  zgw
     * @version 2016-08-13
     * @param   [type]     $data [description]
     */
    public static function addAll($data)
    {
        return Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['material_id', 'material_out_num', 'author', 'status', 'date', 'time', 'material_type_id'], $data)->execute();
    }

    /**
     * 修改出库单数据
     * @author  zgw
     * @version 2016-08-18
     * @param   [type]     $id     [description]
     * @param   [type]     $status [description]
     * @return  [type]             [description]
     */
    public static function changeStatus($id, $status)
    {
        return self::updateAll(['status' => $status], ['id' => $id]);
    }

    /**
     * 获取出库单数据
     * @author  zgw
     * @version 2016-08-18
     * @param   array     $where 查询条件
     * @return  array            出库单数据
     */
    public static function getWarehouseOutList($where, $group = '')
    {
        return self::find()->where($where)->groupby($group)->all();
    }

    public static function getWarehouseOutDetail($where)
    {
        return self::find()->where($where)->one();
    }

    /**
     * 获取日常任务所需物料数组（发送出库单使用）
     * @author  zgw
     * @version 2016-08-22
     * @param   array     $where 查询条件
     * @return  array            返回的数据组
     */
    public static function getTaskMaterial($where)
    {
        $taskMaterialArr  = [];
        $taskMaterialList = self::getWarehouseOutList($where);
        foreach ($taskMaterialList as $taskMaterial) {
            if ($taskMaterial->material->weight > 0) {
                $taskMaterialArr[$taskMaterial->material_id]['content'] = $taskMaterial->material->materialType->material_type_name . '：' . $taskMaterial->material->weight . $taskMaterial->material->materialType->spec_unit;
            } else {
                $taskMaterialArr[$taskMaterial->material_id]['content'] = $taskMaterial->material->materialType->material_type_name . '：';
            }
            $taskMaterialArr[$taskMaterial->material_id]['unit']             = $taskMaterial->material->materialType->unit;
            $taskMaterialArr[$taskMaterial->material_id]['packets'][]        = $taskMaterial->material_out_num;
            $taskMaterialArr[$taskMaterial->material_id]['material_type_id'] = $taskMaterial->material->material_type;
        }

        foreach ($taskMaterialArr as $key => &$taskMaterial) {
            $taskMaterial['packets'] = array_sum($taskMaterial['packets']);
        }
        return $taskMaterialArr;
    }

    /**
     *  出库明细统计
     *  @param param
     *  @return array
     **/
    public static function getWarehouseOutInfo($dateUserList)
    {
        $libraryArr = [];
        if ($dateUserList) {
            foreach ($dateUserList as $dateUserArr) {
                $libraryArr[$dateUserArr['date']][$dateUserArr->user->name]['出库'] = Tools::map(self::find()->select('material_type_id, material_id, sum(material_out_num) as material_out_num')->where(['date' => $dateUserArr['date'], 'author' => $dateUserArr['author']])->groupBy('material_id')->asArray()->all(), 'material_id', 'material_out_num', 'material_type_id');
                unset($libraryArr[$dateUserArr['date']][$dateUserArr->user->name]['']);
            }
        }
        return $libraryArr;
    }

    /**
     *  入库明细统计
     *  @param param
     *  @return array
     **/
    public static function getScmStockInfo($scmStockArr)
    {
        $warehousArr = [];
        foreach ($scmStockArr as $scmStockObj) {
            $inWarehousArr = ScmStock::find()->select('material_id, sum(material_num) as material_num')->where(["FROM_UNIXTIME(ctime,'%Y-%m-%d')" => $scmStockObj['date'], 'distribution_clerk_id' => $scmStockObj['distribution_clerk_id']])->groupBy('material_id')->all();
            foreach ($inWarehousArr as $inWarehous) {
                if (!isset($inWarehous->material)) {
                    continue;
                }
                $warehousArr[$scmStockObj['date']][$scmStockObj->user->name]['入库'][$inWarehous->material->material_type][$inWarehous->stockNum->material_id] = $inWarehous->stockNum->material_num;
            }
        }
        return $warehousArr;
    }

    /**
     * 导出时获取数据
     * @author  zgw
     * @version 2016-10-11
     * @param   string     $param [description]
     * @param   [type]     $type  [description]
     * @return  [type]            [description]
     */
    public static function getWarehousingDetails($param = '', $type)
    {
        if ($type == 1) {
            $title = '出库';
        } else {
            $title = '入库';
        }
        $query        = self::getWhere($param, $type);
        $warehouseArr = $query->all();

        $warehousingDetails = [];
        foreach ($warehouseArr as $warehouseObj) {
            $materialTypeId = $warehouseObj->material->material_type;

            $warehousingDetails[$warehouseObj['date']][$warehouseObj->user->name][$title][$materialTypeId][$warehouseObj['material_id']] = $warehouseObj['material_num'];
        }
        return $warehousingDetails;
    }

    /**
     * 组装查询条件
     * @author  zgw
     * @version 2016-10-11
     * @param   [type]     $param [description]
     * @param   [type]     $type  [description]
     * @return  [type]            [description]
     */
    public static function getWhere($param, $type)
    {
        if ($type == 1) {
            $query  = self::find()->where(['status' => 3])->select(['date', 'author', 'material_id', 'material_type_id', 'sum(material_out_num) as material_num'])->groupBy('date, author, material_id')->orderBy('date desc');
            $date   = "date";
            $author = "author";
        } else {
            $query  = ScmStock::find()->where(['reason' => 2])->select(["FROM_UNIXTIME(ctime,'%Y-%m-%d') as date", "distribution_clerk_id", "material_id", "sum(material_num) as material_num"])->groupBy('date, distribution_clerk_id, material_id')->orderBy('date desc');
            $date   = "FROM_UNIXTIME(ctime,'%Y-%m-%d')";
            $author = "distribution_clerk_id";
        }
        // 追加查询条件
        if (!$param) {
            // 默认按开始日期大于当前月1号
            $query->andFilterWhere(['>=', $date, date('Y-m') . '-01']);
        } else {
            if ($param["startTime"]) {
                $query->andFilterWhere(['>=', $date, $param["startTime"]]);
            }
            if ($param["endTime"]) {
                $query->andFilterWhere(['<=', $date, $param["endTime"]]);
            }
            if ($param['author']) {
                $query->andFilterWhere([
                    $author => $param['author'],
                ]);
            }
        }
        $managerOrgId = Manager::getManagerBranchID();
        $orgId        = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : $managerOrgId;
        if ($orgId > 1) {
            $query->joinWith('user u')->andFilterWhere([
                'u.org_id' => $orgId,
            ]);
        }
        return $query;
    }

    /**
     * 修改日常任务负责人同时修改出库单
     * @author  zgw
     * @version 2016-11-07
     * @param   [type]     $oldAuthor        [description]
     * @param   [type]     $newAuthor        [description]
     * @param   [type]     $deliveryTaskList [description]
     * @return  [type]                       [description]
     */
    public static function changeOut($oldAuthor, $newAuthor, $deliveryTaskList)
    {
        if (!$oldAuthor || !$newAuthor || !$deliveryTaskList) {
            return true;
        }
        $changRes = true;
        // 查询该成员24小时之内有没有未领料的出库单
        $oldWhere         = ['and', ['author' => $oldAuthor], ['>', 'time', time() - 24 * 3600], ['!=', 'status', self::RECEIVE_YES]];
        $oldAuthorOutList = self::find()->where($oldWhere)->all();
        // 查询新的配送员24小时之内是否有未领料的出库单
        $newWhere         = ['and', ['author' => $newAuthor], ['>', 'time', time() - 24 * 3600]];
        $newAuthorOutList = self::find()->where($newWhere)->orderBy('id')->all();
        // 如果新的配送员24小时内没有出库单或者有未领取的出库单
        if ((!$newAuthorOutList || $newAuthorOutList[0]['status'] != self::RECEIVE_YES) && $oldAuthorOutList) {
            $changRes = self::minusOut($oldAuthorOutList, $newAuthorOutList, $deliveryTaskList, $newAuthor);
        }
        return $changRes;
    }

    /**
     * 修改原配送员的物料
     * @author  zgw
     * @version 2016-10-24
     * @param   array     $oldAuthorOutList 原配送员的未领取的出库单列表
     * @param   array     $newAuthorOutList 修改后配送员的未领取出库单列表
     * @param   array     $deliveryTaskList 任务所需物料列表
     * @param   string    $newAuthor        修改后配送员
     * @return  boole
     */
    public static function minusOut($oldAuthorOutList, $newAuthorOutList, $deliveryTaskList, $newAuthor)
    {
        // 初始化要新增出库单的日期
        $outDate = '';

        $oldAuthorObjSaveRes = true;
        // 遍历原配送员的出库单进行取料操作
        foreach ($oldAuthorOutList as $oldAuthorOutObj) {
            $outDate = $oldAuthorOutObj->date;
            // 遍历任务所需物料
            foreach ($deliveryTaskList as $deliveryTaskArr) {

                if ($deliveryTaskArr['packets'] && ($oldAuthorOutObj->material_id == $deliveryTaskArr['material_id'])) {
                    // 减去原配送员该任务所需的物料
                    $oldAuthorOutObj->material_out_num = $oldAuthorOutObj->material_out_num - $deliveryTaskArr['packets'];
                    // 如果相减后小于0则从出库单中删除该物料的记录否则保存记录
                    if ($oldAuthorOutObj->material_out_num <= 0) {
                        if ($oldAuthorOutObj->delete() === false) {
                            $oldAuthorObjSaveRes = false;
                        }
                    } else {
                        if ($oldAuthorOutObj->save() === false) {
                            $oldAuthorObjSaveRes = false;
                        }
                    }

                }
            }
        }
        // 给修改后的配送员添加任务所需物料
        $newAuthorObjSaveRes = self::addOut($newAuthorOutList, $deliveryTaskList, $newAuthor, $outDate);

        if ($oldAuthorObjSaveRes && $newAuthorObjSaveRes) {
            return true;
        }
        return false;
    }

    /**
     * 修改新配送员的物料
     * @author  zgw
     * @version 2016-10-24
     * @param   [type]     $newAuthorOutList [description]
     * @param   [type]     $deliveryTaskList [description]
     * @param   [type]     $date             [description]
     * @param   [type]     $newAuthor        [description]
     */
    public static function addOut($newAuthorOutList, $deliveryTaskList, $newAuthor, $date)
    {
        $newAuthorObjSaveRes = true;
        $warehouseId         = '';
        if ($newAuthorOutList) {
            // 获取修改后配送员出库单中所有的物料id
            $newAuthorOutMaterialIdArr = \yii\helpers\ArrayHelper::getColumn($newAuthorOutList, 'material_id');
            // 遍历任务所需物料
            foreach ($deliveryTaskList as $materialTypeId => $deliveryTaskArr) {
                if (!$deliveryTaskArr['packets']) {
                    continue;
                }
                // 判断任务所需物料是否存在配送员出库单中，存在修改，不存在添加
                if (in_array($deliveryTaskArr['material_id'], $newAuthorOutMaterialIdArr)) {
                    foreach ($newAuthorOutList as $newAuthorOutObj) {
                        if ($newAuthorOutObj->material_id == $deliveryTaskArr['material_id']) {
                            $newAuthorOutObj->material_out_num += $deliveryTaskArr['packets'];
                            if ($newAuthorOutObj->save() === false) {
                                $newAuthorObjSaveRes = false;
                            }
                        }
                    }
                } else {
                    // 获取物料分库
                    $warehouseId = $newAuthorOutList[0]->warehouse_id;
                    $status      = $newAuthorOutList[0]->status;
                    // 获取物料状态
                    $newOutObj                   = new ScmWarehouseOut();
                    $newOutObj->warehouse_id     = $warehouseId;
                    $newOutObj->material_type_id = $materialTypeId;
                    $newOutObj->material_id      = $deliveryTaskArr['material_id'];
                    $newOutObj->material_out_num = $deliveryTaskArr['packets'];
                    $newOutObj->date             = $date;
                    $newOutObj->author           = $newAuthor;
                    $newOutObj->status           = $status;
                    if ($newOutObj->save() === false) {
                        $newAuthorObjSaveRes = false;
                    }

                }
            }
        } else {
            foreach ($deliveryTaskList as $materialTypeId => $deliveryTaskArr) {
                if ($deliveryTaskArr['packets']) {
                    $newOutObj                   = new ScmWarehouseOut();
                    $newOutObj->warehouse_id     = 0;
                    $newOutObj->material_type_id = $materialTypeId;
                    $newOutObj->material_id      = $deliveryTaskArr['material_id'];
                    $newOutObj->material_out_num = $deliveryTaskArr['packets'];
                    $newOutObj->date             = $date;
                    $newOutObj->author           = $newAuthor;
                    $newOutObj->status           = self::SEND;
                    $newOutObj->time             = time();
                    if ($newOutObj->save() === false) {
                        $newAuthorObjSaveRes = false;
                    }
                }
            }
        }
        return $newAuthorObjSaveRes;
    }

    /**
     * 获取分公司下其他运维人员
     * @author wangxiwen
     * @version 2018-06-22
     * @param $author 用户ID
     * @return array
     */
    public static function getUserList($author, $userArr)
    {
        $userList = [];
        foreach ($userArr as $user) {
            if ($user != $author) {
                $userList[] = $user;
            }
        }
        return $userList;
    }

    /**
     * 获取出库单状态
     * @author wangxiwen
     * @version 2018-06-22
     * @param $userList 出领料人外的其他同公司下运维人员列表
     * @return false (除自己外存在其他人员未领取)|true(除自己外其他人员均已领取)
     */
    public static function getOutList($userList)
    {
        if (empty($userList)) {
            return true;
        }
        $statusArr = self::find()
            ->distinct()
            ->andWhere(['date' => date('Y-m-d')])
            ->andWhere(['in', 'author', $userList])
            ->select('status')
            ->column();
        if (in_array(self::NO_CONFIRM, $statusArr)) {
            return false;
        }
        return true;
    }

    /**
     * 获取待确认出库单数据
     * @author wangxiwen
     * @version 2018-10-10
     * @param string $userId 用户ID
     * @return
     */
    public function getWarehouseOut($userId)
    {
        if (!$userId) {
            return [];
        }
        return self::find()
            ->andWhere(['author' => $userId])
            ->andWhere(['status' => self::NO_CONFIRM])
            ->andWhere(['date' => date("Y-m-d")])
            ->all();
    }

    /**
     * 获取需要领取的物料
     * @author wangxiwen
     * @version 2018-10-10
     * @param object $outList 出库单
     * @return
     */
    public function getScmWarehouseOutMaterial($outList)
    {
        $packetArr = [];
        foreach ($outList as $value) {
            $packetArr[$value->date][$value->author][$value->status]['warehouseName']          = isset($value->warehouse->name) ? $value->warehouse->name : '';
            $packetArr[$value->date][$value->author][$value->status]['distribution_user_name'] = $value->user ? $value->user->name : $value->author;
            if (!isset($value->material)) {
                continue;
            }
            // 物料分类名称
            $packetArr[$value->date][$value->author][$value->status]['data'][$value->material_id]['material_name'] = $value->material->materialType->material_type_name;
            if ($value->material->weight > 0) {
                // 物料规格
                $packetArr[$value->date][$value->author][$value->status]['data'][$value->material_id]['format'] = $value->material->weight . $value->material->materialType->spec_unit;
            } else {
                // 物料规格
                $packetArr[$value->date][$value->author][$value->status]['data'][$value->material_id]['format'] = '';
            }
            $packetArr[$value->date][$value->author][$value->status]['data'][$value->material_id]['unit']             = $value->material->materialType->unit;
            $packetArr[$value->date][$value->author][$value->status]['data'][$value->material_id]['material_out_num'] = $value->material_out_num;
        }
        return $packetArr;
    }

    /**
     * 更新出库单状态为出库完成
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $userList 除自己以外的运维人员
     * @return boolean
     */
    public function saveStatusOut($userList)
    {
        $outtingList = $this->getWarehouseOutting($userList);
        if (empty($outtingList)) {
            return true;
        }
        foreach ($outtingList as $outting) {
            $outting->status = self::OUTTED;
            $saveRes         = $outting->save();
            if (!$saveRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取正在出库状态的出库数据
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $userList 除自己以外的运维人员
     * @return array
     */
    private function getWarehouseOutting($userList)
    {
        return self::find()
            ->andWhere(['author' => $userList])
            ->andWhere(['date' => date('Y-m-d')])
            ->andWhere(['status' => self::OUTTING])
            ->all();
    }

    /**
     * 修改当前领料人员的出库状态和领料信息
     * @author wangxiwen
     * @version 2018-10-10
     * @param object $warehouseOutList 待确认的出库单数据
     * @param array $material 领料信息
     * @param int $status 状态
     * @param array $scmMaterial 物料规格信息
     * @return boolean
     */
    public function saveScmWarehouseOut($warehouseOutList, $material, $status, $scmMaterial)
    {
        $saveRes = true;
        //遍历出库单
        foreach ($warehouseOutList as $warehouseOut) {
            // 修改出库单状态
            $warehouseOut->status = $status;
            if (empty($material[$warehouseOut->material_id])) {
                continue;
            }
            $warehouseOut->material_out_num = $material[$warehouseOut->material_id];
            $warehouseOut->confirm_date     = date('Y-m-d H:i:s');
            $outRes                         = $warehouseOut->save();
            if (!$outRes) {
                $saveRes = false;
                break;
            }
            //获取指定仓库的信息
            $inventoryModel = ScmTotalInventory::getInventoryDetail($warehouseOut->warehouse_id, $warehouseOut->material_id);
            if (!empty($inventoryModel)) {
                $inventoryModel->total_number = $inventoryModel->total_number - $warehouseOut->material_out_num;
                $inventory                    = $inventoryModel->save();
                if (!$inventory) {
                    $saveRes = false;
                    break;
                }
            }
            //修改运维人员手中剩余物料
            $userSurplusMaterialRes = ScmUserSurplusMaterial::saveUserSurplusMaterial($warehouseOut, $material, $scmMaterial);
            if (!$userSurplusMaterialRes) {
                $saveRes = false;
                break;
            }
        }
        return $saveRes;
    }

    /**
     * 获取真实出库单数据(领料完成后生成真实出库统计数据使用)
     * @author  wangxiwen
     * @version 2018-10-10
     * @param   array     $userArr 运维人员
     * @return  array     出库单数据
     */
    public static function getRealWarehouseOut($userArr)
    {
        return self::find()
            ->andWhere(['in', 'author', $userArr])
            ->andWhere(['date' => date('Y-m-d')])
            ->andWhere(['status' => self::OUTTED])
            ->all();
    }

    /**
     * 保存出库单
     * @author wangxiwen
     * @version 2018-10-12
     * @param array $outList 出库单
     * @return array
     */
    public static function saveWarehouseOut($outList)
    {
        $insertData = [];
        foreach ($outList as $outArr) {
            foreach ($outArr as $out) {
                if (!$out['material_out_num'] || !$out['author'] || !$out['material_id']) {
                    continue;
                }
                $insertData[] = [$out['author'], $out['warehouse_id'], $out['material_id'], $out['material_out_num'], $out['status'], $out['date'], $out['confirm_date'], $out['material_type_id'],
                ];
            }
        }
        if (!empty($insertData)) {
            $insertKey = ['author', 'warehouse_id', 'material_id', 'material_out_num', 'status', 'date', 'confirm_date', 'material_type_id'];
            $result    = Yii::$app->db->createCommand()->batchInsert(self::tableName(), $insertKey, $insertData)->execute();
            if (!$result) {
                return false;
            }
        }
        return true;
    }

}
