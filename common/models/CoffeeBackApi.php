<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class CoffeeBackApi extends \yii\db\ActiveRecord
{
    public static function verifyString()
    {
        return ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd');
    }

    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data = [], $params = '', $isprocess = 0, $isSign = 1)
    {
        $sign = '.html';
        if ($isSign == 1) {
            $sign = self::verifyString();
        } else if ($isSign == 2) {
            $sign .= '?sign=' . md5('SYQ7G5WO0X84');
        }
        // echo Yii::$app->params['fcoffeeUrl'] . $action . $sign . $params;
        // echo Json::encode($data);die;
        $res = Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . $sign . $params, Json::encode($data));
        if ($isprocess == 0) {
            return $res;
        }
        $resList = Json::decode($res);
        if ($resList['error_code'] == 0) {
            return empty($resList['data']) ? true : $resList['data'];
        } else {
            return false;
        }
    }

    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  array|int     接口返回的数据
     */
    public static function getBase($action, $params = '', $isprocess = 0, $isSign = 1)
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString() . $params;die;
        $sign = '.html';
        if ($isSign == 1) {
            $sign = self::verifyString();
        } else if ($isSign == 2) {
            $sign .= '?sign=' . md5('SYQ7G5WO0X84');
        }
        $res = Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . $sign . $params);
        if ($isprocess == 0) {
            return $res;
        }
        $resList = Json::decode($res);
        if ($resList['error_code'] == 0) {
            return empty($resList['data']) ? true : $resList['data'];
        } else {
            return false;
        }
    }

    /**
     * 获取符合条件的发券任务列表
     * @author  zgw
     * @version 2017-06-29
     * @param   array     $params 查询条件
     * @return  array             发券任务列表
     */
    public static function getCouponSendTaskList($params = [])
    {
        $taskList = self::postBase("coupon-send-task-api/index", $params, '&page=' . $params['page']);
        return !$taskList ? [] : Json::decode($taskList);
    }

    /**
     * 获取指定任务的详情
     * @author  zgw
     * @version 2017-06-25
     * @return  array      任务详情
     */
    public static function getCouponSendTaskInfo($ID)
    {
        $taskInfo = self::getBase("coupon-send-task-api/get-coupon-send-task-info", "&id=" . $ID);
        return !$taskInfo ? [] : Json::decode($taskInfo);
    }

    /**
     * 保存任务数据
     * @author  zgw
     * @version 2017-08-26
     * @param   array     $data 要保存的数据
     * @return  integer         保存结果 1-成功 0-失败
     */
    public static function saveCoffeeProduct($data)
    {
        return self::postBase("coffee-product-api/save-coffee-product", $data);
    }

    /**
     * 保存任务数据
     * @author  zgw
     * @version 2017-08-26
     * @param   array     $data 要保存的数据
     * @return  integer         保存结果 1-成功 0-失败
     */
    public static function saveCouponSendTask($data)
    {
        return self::postBase("coupon-send-task-api/save-coupon-send-task", $data);
    }

    /**
     * 删除任务数据
     * @author  zgw
     * @version 2017-08-26
     * @param   integet     $ID 任务ID
     * @return  integer         删除结果 1-成功 0-失败
     */
    public static function delCouponSendTask($ID)
    {
        return self::getBase("coupon-send-task-api/del-coupon-send-task", '&id=' . $ID);
    }

    /**
     * 获取机构ID和name对应的数据
     * @author  zgw
     * @version 2017-08-26
     * @param   array     $params  搜索条件
     * @return  array              机构idname列表
     */
    public static function getOrganizationIdName($params = [])
    {
        $organizationIdName = self::postBase("organization-api/get-org-id-name-array", $params);
        return !$organizationIdName ? [] : Json::decode($organizationIdName);
    }

    /**
     * 获取机构详情
     * @author  zgw
     * @version 2017-08-28
     * @param   array     $params 查询条件
     * @param   string    $filed  要获取的字段名称
     * @return  array|string      机构详情或某个字段的值
     */
    public static function getOrganizationDetail($params, $filed = '')
    {
        $organizationInfo = self::postBase("organization-api/get-org-details", $params);
        if ($organizationInfo) {
            $organizationInfo = Json::decode($organizationInfo);
            if ($filed) {
                return isset($organizationInfo[$filed]) ? $organizationInfo[$filed] : '';
            }
            return $organizationInfo;
        }
        return [];
    }

    /**
     * 获取单品详情
     * @author  zgw
     * @version 2017-08-28
     * @param   integer|array      $ID    单品id
     * @return  json                      单品名称数组
     */
    public static function getProductNames($ID)
    {
        $productNames = self::postBase("service/get-product-name-list", $ID);
        return !$productNames ? [] : Json::decode($productNames);
    }

    /**
     * 获取楼宇列表信息
     * @author  zgw
     * @version 2017-08-29
     * @param   array     $data 查询条件
     * @return  array           获取到的楼宇列表
     */
    public static function getBuildList($data)
    {
        $page      = isset($data['page']) ? $data['page'] : 0;
        $buildList = self::postBase("service/get-build-list", $data, '&page=' . $page);
        return !$buildList ? [] : Json::decode($buildList);
    }

    /**
     * 获取黑白名单列表
     * @author  zgw
     * @version 2017-09-05
     * @param   array     $data 查询条件
     * @return  array           获取到黑白名单数据
     */
    public static function getBlackAndWhiteList($data)
    {
        $page              = isset($data['page']) ? $data['page'] : 0;
        $blackAndWhiteList = self::postBase("coupon-send-task-api/get-black-and-white-list", $data, '&page=' . $page);
        return !$blackAndWhiteList ? [] : Json::decode($blackAndWhiteList);
    }

    /**
     * 保存黑白名单数据
     * @author  zgw
     * @version 2017-09-05
     * @param   array     $data  要保存的数据
     * @return  boolen           true-保存成功 false-保存失败
     */
    public static function saveBlackAndWhiteList($data)
    {
        return self::postBase("coupon-send-task-api/save-black-and-white-list", $data);
    }

    /**
     * 移除黑白名单数据
     * @author  zgw
     * @version 2017-09-05
     * @param   array     $data 要移除的数据
     * @return  boolen          TRUE-移除成功 FALSE-移除失败
     */
    public static function deleteBlackAndWhiteList($data)
    {
        return self::postBase("coupon-send-task-api/del-black-and-white-list", $data);
    }

    /**
     * 获取黑白名单详情
     * @author  zgw
     * @version 2017-09-05
     * @param   array     $userID  用户ID
     * @return  array              黑白名单详情
     */
    public static function getBlackAndWhiteListInfo($userID)
    {
        $blackAndWhiteListInfo = self::getBase("coupon-send-task-api/get-black-and-white-list-info", '&userID=' . $userID);
        return !$blackAndWhiteListInfo ? [] : Json::decode($blackAndWhiteListInfo);
    }

    /**
     * 更新黑白名单备注
     * @author  zgw
     * @version 2017-09-05
     * @param   array     $data 需要更新的数据和内容
     * @return  boolen          TRUE-更新成功 FALSE-更新失败
     */
    public static function UpdateBlackAndWhiteListRemark($data)
    {
        return self::postBase("coupon-send-task-api/update-black-and-white-list-remark", $data);
    }

    /**
     * 获取所有符合条件的楼宇列表
     * @author  zgw
     * @version 2017-09-11
     * @param   array     $data 查询条件
     * @return  array           符合条件的楼宇列表
     */
    public static function getAllBuildingByCondition($data)
    {
        return self::postBase("coupon-send-task-api/get-all-building-by-condition", $data);
    }
    /**
     * 删除文件
     * @author  zgw
     * @version 2017-09-18
     * @param   string     $filePath 文件路径
     * @return  integer              1-删除成功 0-删除失败
     */
    public static function unlinkFile($filePath)
    {
        return self::postBase("coupon-send-task-api/unlink-file", $filePath);
    }

    /**
     * 获取点位助手点位列表
     * @author zhenggangwei
     * @date   2020-04-12
     * @param  array     $params 筛选条件
     * @return array
     */
    public static function getPointPostionList($params)
    {
        return self::postBase("erpapi/point-position/get-point-position-list", $params, '', 1, 2);
    }

    /**
     * 获取点位助手点位详情
     * @author zhenggangwei
     * @date   2020-04-12
     * @param  array     $id 点位ID
     * @return array
     */
    public static function getPointPositionInfo($id)
    {
        return self::getBase("erpapi/point-position/get-point-position-info", '&id=' . $id, 1, 2);
    }

    /**
     * 删除点位助手点位
     * @author zhenggangwei
     * @date   2020-04-12
     * @param  array     $id 点位ID
     * @return array
     */
    public static function delPointPostion($id)
    {
        return self::getBase("erpapi/point-position/del-point-position", '&id=' . $id, 1, 2);
    }

}
