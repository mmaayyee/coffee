<?php

namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 楼宇管理列表接口类
 */
class PointEvaluationApi extends \yii\db\ActiveRecord
{

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
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html?' . $params;
        // var_dump(Json::encode($data));die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html?' . $params, Json::encode($data));
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
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html?' . $params;die;
        // echo '<br>';
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html?' . $params);
    }
    /**
     * 根据ID获取点位评分详情
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-11
     * @param     [int]     $pointID [评分详情ID]
     * @return    [array]               [评分详情数组]
     */
    public static function getPointInfoByID($pointID)
    {
        $point = self::getBase("point-evaluation-api/view-point-info", 'point_id=' . $pointID);
        return !$point ? [] : Json::decode($point)['data'];
    }
    /**
     * web初始化点位评分列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-11
     * @return    [type]     [description]
     */
    public static function getPointIndex($orgID, $pointApplicant = '')
    {
        $pointList = self::getBase("point-evaluation-api/web-point-list", '&org_id=' . $orgID . '&point_applicant=' . $pointApplicant);
        return Json::decode($pointList);
    }
    /**
     * web 点位转交接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-13
     * @param     [array]     $transferInfo [转交信息]
     * @return    [json]                   [转交结果]
     */
    public static function transferPoint($transferInfo)
    {
        return self::postBase("point-evaluation-api/transfer-point", $transferInfo);
    }
    /**
     * web 点位评审接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-13
     * @param     [array]     $transferInfo [转交信息]
     * @return    [json]                   [转交结果]
     */
    public static function pointApproval($approvalInfo)
    {
        return self::postBase("point-evaluation-api/point-approval", $approvalInfo);
    }

    /**
     * web 端创建点位评分需要的渠道类型列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @return    [json]     [渠道类型列表]
     */
    public static function getBuildTypeObj()
    {
        return self::getBase("point-evaluation-api/get-build-type-name");
    }

    /**
     * web 端创建点位评分 根据渠道类型和分公司ID 展示的楼宇已经创建的楼宇名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @param     [int]     $buildTypeID [渠道类型ID]
     * @param     [int]     $orgID       [分公司ID]
     * @return    [json]                  [楼宇ID =》楼宇名称+状态]
     */
    public static function getBuildingNameList($buildTypeID, $orgID)
    {
        return self::getBase("point-evaluation-api/get-building-name-list", '&build_type_id=' . $buildTypeID . '&org_id=' . $orgID);
    }

    /**
     * web 端创建点位评分 根据选择的楼宇信息 展示的楼宇的详细信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @param     [int]     $buildRecordID [楼宇ID]
     * @return    [json]                    [楼宇详细信息]
     */
    public static function getCreateBuildRecordInfo($buildRecordID)
    {
        return self::getBase("point-evaluation-api/find-record-info", '&record_id=' . $buildRecordID);
    }

    /**
     * 企业微信创建点位评分 获取本公司楼宇名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @param     [type]     $orgID [分公司ID]
     * @return    [type]            []
     */
    public static function getRecordNameList($orgID)
    {
        // http://mastersdev.work/point-evaluation-api/get-record-name-list.html?org_id=2
        return self::getBase("point-evaluation-api/get-record-name-list", '&org_id=' . $orgID);
    }
    /**
     * 创建点位评分
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @param     [array]     $paramsInfo [点位信息]
     * @return    [json]                    [创建点位信息的结果]
     */
    public static function savePointEvaluation($paramsInfo)
    {
        return self::postBase("point-evaluation-api/save-point-info", $paramsInfo);
    }

    /**
     * web 搜索接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @param     [array]     $searchParams [搜索参数]
     * @return    [json]                   [搜索结果]
     */
    public static function webSearchPointList($searchParams)
    {
        return self::postBase("point-evaluation-api/search-point-list", $searchParams);
    }
    /**
     * web 导出接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @param     [array]     $searchParams [搜索参数]
     * @return    [json]                   [搜索结果]
     */
    public static function webExportPoint($searchParams)
    {
        $pointList = self::postBase("point-evaluation-api/export-point-list", $searchParams);
        return empty($pointList) ? [] : Json::decode($pointList)['data'];
    }
    /**
     *  更新修改接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-21
     * @param     [type]     $pointID [description]
     * @return    [type]              [description]
     */
    public static function getUpdatePointByID($pointID)
    {
        return self::getBase("point-evaluation-api/update-point", 'point_id=' . $pointID);
    }
    /**
     * 企业微信端列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @return
     * @return    [type]     [description]
     */
    public static function weChatPointList($orgID, $pointApplicant)
    {
        return self::getBase("point-evaluation-api/we-chat-point-list", 'org_id=' . $orgID . '&point_applicant=' . $pointApplicant);
    }

    /**
     * 企业微信端搜索
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @return    [type]     [description]
     */
    public static function weChatSearchPointList($searchParams)
    {
        return self::postBase("point-evaluation-api/search-point-list", $searchParams);
    }
}
