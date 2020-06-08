<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class ActivityApi extends \yii\db\ActiveRecord
{
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  zmy
     * @version 2017-11-21
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die(Json::encode($data));
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params, Json::encode($data));
    }

    /**
     * get提交数据共用方法
     * @author  zmy
     * @version 2017-11-21
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
     * @version 2017-11-21
     * @param   array      $activityParams [活动查询]
     * @return  [string]                   [json数组]
     */
    public static function getNineLotteryList($activityParams = [])
    {
        $page         = isset($activityParams['page']) ? $activityParams['page'] : 0;
        $proGroupList = self::postBase("activity-api/nine-lottery-index", $activityParams, '&page=' . $page);
        return !$proGroupList ? [] : Json::decode($proGroupList);
    }

    /**
     * 页面ajax  请求九宫格活动 联动数据，进行查询接口返回
     * @author  zmy
     * @version 2017-11-25
     * @param   [string]     $awardsNum  [奖项数量]
     * @param   [string]     $activityId [活动ID]
     * @return  [string]                 [json]
     */
    public static function getNineLotteryAwardsSetList($awardsNum, $activityId)
    {
        $linkageData = self::getBase("activity-api/get-nine-lottery-awards-set-list", "&activity_id=" . $activityId . "&awards_num=" . $awardsNum);
        return !$linkageData ? [] : Json::decode($linkageData);
    }

    /**
     * 修改时使用，根据活动ID，查询出活动的组合数组
     * @author  zmy
     * @version 2017-11-25
     * @param   [string]     $activityId [活动Id]
     * @return  [Array]                  [组合的活动数组]
     */
    public static function getNineLotteryActivityList($activityId)
    {
        $activityList = self::getBase("activity-api/get-nine-lottery-activity-list", "&activity_id=" . $activityId);
        return !$activityList ? [] : Json::decode($activityList);
    }

    /**
     * 删除活动，根据活动ID
     * @author  zmy
     * @version 2017-11-25
     * @param   [string]     $activityId [活动ID]
     * @return  [boolen]                 [true、false]
     */
    public static function deleteActivity($activityId)
    {
        return self::getBase("activity-api/delete-activity", "&activity_id=" . $activityId);
    }

    /**
     * 获取提示语数据列表 index 列表
     * @author  zmy
     * @version 2017-11-27
     * @param   array      $lotteryWinningHintParams [活动查询]
     * @return  [string]                   [json数组]
     */
    public static function getLotteryWinningHintList($lotteryWinningHintParams = [])
    {
        $page                   = isset($lotteryWinningHintParams['page']) ? $lotteryWinningHintParams['page'] : 0;
        $lotteryWinningHintList = self::postBase("activity-api/lottery-winning-hint-index", $lotteryWinningHintParams, '&page=' . $page);
        return !$lotteryWinningHintList ? [] : Json::decode($lotteryWinningHintList);
    }

    /**
     * 根据ID，查询提示语信息
     * @author  zmy
     * @version 2017-11-28
     * @param   [type]     $hintId [description]
     * @return  [type]             [description]
     */
    public static function getLoteryWinningHintListByHintId($hintId)
    {
        $ret = self::getBase("activity-api/get-lotery-winning-hint-list-by-hint-id", "&hint_id=" . $hintId);
        return $ret ? Json::decode($ret) : "";
    }

    /**
     * 根据ID，删除提示语信息
     * @author  zmy
     * @version 2017-11-28
     * @return  [type]     [description]
     */
    public static function deleteLotteryWinningHintByHintId($hintId)
    {
        return self::getBase("activity-api/delete-lottery-winning-hint-by-hint-id", "&hint_id=" . $hintId);
    }

    /**
     * 中奖记录详情数据
     * @author  zmy
     * @version 2017-11-29
     * @param   [Array]     $activityId [活动Id]
     * @return  [Array]                 [中奖详情数组]
     */
    public static function getLotteryWinningRecordList($activityId)
    {
        $ret = self::getBase("activity-api/get-lottery-winning-record-list", "&activity_id=" . $activityId);
        return $ret ? Json::decode($ret) : "";
    }

    /**
     * 根据活动Id，删除活动
     * @author  zmy
     * @version 2017-12-01
     * @return  [type]     [description]
     */
    public static function checkIsDeleteActivity($activityId)
    {
        $ret = self::getBase("activity-api/check-is-delete-activity", "&activity_id=" . $activityId);
        return $ret ? Json::decode($ret) : "";
    }

    /**
     * 获取抽奖活动中奖数据列表 index 列表
     * @author  zmy
     * @version 2017-11-21
     * @param   array      $activityParams [活动查询]
     * @return  [string]                   [json数组]
     */
    public static function getNineLotteryRecordList($activityParams = [], $activityId)
    {
        $page              = isset($activityParams['page']) ? $activityParams['page'] : 0;
        $lotteryRecordList = self::postBase("activity-api/lottery-winning-record-index", $activityParams, '&page=' . $page . '&activity_id=' . $activityId);
        return !$lotteryRecordList ? [] : Json::decode($lotteryRecordList);
    }

    /**
     * 通过活动记录ID，修改活动记录收货状态
     * @author  zmy
     * @version 2017-12-04
     * @param   [type]     $recordId [活动记录ID]
     * @return  [type]               [true/false]
     */
    public static function updateLotteryActivityShip($recordId)
    {
        $ret = self::getBase("activity-api/update-lottery-activity-ship", "&lottery_record_id=" . $recordId);
        return $ret;
    }

    /**
     * 通过用户ID，查询用户名称
     * @author  zmy
     * @version 2017-12-04
     * @param   [type]     $userId [用户Id]
     * @return  [type]             [用户名称]
     */
    public static function getUserNameById($userId)
    {
        return self::getBase("activity-api/get-user-name-by-id", "&user_id=" . $userId);
    }

    /**
     * 获取活动类型数组
     * @author  zmy
     * @version 2017-12-04
     * @param   [type]     $level [等级]
     * @param   [type]     $type  [类型]
     * @return  [type]            [是否开启过滤]
     */
    public static function getActivityTypeList($level = 0, $type = 2, $isPromptMode = 0)
    {
        $ret = self::getBase("activity-api/get-activity-type-list", "&level=" . $level . '&type=' . $type . '&isPromptMode=' . $isPromptMode);
        return $ret ? Json::decode($ret) : "";
    }

    /**
     * 获取活动ID对应的活动名称
     * @author  zmy
     * @version 2017-12-08
     * @param   [string]   $activityTypeId  [活动类型ID]
     * @param   integer    $type            [是否加请选择]
     * @return  [array]                     [id=>name数组]
     */
    public static function getActivityIdToName($activityTypeId, $type = 2)
    {
        $ret = self::getBase("activity-api/get-activity-id-to-name", "&activityTypeId=" . $activityTypeId . '&type=' . $type);
        return $ret ? Json::decode($ret) : "";
    }

    /*************************************************************************/
    //          自组合套餐活动相关接口

    /**
     * 获取自组合套餐活动中奖数据列表 index 列表 接口
     * @author  zmy
     * @version 2018-03-26
     * @param   array      $activityParams  [活动查询]
     * @return  string                      [json数组]
     */
    public static function getCombinPackageAssocIndex($activityParams = [])
    {
        $page                   = isset($activityParams['page']) ? $activityParams['page'] : 0;
        $combinPackageAssocList = self::postBase("activity-api/combin-package-assoc-index", $activityParams, '&page=' . $page);
        return !$combinPackageAssocList ? [] : Json::decode($combinPackageAssocList);
    }

    // 获取自组合套餐活动 页面渲染数据、详情信息接口
    public static function getCombinPackageAssocView($activityId, $flag = 'edit')
    {
        $ret = self::getBase("activity-api/combin-package-assoc-view", "&activityId=" . $activityId . "&flag=" . $flag);
        return $ret ? Json::decode($ret) : [];
    }

    /**
     * 检测活动名称是否唯一
     * @author  zmy
     * @version 2018-03-28
     * @param   [string]     $activityName [活动名称]
     * @return  [boolen]                   [true/false]
     */
    public static function getActivityNameUnique($activityName)
    {
        $ret = self::getBase("activity-api/combin-package-assoc-view", "&activityId=" . $activityId);
        return $ret ? Json::decode($ret) : [];
    }

    /**
     * 获取自组合套餐活动守护工获取中奖数据列表 index 列表 接口
     * @author  zmy
     * @version 2018-03-26
     * @param   array      $activityParams  [活动查询]
     * @return  string                      [json数组]
     */
    public static function getCombinPackageDeliveryIndex($activityParams = [])
    {
        $page                   = isset($activityParams['page']) ? $activityParams['page'] : 0;
        $combinPackageAssocList = self::postBase("activity-api/combin-package-delivery-index", $activityParams, '&page=' . $page);
        return !$combinPackageAssocList ? [] : Json::decode($combinPackageAssocList);
    }

    /**
     * 修改发货信息 组合套餐活动用户发货表
     * @author  zmy
     * @version 2018-04-08
     * @return  [type]     [description]
     */
    public static function updateDeliverGoods($params)
    {
        $ret = self::postBase("activity-api/update-deliver-goods", $params);
        return $ret ? true : false;
    }

    /**
     * 获取领券活动数据
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  array     $params 查询条件
     * @return array
     */
    public static function getCouponActivityList($params)
    {
        $page         = isset($params['page']) ? $params['page'] : 0;
        $proGroupList = self::postBase("activity-api/coupon-activity-list", $params, '&page=' . $page);
        return !$proGroupList ? [] : Json::decode($proGroupList);
    }

    /**
     * 获取领券活动详情
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  integer     $id 活动ID
     * @return array
     */
    public static function getCouponActivityInfo($id)
    {
        $proGroupInfo = self::getBase("activity-api/coupon-activity-info", "&id=" . $id);
        return !$proGroupInfo ? [] : Json::decode($proGroupInfo);
    }

    /**
     * 编辑领券活动时获取活动详情
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  integer     $id 活动ID
     * @return array
     */
    public static function getCouponActivityByActivityId($id)
    {
        $proGroupInfo = self::getBase("activity-api/coupon-activity-by-activity-id", "&id=" . $id);
        return !$proGroupInfo ? [] : Json::decode($proGroupInfo);
    }

    /**
     * 保存优惠券信息
     * @author zhenggangwei
     * @date   2019-03-05
     * @param  array     $params 要保存的数据
     * @return array
     */
    public static function saveCouponActivity($params)
    {
        return self::postBase("activity-api/save-coupon-activity", $params);
    }
}
