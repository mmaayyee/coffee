<?php

namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 楼宇管理列表接口类
 */
class BuildingRecordApi extends \yii\db\ActiveRecord
{
    // 接口加密
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }
    /**
     * post提交数据共用方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [string]     $action [url]
     * @param     array      $data   [提交的数据]
     * @param     string     $params [url后缀的参数]
     * @return    [str]             [json格式的数据]
     */
    private static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;
        // var_dump(Json::encode($data));die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }

    /**
     * get提交数据共用方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [str]     $action [URL]
     * @param     string     $params [URL后缀参数]
     * @return    [json]             [json数据]
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die;
        // echo '<br>';
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params);
    }
    /**
     * 获取楼宇类型列表 ID => name
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @return    [array]     [楼宇类型列表]
     */
    public static function getBuildTypeList()
    {
        $buildingList = self::getBase("building-record-api/get-build-type-list");
        return !$buildingList ? [] : Json::decode($buildingList)['data'];
    }
    /**
     * 获取自己创建的楼宇列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @return    [type]     [description]
     */
    public static function getBuildingRecordList($userID, $orgID)
    {
        return self::getBase("building-record-api/get-building-list-by-self", '&creator_id=' . $userID . '&org_id=' . $orgID);
    }
    /**
     * 搜索楼宇列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @return    [type]     [description]
     */
    public static function getRecordList($searchParams)
    {
        return self::postBase("building-record-api/get-record-list", $searchParams);
    }
    /**
     * 新建或修改楼宇列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-19
     * @param     [json]     $params [需要保存的json串]
     * @return    [string]           [保存成的楼宇记录ID]
     */
    public static function saveBuildingRecord($params)
    {
        return self::postBase("building-record-api/save-building-record", $params);
    }
    /**
     * 修改获取楼宇详情接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-20
     * @param     [int]     $id [楼宇ID]
     * @return    [array]         [楼宇详情]
     */
    public static function updateBuildingRecordInfo($id)
    {
        $buildingList = self::getBase("building-record-api/update-building-record-info", '&record_id=' . $id);
        return Json::decode($buildingList);
    }
    /**
     * 获取查看详情
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-22
     * @param     [type]     $id [description]
     * @return    [type]         [description]
     */
    public static function getBuildingRecordInfo($id)
    {
        return self::getBase("building-record-api/building-record-info", '&record_id=' . $id);
    }
    /**
     *  web端初始化列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-26
     * @param     [int]     $orgID  [分公司ID]
     * @return    [array]            [楼宇列表]
     */
    public static function webGetBuildingRecordList($orgID)
    {
        return self::getBase("building-record-api/web-get-record-list", '&org_id=' . $orgID);
    }
    /**
     * 楼宇初评
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-29
     * @param     [type]     $rateInfo [description]
     * @return    [type]               [description]
     */
    public static function rateBuildingRecord($rateInfo)
    {
        return self::postBase("building-record-api/rate-building-record", $rateInfo);
    }
    /**
     * 楼宇转交接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-04
     * @param     [type]     $transferInfo [description]
     * @return    [type]                   [description]
     */
    public static function transferBuilding($transferInfo)
    {
        return self::postBase("building-record-api/transfer-building", $transferInfo);
    }
    /**
     * web端楼宇列表搜索
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-04
     * @param     [type]     $searchInfo [description]
     * @return    [type]                 [description]
     */
    public static function searchRecord($searchInfo)
    {
        return self::postBase("building-record-api/building-record-list", $searchInfo);
    }
    /**
     * 修改楼宇联系方式
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-07
     * @param     [array]     $contactInfo [联系信息]
     * @return    [json]                  [修改结果]
     */
    public static function updateContactInfo($contactInfo)
    {
        return self::postBase("building-record-api/update-contact-info", $contactInfo);
    }
}
