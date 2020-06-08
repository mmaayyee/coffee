<?php
namespace common\models;

use common\helpers\multiRequest\MutiRequestHandler;
use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 任务 接口类
 */
class TaskApi extends \yii\db\ActiveRecord
{
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * 生成一个get请求句柄
     * @author wlw
     * @date   2018-09-07
     * @param string $action 请求的方法名
     * @param string $params 请求的参数
     * @param array  $options   curl_setopt_array 接收的参数
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    private static function getRequestHandle($action, $params = '', $options = [])
    {
        $url    = Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;
        $handle = new MutiRequestHandler();
        $handle->setGetHandle($url, $options);

        return $handle;
    }

    /**
     * 生成一个post请求句柄
     * @author wlw
     * @date   2018-09-07
     * @param string $action
     * @param array  $data     post的数据
     * @param string $params   拼接到url后的数据
     * @param array  $options   curl_setopt_array 接收的参数
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    private static function getPostRequestHandle($action, $data = [], $params = '', $options = [])
    {
        $url    = Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;
        $handle = new MutiRequestHandler();
        $handle->setPostHandle($url, json_encode($data), $options);

        return $handle;
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
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString();die();
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
     * 获取 用户筛选 数据列表 index 列表
     * @author  zmy
     * @version 2018-01-04
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getUserSelectionTaskList($params = [])
    {
        $page     = isset($params['page']) ? $params['page'] : 0;
        $taskList = self::postBase("task-api/user-selection-task-index", $params, '&page=' . $page);
        return !$taskList ? [] : Json::decode($taskList);
    }

    /**
     * 根据任务ID，获取用户筛选单条任务信息 （用于修改、查看）
     * @author  zmy
     * @version 2018-01-05
     * @param   array      $data [description]
     * @return  [type]           [description]
     */
    public static function getUserSelectionTaskInfo($selectionTaskId)
    {
        $data = self::getBase("task-api/get-user-selection-task-info", "&selectionTaskId=" . $selectionTaskId);
        if ($data) {
            return Json::decode($data);
        }
        return [];
    }

    /**
     * 页面ajax提交，获取楼宇点位 楼宇数组
     * 通过条件数组，查询出楼宇ID=》name数组
     * @author  zmy
     * @version 2018-01-05
     * @param   [Array]     $conditionList [查询条件数组]
     * @return  [string]                [buildList Json]
     */
    public static function getBuildLevelBuildList($conditionList)
    {
        // $conditionList = [
        //     'city'       => [],
        //     'build_type' => [],
        //     'equip_type' => [],
        // ];
        $buildList = self::postBase("task-api/get-build-level-build-list", $conditionList);
        if ($buildList) {
            return Json::decode($buildList);
        }
        return [];
    }

    /**
     * 检测手机号是否合法
     * @author  zmy
     * @version 2018-01-24
     * @param   [string]     $mobile [手机号]
     * @return  [boolen]             [true/false]
     */
    public static function getMobileDetect($mobile)
    {
        return self::getBase("task-api/get-mobile-detect", "&mobile=" . $mobile);

    }

    /**
     * 检测楼宇是否合法
     * @author  zmy
     * @version 2018-01-24
     * @param   [string]     $build [楼宇]
     * @return  [boolen]            [true/false]
     */
    public static function getBuildDetect($build)
    {
        return self::getBase("task-api/get-build-detect", "&build=" . $build);
    }

    /**
     * 检测公司是否合法
     * @author  zmy
     * @version 2018-01-24
     * @param   [string]     $company [公司]
     * @return  [boolen]              [true/false]
     */
    public static function getCompanyDetect($company)
    {
        return self::getBase("task-api/get-company-detect", "&company=" . $company);
    }

    /**
     * 通过任务ID，获得任务的条件和逻辑关系
     * @author  zmy
     * @version 2018-01-25
     * @param   [string]     $taskId [用户筛选任务ID]
     * @return  [string]             [Json数据]
     */
    public static function getWhereByTaskId($taskId)
    {
        $list = self::getBase("task-api/get-where-by-task-id", "&task_id=" . $taskId);
        return Json::decode($list);
    }

    /**
     * 获取 发券管理 数据列表 index 列表
     * @author  zmy
     * @version 2018-01-04
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function couponSendTaskIndex($params = [])
    {
        $page     = isset($params['page']) ? $params['page'] : 0;
        $taskList = self::postBase("task-api/coupon-send-task-index", $params, '&page=' . $page);
        return !$taskList ? [] : Json::decode($taskList);
    }

    public static function couponSendTaskStatistics($params = [])
    {
        $statistics = self::postBase("task-api/coupon-send-task-statistics", $params);
        return !$statistics ? [] : Json::decode($statistics);
    }

    /**
     * 发券任务详情，通过任务ID
     * @author  zmy
     * @version 2018-01-31
     * @param   [string]     $taskId [发券任务ID]
     * @return  [string]             [json发券详情数据]
     */
    public static function getCouponSendTaskView($taskId)
    {
        $list = self::getBase("task-api/get-coupon-send-task-view", "&task_id=" . $taskId);
        return Json::decode($list);
    }

    /**
     * 审核发券任务管理
     * @author  zmy
     * @version 2018-01-31
     * @param   [Array]     $data [审核的内容和发券ID]
     * @return  [type]           [true/fasle]
     */
    public static function auditCouponSendTask($data)
    {
        $sendTaskList = self::postBase("task-api/audit-coupon-send-task", $data);
        if ($sendTaskList) {
            return true;
        }
        return false;
    }

    /**
     * 获取发券统计详情中：优惠券统计
     * @author  zmy
     * @version 2018-05-15
     * @param   [int]     $taskId [任务id]
     * @return  [array]           [发券优惠券统计数组]
     */
    public static function getCouponStatisticsInfo($taskId)
    {
        $list = self::getBase("task-api/get-coupon-statistics-info", "&task_id=" . $taskId);
        return Json::decode($list);
    }

    /**
     * 获取发券详情统计中的：单品统计
     * @author  zmy
     * @version 2018-05-15
     * @param   [int]     $taskId [任务id]
     * @return  [array]            [单品统计数组]
     */
    public static function getProductStatisticsInfo($taskId)
    {
        $list = self::getBase("task-api/get-product-statistics-info", "&task_id=" . $taskId);
        return Json::decode($list);
    }

    /**
     * 获取需要导出的任务信息数组
     * @author  zmy
     * @version 2018-05-23
     * @param   [int]       $taskId [任务id]
     * @return  [array]             [返回组装的数据]
     */
    public static function getCouponSendTask($taskId)
    {
        $list = self::getBase("task-api/get-coupon-send-task", "&task_id=" . $taskId);
        return Json::decode($list);
    }

    /**
     * 获取发券详情统计中的：单品统计
     * @author  wlw
     * @date    2018-09-07
     *
     * @param int $taskId
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getProductStatisticsInfoMQHandle($taskId)
    {
        return self::getRequestHandle("task-api/get-product-statistics-info", "&task_id=$taskId");
    }

    /**
     * 获取需要导出的任务信息数组
     * @author wlw
     * @date   2018-09-07
     *
     * @param int $taskId
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getCouponSendTaskMQHandle($taskId)
    {
        return self::getRequestHandle("task-api/get-coupon-send-task", "&task_id=$taskId");
    }

    /**
     * 获取优惠券统计信息
     * @author wlw
     * @date   2018-09-07
     * @param int $taskId
     * @return \common\helpers\multiRequest\MutiRequestHandler
     */
    public static function getCouponStatisticsInfoMQHandle($taskId)
    {
        return self::getRequestHandle('task-api/get-coupon-statistics-info', "&task_id=$taskId");
    }

}
