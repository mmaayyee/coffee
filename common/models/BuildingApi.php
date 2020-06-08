<?php

namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class BuildingApi extends \yii\db\ActiveRecord
{
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-09-05
     * @param   string $action 请求的方法名
     * @param   array  $data 发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '')
    {
//        echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;var_dump(Json::encode($data));die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));

    }

    /**
     * get提交数据共用方法
     * @author  zmy
     * @version 2017-09-05
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die;
        // echo '<br>';
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);
    }

    /**
     * @param array $buildingParams
     * 获取楼宇列表  index接口
     * @author jiangfeng
     * @version 2018/10/11
     * @return array|mixed
     */
    public static function getBuildingList($buildingParams = [], $field = '*')
    {
        $page         = isset($buildingParams['page']) ? $buildingParams['page'] : 0;
        $buildingList = self::postBase("building-api/get-build-list", ['where' => $buildingParams, 'field' => $field], '&page=' . $page);
        return !$buildingList ? [] : Json::decode($buildingList);
    }

    /**
     * 根据id获取楼宇信息
     * @author jiangfeng
     * @version 2018/10/13
     * @param int $id
     * @return array|mixed
     */
    public static function getBuildingOne($id = 0)
    {
        $buildingList = self::getBase("building-api/get-building-detail", '&buildId=' . $id);
        return !$buildingList ? [] : Json::decode($buildingList)['data'];
    }

    public static function saveBuilding($params = [])
    {
        $buildingList = self::postBase("building-api/save-build", $params);
        return !$buildingList ? [] : Json::decode($buildingList);
    }

    /**
     * 根据OrgID 分公司获取所有的楼宇列表名称  楼宇ID 对应 楼宇名称
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [楼宇名称列表]
     */
    public static function getbuildIDNameByOrgID($orgID)
    {
        $orgIDList        = empty($orgID) ? '' : Json::encode($orgID);
        $buildingNameList = self::getBase("building-api/get-build-name-list", '&org_id=' . $orgIDList);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 未投放
     * 根据OrgID 分公司获取所有未投放状态的楼宇列表名称  楼宇ID 对应 楼宇名称
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [楼宇名称列表]
     */
    public static function getbuildPreDeliveryByOrgID($orgID)
    {
        $buildingNameList = self::getBase("building-api/get-build-pre-delivery", '&org_id=' . $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 已投放
     * 根据OrgID 分公司获取所有已投放状态 设备编号对应的楼宇名称数组
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [设备编号对应的楼宇名称数组]
     */
    public static function getEquipCodeBuildServedByOrgID($orgID)
    {
        $buildingNameList = self::getBase("building-api/get-org-equip-code-served-build", '&org_id=' . $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取所有的楼宇列表名称 不是 id => name 类型的数组
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [楼宇名称列表]
     */
    public static function getbuildNameList()
    {
        $buildingNameList = self::getBase("building-api/get-name-list");
        return !$buildingNameList ? [] : Json::decode($buildingNameList);
    }

    /**
     * 获取已投放的楼宇名称列表 可以根据分公司获取
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [已投放的楼宇名称列表]
     */
    public static function getServedBuildName($orgID = '')
    {
        $buildingServeNameList = self::getBase("building-api/get-served-build-list", '&org_id=' . $orgID);
        return !$buildingServeNameList ? [] : Json::decode($buildingServeNameList)['data'];
    }

    /**
     * 获取配送员负责的楼宇名称
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [配送员负责的楼宇名称]
     */
    public static function getDistributionByUserID($userName)
    {
        $buildingDistributionNameList = self::getBase("building-api/get-distribution-build-list", '&user_id=' . $userName);
        return empty($buildingDistributionNameList) ? [] : Json::decode($buildingDistributionNameList)['data'];
    }

    /**
     * 修改楼宇的状态
     * @author:GaoYongLi
     * @Date:2018/10/25
     * @param $buildID
     * @return string null or true
     */
    public static function changeBuildStatus($buildinfo = [])
    {
        $building = self::postBase("building-api/change-build-static", $buildinfo);
        return !$building ? [] : Json::decode($building)['data'];
    }

    /**
     * 已投放
     * 根据OrgID 分公司获取所有已投放状态 设备编号对应的楼宇名称数组
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @return    [array]     [设备编号对应的楼宇名称数组]
     */
    public static function getAllBuildIdEquipCodeArr($orgID)
    {
        $buildingNameList = self::getBase("building-api/get-all-build-id-equip-code", '&org_id=' . $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取分公司下商业运营已投放的楼宇
     * @author:GaoYongLi
     * @Date:2018/10/26
     * @param int $orgID 分公司ID
     * @return array 分工下的已投放商业运营的设备的楼宇
     */
    public static function getBusinessBuildByOrgId($orgID)
    {
        $buildingNameList = self::getBase("building-api/get-commercial-build-by-org-id", '&org_id=' . $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取正常运营的设备楼宇 根据分公司ID
     * @author:GaoYongLi
     * @Date  :2018/10/26
     * @param array $orgID 分公司ID列表
     * @return json 正常运营的设备楼宇
     */
    public static function getRunBuildList($orgID)
    {
        $orgIDList        = empty($orgID) ? '' : Json::encode($orgID);
        $buildingNameList = self::getBase("building-api/get-run-build-by-org-id", '&org_id=' . $orgIDList);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     *
     * @author:GaoYongLi
     * @Date:2018/10/27
     * @return array
     */
    public static function updateFirst()
    {
        $buildingNameList = self::getBase("building-api/update-first");
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 删除指定配送员负责的楼宇
     * @author:GaoYongLi
     * @Date:2018/10/27
     * @param string $userID 配送员ID
     * @return int 0 or number
     */
    public static function delDistributionUser($userID)
    {
        $buildingNameList = self::getBase("building-api/del-distribution-user", '&user_id=' . $userID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取当前用户所在分公司已投放和投放中的楼宇列表
     * @author:GaoYongLi
     * @Date:2018/10/27
     * @param $searchData 搜索条件
     * @return array  已投放和投放中的楼宇列表
     */
    public static function getDeliveryBuildList($searchData)
    {
        $buildingList = self::postBase("building-api/get-delivery-build-list", $searchData);
        return !$buildingList ? [] : Json::decode($buildingList)['data'];
    }

    /**
     * 根据条件获取楼宇表指定的字段
     * @author:GaoYongLi
     * @Date:2018/10/27
     * @param  array $searchData 搜索条件
     * @return array  根据条件获取楼宇表指定的字段
     */
    public static function getBuildingFieldValue($searchParams)
    {
        $buildingFeild = self::postBase("building-api/get-building-field-value", $searchParams);
        return !$buildingFeild ? [] : Json::decode($buildingFeild)['data'];
    }

    /**
     * 根据条件获取楼宇表需要查询的字段
     * @author:GaoYongLi
     * @Date:2018/10/27
     * @param  array $searchParams 搜索条件
     * @return array  根据条件获取楼宇表指定的字段
     */
    public static function getBuildingFieldArray($searchParams)
    {
        $buildingFeildArr = self::postBase("building-api/get-building-field-array", $searchParams);
        return !$buildingFeildArr ? [] : Json::decode($buildingFeildArr)['data'];
    }

    /**
     * 分公司下不同楼宇状态的楼宇名称列表
     * @author:GaoYongLi
     * @Date:2018/10/29
     * @param array $searchWhere 楼宇状态的条件和分公司的条件
     * @return array 楼宇名称列表
     */
    public static function getOrgBuildNameList($searchWhere)
    {
        $buildingNameList = self::postBase("building-api/get-org-build-name-list", $searchWhere);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取当前用户所在分公司已投放楼宇的指定设备状态的楼宇名称列表
     * @author:GaoYongLi
     * @Date:2018/10/29
     * @param $orgID
     * @return array
     */
    public static function getEqStatusBuildList($orgID)
    {
        $buildingNameList = self::postBase("building-api/get-eq-status-build-list", $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    /**
     * 获取当前用户所在分公司已投放的楼宇列表
     * @author:GaoYongLi
     * @Date:2018/10/29
     * @param $orgID
     * @return array
     */
    public static function getOperationBuildList($orgID)
    {
        $buildingNameList = self::postBase("building-api/get-operation-build-list", $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }
    /**
     * 上传楼宇故障获取楼宇和设备的相应信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-07
     * @param     [int]     $buildID [楼宇ID]
     * @return    [type]              [结果]
     */
    public static function getEquipTaskByBuildID($buildID)
    {
        $buildingNameList = self::getBase("building-api/get-equip-task-by-build-id", '&build_id=' . $buildID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }

    public static function pageGetBuildingList($page, $orgID)
    {
        $buildingNameList = self::getBase("building-api/page-get-material-build-list", '&page=' . $page . '&org_id=' . $orgID);
        return !$buildingNameList ? [] : Json::decode($buildingNameList)['data'];
    }
    /**
     * 导出点位列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-03
     * @return    [type]     [description]
     */
    public static function exportBuildingList($buildingParams, $field = '*')
    {
        $buildingList = self::postBase("building-api/export-build-list", ['where' => $buildingParams, 'field' => $field]);
        return !$buildingList ? [] : Json::decode($buildingList);
    }

    /**
     * 获取点位开始运营时间
     * @author  wangxiwen
     * @version 2018-12-25
     * @return
     */
    public static function getBuildOperationDate()
    {
        $operationDateStr = self::getBase("building-api/get-build-operation-date");
        return !$operationDateStr ? [] : Json::decode($operationDateStr);
    }

    /**
     * 获取点位BD维护人员数组
     * @author  wangxiwen
     * @version 2018-12-28
     * @return
     */
    public static function getBdMaintenanceUserArray()
    {
        $bdMaintenanceUserStr = self::getBase("building-api/get-bd-maintenance-user-array");
        return !$bdMaintenanceUserStr ? [] : Json::decode($bdMaintenanceUserStr);
    }

    /**
     * 获取指定点位BD维护人员
     * @author  wangxiwen
     * @version 2018-12-28
     * @return
     */
    public static function getBdMaintenanceUser($buildNumber)
    {
        $bdMaintenanceUserStr = self::getBase("building-api/get-bd-maintenance-user", '&buildNumber=' . $buildNumber);
        return !$bdMaintenanceUserStr ? '' : Json::decode($bdMaintenanceUserStr);
    }

    /**
     * 获取点位编号对应的任务列表
     * @author zhenggangwei
     * @date   2020-01-09
     * @return array
     */
    public static function getBuildNumTaskList()
    {
        $buildNumTaskList = self::getBase("building-api/get-build-num-task-list");
        return !$buildNumTaskList ? '' : Json::decode($buildNumTaskList);
    }
}
