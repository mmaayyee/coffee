<?php

namespace backend\controllers;

use backend\models\BuildingSearch;
use backend\models\Organization;
use backend\models\ScmWarehouse;
use common\models\AgentsApi;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use Yii;
use yii\web\Controller;

/**
 *   key=agents&secret=31532302c2f840d5eaf3ca765336dcdb   81fa510ab6c397c03a8aa4213467fd49
 *     http://www.erpbacked.com/index.php/agents-api/text?key=agents&secret=81fa510ab6c397c03a8aa4213467fd49&agents_number=001002&agents_name=代理商A&parant_number=001001
 */

class AgentsApiController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 加密验证
     * @author  zgw
     * @version 2016-11-11
     * @return  json     加密验证结果
     */
    private function verify()
    {
        $key          = Yii::$app->request->get("key");
        $secretString = Yii::$app->request->get("secret");
        $verifyRs     = AgentsApi::verifyService($key, $secretString);
        if (!$verifyRs) {
            AgentsApi::returnData(1, '加密验证失败');
        }
    }

    /**
     * 代理商添加（编辑）接口
     * post方式
     * @return json
     * @author zmy
     * @version 2016-11-11
     */
    public function actionCreateAgents()
    {
        try {
            // 加密验证
            $this->verify();
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            if (!$data['org_id']) {
                AgentsApi::returnData(1, '参数传输错误');
            }
            Organization::createOrganization($data);
        } catch (\Exception $e) {
            AgentsApi::returnData(1, '接口异常', $e->getMessage());
        }
    }
    /**
     * 代理商删除接口
     * get方式
     * @return json
     * @author zmy
     * @version 2016-11-11
     */
    public function actionDeleteAgents()
    {
        $this->verify();
        $orgId = Yii::$app->request->get("orgId");
        if ($orgId <= 0) {
            AgentsApi::returnData(1, '参数不正确');
        }
        // 删除代理商以及代理商仓库
        Organization::delAgents($orgId);
    }
    /**
     * 代理商添加（编辑）楼宇接口
     * @return json
     * @author zmy
     */
    public function actionCreateBuilding()
    {
        try {
            // $data = [
            //     'name'                  => '梦想',
            //     'build_number'          => '23',
            //     'build_type'            => 1,
            //     'build_status'          => 1,
            //     'contact_name'          => '如果',
            //     'contact_tel'           => '18211172615',
            //     'people_num'            => '3000',
            //     'province'              => '大概',
            //     'city'                  => '发给',
            //     'area'                  => '大概',
            //     'address'               => '朝阳区大望路',
            //     'longitude'             => '345.32',
            //     'latitude'              => '32.45',
            //     'first_free_strategy'   => 0,
            //     'strategy_change_date'  => date("Y-m-d"),
            //     'first_backup_strategy' => 1,
            //     'is_bind'               => 1,
            //     'organization_id'       => 6,
            //     'create_time' => time()
            // ];
            $this->verify();
            $data = file_get_contents('php://input');
            $data = json_decode($data, 1);
            // 更新楼宇信息
            Building::updateBuilding($data);
        } catch (\Exception $e) {
            AgentsApi::returnData(1, '该楼宇已存在', $e->getMessage());
        }

    }
    /**
     * 根据设备类型和分公司获取指定数量的设备（代理商下单支付成功后点击分配设备获取设备列表）
     * @author  zgw
     * @version 2016-11-14
     * @return  json
     */
    public function actionGetEquipList()
    {
        $this->verify();
        // 获取设备类型和购买数量
        $data = file_get_contents('php://input');
        $data = json_decode($data, 1);

        // $data = ['equip_type_id' => 3, 'buy_num' => 2, 'org_id' => 2];
        if (!$data || !$data['equip_type_id'] || !$data['buy_num'] || !$data['org_id']) {
            AgentsApi::returnData(1, '缺少参数');
        }
        $data['where'] = ['org_id' => $data["org_id"]];
        $orgList       = Api::getOrgInfoListOne($data);
        if (!$orgList) {
            AgentsApi::returnData(10, ' 代理商信息错误');
        }
        // 根据设备类型、分公司、购买数量获取设备列表
        $query = Equipments::find()->select('equip_code, factory_code')->limit($data['buy_num']);
        $query->andWhere(['!=', 'factory_code', '']);
        $query->andFilterWhere([
            'org_id'           => $orgList['org_id'],
            'equip_type_id'    => $data['equip_type_id'],
            'is_unbinding'     => Equipments::NOBINDING,
            'build_id'         => 0,
            'operation_status' => Equipments::PRE_SELIVERY,
        ]);
        $equipList = $query->asArray()->all();
        if (count($equipList) < $data['buy_num']) {
            $diffNum = $data['buy_num'] - count($equipList);
            AgentsApi::returnData(0, '库存设备不足，缺少' . $diffNum . '台', $equipList);
        }
        AgentsApi::returnData(0, '', $equipList);
    }

    /**
     * 设备和代理商绑定操作
     * @author  zgw
     * @version 2016-11-14
     * @return  [type]     [description]
     */
    public function actionEquipOrgBind()
    {
        $this->verify();
        $data = file_get_contents('php://input');
        $data = json_decode($data, 1);
        // $data = ['agents_number' => '0401001', 'equip_code' => ['1901400004', '1901400005']];
        if (!$data['organization_id'] || !$data['equip_code']) {
            AgentsApi::returnData(1, '缺少参数');
        }
        // 获取指定代理商信息
        $orgInfo = Organization::getOrgName('org_id', ['org_id' => $data['organization_id'], 'organization_type' => Organization::TYPE_AGENTS]);
        if (!$orgInfo) {
            AgentsApi::returnData(1, '该代理商不存在');
        }
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($data['equip_code'] as $equipCode) {
            // 获取指定设备的信息(库存、未绑定过)
            $equipInfo = Equipments::getEquipBuildDetail('*', ['equip_code' => $equipCode, 'operation_status' => Equipments::PRE_SELIVERY, 'build_id' => 0]);
            if (!$equipInfo) {
                AgentsApi::returnData(1, '该设备不存在');
            }
            // 绑定设备和代理商
            // 代理商id
            $equipInfo->org_id = $orgInfo['org_id'];
            // 代理商仓库id
            $equipInfo->warehouse_id = ScmWarehouse::getField('id', ['organization_id' => $orgInfo['org_id'], 'use' => 1]);
            if (!$equipInfo->warehouse_id) {
                AgentsApi::returnData(1, '代理商仓库不存在');
            }
            // 保存设备信息
            $equipRes = $equipInfo->save();
            if ($equipRes === false) {
                $transaction->rollBack();
                AgentsApi::returnData(1, '分配设备失败');
            } else {
                // 更新智能平台中的设备信息
                $coffeeEquipRes = Equipments::syncEquip($equipInfo);
                if (!$coffeeEquipRes) {
                    $transaction->rollBack();
                    AgentsApi::returnData(1, '同步到智能平台失败');
                }
            }
        }
        $transaction->commit();
        AgentsApi::returnData(0, '同步成功');
    }
    /**
     * 根据设备类型id获取产品组（包含产品详细信息）
     * @author  zgw
     * @version 2016-11-14
     * @return  [type]     [description]
     */
    public function actionGetProductList()
    {
        // 加密验证
        $this->verify();
        $productGroupArr = [];
        // 获取设备类型id
        $equipTypeId = Yii::$app->request->get('equip_type_id');
        // 获取所有产品组
        $productGroupList = json_decode(Api::getGroups($equipTypeId, 2), 1);
        // 获取所有产品信息
        $productList = json_decode(Api::getProducts('', 2), 1);
        if ($productGroupList && $productList) {
            foreach ($productGroupList as $equipTypeId => $groupArr) {
                if (!is_array($groupArr)) {
                    continue;
                }
                foreach ($groupArr as $groupId => $groupName) {
                    $productGroupArr[$equipTypeId][$groupId] = [
                        'product_group_id'   => $groupId,
                        'product_group_name' => $groupName,
                        'productList'        => isset($productList[$groupId]) ? $productList[$groupId] : '',
                    ];
                }
            }
            AgentsApi::returnData(0, '', $productGroupArr);
        } else {
            AgentsApi::returnData(1, '获取产品组列表失败');
        }
    }

    /**
     * 解锁锁定接口
     * @author  zgw
     * @version 2016-11-15
     * @return  [type]     [description]
     */
    public function actionEquipIsBind()
    {
        $this->verify();
        $data = Yii::$app->request->get();
        if (!$data['equip_code']) {
            AgentsApi::returnData(1, '参数传输错误');
        }

        if ($data['is_lock'] == Equipments::UNLOCK) {
            //解锁
            Equipments::unBindEquip($data);
        } else if ($data['is_lock'] == Equipments::LOCKED) {
            //锁定
            Equipments::bindEquip($data);
        } else {
            AgentsApi::returnData(1, '状态类型错误');
        }
    }

    /**
     * 获取上下线值接口
     * @author  zgw
     * @version 2016-11-14
     * @return  [type]     [description]
     */
    public function actionGetMaterialLimit()
    {
        // 加密验证
        $this->verify();
        // 获取物料上下线值
        $materialLimitArr = json_decode(Api::stockLimit(), true);
        // 处理获取的数据
        if ($materialLimitArr) {
            AgentsApi::returnData(0, '', $materialLimitArr);
        } else {
            AgentsApi::returnData(1, '获取物料上下线值失败');
        }
    }

    /**
     * 代理商自投(绑定)
     * @author zmy
     * @version 2016-11-14
     */
    public function actionAgentsOwnDelivery()
    {
        // $data = '{"build_code":"23","equip_code":"010100002","operate_status":0,"product_group_id":1, 'equip_operation_time':1483408502}';
        $this->verify();
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if (!$data['equip_code'] || !$data['build_code'] || !isset($data['operate_status']) || !$data['product_group_id']) {
            AgentsApi::returnData(1, '参数传输错误');
        }
        Equipments::updateEquipInfo($data);
    }

    /**
     * 代理商设备解绑
     * @author  zgw
     * @version 2016-11-15
     * @return  [type]     [description]
     */
    public function actionUnbind()
    {
        try {
            // $data = '{"equip_code":"010100002", "organization_id":"6", "type":3}';
            $this->verify();
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            // 获取分公司id
            $orgId = Organization::getField('org_id', ['org_id' => $data['organization_id']]);
            if ($data['type'] == 3) {
                // 获取设备id
                $equipObj = Equipments::getEquipBuildDetail('id, build_id', ['equip_code' => $data['equip_code']]);
                if (!$equipObj) {
                    AgentsApi::returnData(1, '设备不存在');
                }
                // 获取分库
                $warehouseId = ScmWarehouse::getField('id', ['organization_id' => $orgId, 'use' => 1]);
                if (!$warehouseId) {
                    AgentsApi::returnData(1, '该分公司分库不存在');
                }
                if (!$equipObj->build_id) {
                    AgentsApi::returnData(1, '设备已解绑或者该设备未进行绑定');
                }
                // 将设备和楼宇进行解绑
                $unbindRes = Equipments::getUnBind($equipObj->id, $warehouseId);
                if (!$unbindRes) {
                    AgentsApi::returnData(1, '解绑失败');
                }
                AgentsApi::returnData();
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                // 获取设备信息
                $equipInfo = Equipments::getEquipBuildDetail('*', ['equip_code' => $data['equip_code']]);
                if ($equipInfo && $equipInfo->org_id != $orgId) {
                    // 修改设备所属代理商或者分公司
                    $equipInfo->org_id = $orgId;
                    if ($equipInfo->save()) {
                        // 修改设备所属楼宇
                        if ($equipInfo->build) {
                            $equipInfo->build->org_id = $orgId;
                            if ($equipInfo->build->save() !== false) {
                                $transaction->commit();
                                AgentsApi::returnData();
                            }
                        }
                    }
                }
                $transaction->rollBack();
                AgentsApi::returnData(1, '解绑失败');
            }
        } catch (\Exception $e) {
            AgentsApi::returnData(1, '接口异常', $e->getMessage());
        }
    }

    /**
     * 获取楼宇二维码
     * @author  zgw
     * @version 2016-11-21
     * @return  [type]     [description]
     */
    public function actionEquipCode()
    {
        $this->verify();
        $equipCode    = Yii::$app->request->get('equip_code');
        $equipTwocode = Api::buildCode($equipCode);
        if (!$equipTwocode) {
            AgentsApi::returnData(1, '获取二维码失败');
        } else {
            AgentsApi::returnData(0, '', ['equip_twocode' => $equipTwocode]);
        }

    }

    /**
     *  获取设备信息
     * @author sulingling
     * @dateTime 2018-08-29
     * @version  [version]
     * @return   [type]     [description]
     */
    public function actionBuildingIndex()
    {
        $this->verify();
        $data         = file_get_contents('php://input');
        $data         = json_decode($data, true);
        $buildingInfo = BuildingSearch::searchAgentsBuild($data);
        return AgentsApi::returnInfo($buildingInfo);
    }

    /**
     *  根据楼宇ID得到详细信息
     * @author sulingling
     * @dateTime 2018-08-31
     * @version  [version]
     * @return   string()     [详细信息]
     */
    public function actionGetBuildingView()
    {
        $this->verify();
        $data           = file_get_contents('php://input');
        $data           = json_decode($data, true);
        $getBuildDetail = Building::getBuildDetail($data);
        return AgentsApi::returnInfo($getBuildDetail);
    }
}
