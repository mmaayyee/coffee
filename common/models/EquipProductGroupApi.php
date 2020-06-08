<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class EquipProductGroupApi extends \yii\db\ActiveRecord
{
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-09-05
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params . Json::encode($data);die;
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
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);
    }

    /**
     * 获取咖啡单品数据列表 index 列表
     * @author  zmy
     * @version 2017-09-06
     * @param   array      $proGroupParams [单品查询]
     * @return  [string]                   [json数组]
     */
    public static function getProductGroupList($proGroupParams = [])
    {
        $page         = isset($proGroupParams['page']) ? $proGroupParams['page'] : 0;
        $proGroupList = self::postBase("equip-product-group-api/index", $proGroupParams, '&page=' . $page);
        return !$proGroupList ? [] : Json::decode($proGroupList);
    }

    /**
     * 获取工序数据列表 index 列表
     * @author  zmy
     * @version 2017-09-06
     * @param   array      $processParams [传输的数组]
     * @return  [string]                  [json数组]
     */
    public static function getEquipProcessList($processParams = [])
    {
        $page         = isset($processParams['page']) ? $processParams['page'] : 0;
        $proGroupList = self::postBase("equip-product-group-api/equip-process-index", $processParams, '&page=' . $page);
        return !$proGroupList ? [] : Json::decode($proGroupList);
    }

    /**
     *  通过工序ID，查询工序信息接口
     * @author  zmy
     * @version 2017-09-05
     * @param   [string]     $ID [进度条ID]
     * @return  [string]         [进度条json数据]
     */
    public static function getEquipProcessById($ID)
    {
        $cofProInfo = self::getBase("equip-product-group-api/get-equip-process-by-id", "&id=" . $ID);
        return !$cofProInfo ? [] : Json::decode($cofProInfo);
    }

    /**
     * save 设备工序
     * @author  zmy
     * @version 2017-09-06
     * @param   [Array]     $processData  [工序添加参数]
     * @return  [boolen]                  [true/false]
     */
    public static function saveEquipProcess($processData)
    {
        return self::postBase("equip-product-group-api/save-equip-process", $processData);
    }

    /**
     * 根据ID 删除设备工序
     * @author  zmy
     * @version 2017-09-06
     * @param   [string]     $id [设备工序ID]
     * @return  [boolen]         [true/false]
     */
    public static function deleteEquipProcess($id)
    {
        return self::getBase("equip-product-group-api/delte-equip-process", "&id=" . $id);
    }

    /**
     * 获取进度条数据列表 index 列表
     * @author  zmy
     * @version 2017-09-08
     * @param   array      $progressAssocParams [查询参数]
     * @return  [string]                        [json数据]
     */
    public static function getEquipTypeProgressAssocList($progressAssocParams = [])
    {
        $page              = isset($progressAssocParams['page']) ? $progressAssocParams['page'] : 0;
        $progressAssocList = self::postBase("equip-product-group-api/progress-assoc-index", $progressAssocParams, '&page=' . $page);
        return !$progressAssocList ? [] : Json::decode($progressAssocList);
    }

    /**
     * 获取已添加的进度条单品
     * @author  zmy
     * @version 2017-10-25
     * @return  [type]     [description]
     */
    public static function getProgressProductList()
    {
        $progressAssocProductIdList = self::postBase("equip-product-group-api/get-progress-product-list");
        return !$progressAssocProductIdList ? [] : Json::decode($progressAssocProductIdList);
    }

    /**
     * 产品组和特价排期查询楼宇 获取所有符合条件的楼宇
     * @author  zmy
     * @version 2017-10-20
     * @param   [Array]     $data [查询条件]
     * @return  [Array]           [符合条件的楼宇列表]
     */
    public static function getAllBuildingInProduct($data)
    {
        return self::postBase("equip-product-group-api/get-all-building-in-product", $data);
    }

    /**
     * 进度条进行添加、修改
     * @author  zmy
     * @version 2017-09-08
     * @param   [Array]     $progressData [进度条数组]
     * @return  [boolen]                  [true/false]
     */
    public static function saveEquipProgressBar($progressData)
    {
        return self::postBase("equip-product-group-api/save-equip-progress-bar", $progressData);
    }

    /**
     * 根据单品ID删除进度条
     * @author  zmy
     * @version 2017-09-08
     * @param   [string]     $productID [单品ID]
     * @return  [booble]                [true/false]
     */
    public static function deleteEquipProcessBarByProId($productID)
    {
        return self::getBase("equip-product-group-api/delete-equip-process-bar-by-pro-id", "&productId=" . $productID);
    }

    /**
     * 通过单品ID，查询进度条信息，详情页进行展示。
     * @author  zmy
     * @version 2017-09-08
     * @param   [string]     $productID [单品ID]
     * @return  [string]                [json]
     */
    public static function getEquipProgressProViewById($productID)
    {
        $equipProgressInfo = self::getBase("equip-product-group-api/get-equip-progress-pro-view-by-id", "&productId=" . $productID);
        return !$equipProgressInfo ? [] : Json::decode($equipProgressInfo);
    }

    /**
     * 通过单品ID，查询进度条信息，修改时，进行展示。
     * @author  zmy
     * @version 2017-09-08
     * @param   [string]     $productID [单品ID]
     * @return  [string]                [json]
     */
    public static function getEquipProgressProUpdateById($productID)
    {
        $equipProgressInfo = self::getBase("equip-product-group-api/get-equip-progress-update-by-id", "&productId=" . $productID);
        return !$equipProgressInfo ? [] : Json::decode($equipProgressInfo);
    }

    /**
     * 获取单品ID=》name数组
     * @author  zmy
     * @version 2017-09-11
     * @param   integer    $isChoose [是否有请选择]
     * @param   integer    $online   [是否下架产品]
     * @return  [string]             [json字符串]
     */
    public static function getProductList($isChoose = 1, $online = 0)
    {
        $productList = self::getBase("equip-product-group-api/get-product-list", "&isChoose=" . $isChoose, '&online=' . $online);
        return !$productList ? [] : Json::decode($productList);
    }

    /**
     * 获取所有单品name数组
     * @author  tuqiang
     * @version 2017-10-18
     * @return  array      name数组
     */
    public static function getProductNameList()
    {
        $productList = self::getBase("equip-product-group-api/get-product-name-list");
        return !$productList ? [] : Json::decode($productList);
    }

    /**
     * 获取设备工序ID=》name
     * @author  zmy
     * @version 2017-09-11
     * @param   integer    $isChoose [是否有请选择，1-请选择，2-无 请选择]
     * @return  [type]               [description]
     */
    public static function getProcessCorrespondNameList($isChoose = 1)
    {
        $equipProcessList = self::getBase("equip-product-group-api/get-process-correspond-name-list", "&isChoose=" . $isChoose);
        return !$equipProcessList ? [] : Json::decode($equipProcessList);
    }

    /**
     * 获取设备工序name列表
     * @author  tuqiang
     * @version 2017-10-18
     * @param   integer    $isChoose [是否有请选择，1-请选择，2-无 请选择]
     * @return  [type]               [description]
     */
    public static function getProgressNameList()
    {
        $equipProcessList = self::getBase("equip-product-group-api/get-progress-name-list");
        return !$equipProcessList ? [] : Json::decode($equipProcessList);
    }

    /**
     * 获取设备类型下的工序
     * @author  zmy
     * @version 2017-09-11
     * @return  [string]     [json]
     */
    public static function getEquipTypeProcessList()
    {
        $equipTypeProcess = self::getBase("equip-product-group-api/get-equip-type-process-list");
        return !$equipTypeProcess ? [] : Json::decode($equipTypeProcess);
    }

    /**
     * 通过单品Id，返回前端 进度条 数据格式 （添加 productId = 0、修改 productId = 1）
     * @author  zmy
     * @version 2017-09-21
     * @param   string     $productId [单品ID]
     * @return  [string]                [json]
     */
    public static function getEquipTypeProcessListByProductId($productId = '')
    {
        $equipTypeProcess = self::getBase("equip-product-group-api/get-equip-type-process-list-by-product-id", '&productId=' . $productId);
        return !$equipTypeProcess ? [] : Json::decode($equipTypeProcess);
    }

    /**
     * 是否发布产品组接口
     * @author  zmy
     * @version 2017-10-18
     * @param   [string]     $groupId [产品组ID]
     * @return  [string]            [返回单品名称]
     */
    public static function productIsProgress($groupId)
    {
        $isProgress = self::getBase("equip-product-group-api/product-is-progress", '&groupId=' . $groupId);
        return !$isProgress ? [] : Json::decode($isProgress);
    }

    /**
     * 更新版本号接口
     * @author  zmy
     * @version 2017-10-19
     * @param   [string]     $groupId [产品组ID]
     * @return  [boolen]              [true/false]
     */
    public static function updateReleaseVersion($groupId)
    {
        $updateReleaseVersion = self::getBase("equip-product-group-api/update-release-version", '&groupId=' . $groupId);
        return !$updateReleaseVersion ? [] : Json::decode($updateReleaseVersion);
    }

    /**
     * 根据产品组料仓信息ID，查询设备类型
     * @author  zmy
     * @version 2017-10-18
     * @param   [type]     $stockInfoId [产品组料仓信息ID]
     * @return  [type]                  [设备类型]
     */
    public static function getEquipTypeByStockInfoId($stockInfoId)
    {
        $equiptype = self::getBase("equip-product-group-api/get-equip-type-by-stock-info-id", '&stockInfoId=' . $stockInfoId);
        return !$equiptype ? [] : Json::decode($equiptype);
    }

    /**
     * 通过产品组料仓ID，查询产品组料仓信息
     * @author  zmy
     * @version 2017-10-27
     * @param   [string]     $stockInfoId [产品组料仓信息ID]
     * @return  [Array]                           [产品组料仓信息数组 ]
     */
    public static function getProGroupStockInfoByStockId($stockInfoId)
    {
        $proGroupStockInfoList = self::getBase("equip-product-group-api/get-pro-group-stock-info-by-stock-id", '&stockInfoId=' . $stockInfoId);
        return !$proGroupStockInfoList ? [] : Json::decode($proGroupStockInfoList);
    }

    /**
     * 获取产品组模块的添加修改的模板数据
     * @author  zmy
     * @version 2017-09-23
     * @param   [string]     $equipGroupId [产品组ID]
     * @return  [Array]                    [产品组数组]
     */
    public static function getEquipGroupTemplate($equipGroupId)
    {
        $equipGroupInfo = self::getBase("equip-product-group-api/get-equip-group-template", '&equipGroupId=' . $equipGroupId);
        return !$equipGroupInfo ? [] : Json::decode($equipGroupInfo);
    }

    /**
     * 获取产品组料仓信息ID=》name数组
     * @author  zmy
     * @version 2017-10-10
     * @return  [type]     [description]
     */
    public static function getGroupStockIdAndName()
    {
        $groupStockIdAndName = self::getBase("equip-product-group-api/get-group-stock-id-and-name");
        return !$groupStockIdAndName ? [] : Json::decode($groupStockIdAndName);
    }

    /**
     * 通过产品组ID，查询出产品组信息
     * @author  zmy
     * @version 2017-09-13
     * @param   [string]     $proGroupId [产品组ID]
     * @return  [json]                   [json]
     */
    public static function getEquipProductGroupById($proGroupId)
    {
        $equipProGroup = self::getBase("equip-product-group-api/get-equip-prouct-group-by-id", "&proGroupId=" . $proGroupId);
        return !$equipProGroup ? [] : Json::decode($equipProGroup);
    }

    /**
     * 获取产品组料仓下的所有单品(添加产品组时，使用)
     * @author  zmy
     * @version 2017-09-14
     * @param   [string]     $proGroupStockId [产品组料仓ID]
     * @param   [string]     $productGroupId  [产品组ID]
     * @return  [string]                      [产品组料仓下的单品信息]
     */
    public static function getProGroupStockById($proGroupStockId, $productGroupId)
    {
        $proGroupStockCoffeeList = self::getBase("equip-product-group-api/get-pro-group-stock-by-id", "&proGroupStockId=" . $proGroupStockId . '&productGroupId=' . $productGroupId);
        return !$proGroupStockCoffeeList ? [] : Json::decode($proGroupStockCoffeeList);
    }

    /**
     * 根据条件查询符合条件的楼宇
     * @author  zmy
     * @version 2017-09-14
     * @param   [string]   $proGroupId [产品组ID]
     * @param   [type]     $param      [传输查询条件]
     * @param   [string]   $data       [页数]
     * @param   [string]   $pageSize   [分页大小]
     * @return  [string]               [json楼宇数据]
     */
    public static function getSearchBuildByWhere($proGroupId, $data, $page = '1', $pageSize = "20")
    {
        $buildList = self::getBase("equip-product-group-api/get-search-build-by-where", "&proGroupId=" . $proGroupId . "&data=" . json_encode($data, JSON_UNESCAPED_UNICODE) . '&pageSize=' . $pageSize . "&page=" . $page);
        if ($buildList) {
            return Json::decode($buildList);
        }
        return [];
    }

    /**
     * 添加、修改 特价排期 Api
     * @author  zmy
     * @version 2017-09-15
     * @param   [Array]     $specialSchedulParam [添加的特价排期数组]
     * @return  [true/false]                    [true/false]
     */
    public static function saveSpecialSchedul($specialSchedulParam)
    {
        return self::postBase("equip-product-group-api/save-special-schedul", $specialSchedulParam);
    }

    /**
     * 根据特价排期ID，删除特价 排期数据
     * @author  zmy
     * @version 2017-09-15
     * @param   [Array]     $specialSchedulID [特价排期ID]
     * @return  [boolen]                       [true/false]
     */
    public static function deleteSpecialSchedul($specialSchedulID)
    {
        return self::getBase("equip-product-group-api/delete-special-schedul", "&id=" . $specialSchedulID);
    }

    /**
     * 通过活动排期ID,查询出所有符合的活动排期数据，活动单品，及 相关楼宇
     * @author  zmy
     * @version 2017-09-18
     * @param   [string]     $specialSchedulID [特价排期ID]
     * @param   [string]     $[isUpdate]       [是否更新]
     * @return  [string]                       [json数据]
     */
    public static function getSpecialSchedulInfo($specialSchedulID = '', $isUpdate = '')
    {
        $specialSchedulInfo = self::getBase("equip-product-group-api/get-special-schedul-info", "&id=" . $specialSchedulID . '&isUpdate=' . $isUpdate);
        return !$specialSchedulInfo ? [] : Json::decode($specialSchedulInfo);
    }

    /**
     * 获取特价排期单品数据
     * @author  tuqinag
     * @version 2017-10-18
     * @param   $id 单品id
     * @return  array       特价单品数据列表
     */
    public static function getSpecialSchedulProductListByID($specialSchedulID = 0)
    {
        $specialSchedulList = self::getBase("equip-product-group-api/get-special-schedul-product-list-by-id", "&id=" . $specialSchedulID);
        return !$specialSchedulList ? [] : Json::decode($specialSchedulList);
    }
    /**
     * 获取特价排期楼宇数据
     * @author  tuqiang
     * @version 2017-10-18
     * @param   $id 单品id
     * @return  array       特价单品数据列表
     */
    public static function getSpecialSchedulEquipAssoc($specialSchedulID = 0)
    {
        $specialSchedulList = self::getBase("equip-product-group-api/get-special-schedul-equip-assoc", "&id=" . $specialSchedulID);
        return !$specialSchedulList ? [] : Json::decode($specialSchedulList);
    }

    /**
     * 获取特价排期所有数据列表
     * @author  zmy
     * @version 2017-09-18
     * @param   [Array]     $specialSchedulParams [查询的条件]
     * @return  [String]                          [json结果]
     */
    public static function getSpecialSchedulList($specialSchedulParams)
    {
        $page               = isset($specialSchedulParams['page']) ? $specialSchedulParams['page'] : 0;
        $specialSchedulList = self::postBase("equip-product-group-api/get-special-schedul-list", $specialSchedulParams, '&page=' . $page);
        return !$specialSchedulList ? [] : Json::decode($specialSchedulList);
    }

    /**
     * 根据条件查询出楼宇，
     * @author  zmy
     * @version 2017-09-18
     * @param   string     $specialSchedulId [特价活动ID]
     * @param   array      $data             [条件数组]
     * @param   integer    $selectType       [查询类型，0-添加，1-修改]
     * @param   integer    $page             [分页数]
     * @param   integer    $pageSize         [分页大小]
     * @return  [string]                     [json 楼宇数据]
     */
    public static function getSpecialSchedulBuildList($data = [])
    {
        $page      = empty($data['page']) ? 0 : $data['page'];
        $buildList = self::postBase("equip-product-group-api/get-special-schedul-build", $data, '&page=' . $page);
        return !$buildList ? [] : Json::decode($buildList);
    }
    /**
     * 根据条件查询进度条信息
     * @author  tuqiang
     * @version 2017-10-17
     * @param   $id         工序id
     * @return  array       进度条数组
     */
    public static function getEquipTypeProgressProductAssocByWhere($data = [])
    {
        $equipTypeProductList = self::postBase("equip-product-group-api/equip-type-progress-product-assoc-by-where", $data);
        return !$equipTypeProductList ? [] : Json::decode($equipTypeProductList);
    }

    /**
     * 根据产品组id获取楼宇信息
     * @author  zgw
     * @version 2017-10-20
     * @param   integer    $groupID 产品组id
     * @return  array              楼宇信息
     */
    public static function getBuildingByGroup($groupID = 0)
    {
        $buildingList = self::getBase("equip-product-group-api/get-building-by-group", "&groupID=" . $groupID);
        return !$buildingList ? [] : Json::decode($buildingList);
    }
    /**
     *
     * 删除产品组信息
     * @author  tuqiang
     * @version 2017-11-14
     * @param   integer    $groupID     产品组id
     * @return  boolean    true/false   成功/失败
     */
    public static function delEquipProductGroupInfo($groupID = 0)
    {
        return self::getBase("equip-product-group-api/del-equip-product-group-info", "&groupID=" . $groupID);
    }

    /**
     * 批量发布产品组 Api
     * @author  sulingling
     * @version 2018-06-28
     * @param   [Array]     $specialSchedulParam [添加的特价排期数组]
     * @return  [true/false]                    [true/false]
     */
    public static function productIsProgressAll($ids)
    {
        return self::postBase("equip-product-group-api/product-is-progress-update", $ids);
    }

    /**
     * 获取所有产品组料仓信息
     * @author zhenggangwei
     * @date   2020-01-09
     * @return array
     */
    public static function getProGroupStockList()
    {
        return Json::decode(self::getBase("equip-product-group-api/pro-group-stock-list"));
    }

}
