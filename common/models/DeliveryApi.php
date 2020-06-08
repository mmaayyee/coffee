<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 设备类型参数 接口类
 */
class DeliveryApi extends \yii\db\ActiveRecord
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
     * 接单端 待接单列表
     * @author  wbq
     * @version 2018-8-10
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliWaitList($params = [])
    {
        $takeOutDeliWaitList = self::postBase("delivery-api/get-deli-wait-list", $params);
        if (!$takeOutDeliWaitList) {
            return [];
        }
        return Json::decode($takeOutDeliWaitList);
    }

    /**
     * 接单端 已接单列表
     * @author  wbq
     * @version 2018-8-10
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliList($params = [])
    {
        $takeOutDeliList = self::postBase("delivery-api/get-deli-list", $params);
        if (!$takeOutDeliList) {
            return [];
        }
        return Json::decode($takeOutDeliList);
    }

    /**
     * 接单端 已完成列表
     * @author  wbq
     * @version 2018-8-10
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliCompleteList($params = [])
    {
        $takeOutDeliList = self::postBase("delivery-api/get-deli-complete-list", $params);
        if (!$takeOutDeliList) {
            return [];
        }
        return Json::decode($takeOutDeliList);
    }

    /**
     * 根据用户信息获取待接单,已接单,已完成数量
     * @author  wbq
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryOrderCount($params = [])
    {
        $deliveryCount = self::postBase("delivery-api/get-count-list", $params);
        if (!$deliveryCount) {
            return [];
        }
        return Json::decode($deliveryCount);
    }

    /**
     * 执行接单操作
     * @author  wbq
     * @version 2018-8-13
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function acceptDeli($params = [])
    {
        $takeOutDeliData = self::postBase("delivery-api/accept-deli", $params);
        if (!$takeOutDeliData) {
            return [];
        }
        return Json::decode($takeOutDeliData);
    }

    /**
     * 获取已接单详情页面
     * @author  wbq
     * @version 2018-8-13
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliDetail($params = [])
    {
        $takeOutDeliData = self::postBase("delivery-api/get-deli-detail", $params);
        if (!$takeOutDeliData) {
            return [];
        }
        return Json::decode($takeOutDeliData);
    }

    /**
     * 制作完成
     * @author  wbq
     * @version 2018-8-14
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function makeCompleteDeli($params = [])
    {
        $completeStatus = self::postBase("delivery-api/make-complete-deli", $params);
        if (!$completeStatus) {
            return [];
        }
        return Json::decode($completeStatus);
    }

    /**
     * 确认送达
     * @author  wbq
     * @version 2018-10-16
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function completeDeliveryOrder($params = [])
    {
        $completeStatus = self::postBase("delivery-api/complete-delivery-order", $params);
        if (!$completeStatus) {
            return [];
        }
        return Json::decode($completeStatus);
    }

    /**
     * 修改预计送达时间
     * @author  jiangfeng
     * @version 2018-10-24
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function saveExpectTime($params = [])
    {
        $completeStatus = self::postBase("delivery-api/save-expect-time", $params);
        if (!$completeStatus) {
            return [];
        }
        return Json::decode($completeStatus);
    }
    /***erp后台操作***/
    /**
     * 获取外卖订单列表 分页搜索
     * @author  wbq
     * @version 2018-9-3
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryOrderList($params = [])
    {
        $deliveryList = self::postBase("delivery-api/get-delivery-order-list", $params);
        if (!$deliveryList) {
            return [];
        }
        return Json::decode($deliveryList);
    }

    /**
     * 获取外卖订单
     * @author  wbq
     * @version 2018-10-10
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryOrder($params = [])
    {
        $deliveryOrder = self::postBase("delivery-api/get-delivery-order", $params);
        if (!$deliveryOrder) {
            return [];
        }
        return Json::decode($deliveryOrder);
    }

    /**
     * 获取取消原因列表
     * @author  wbq
     * @version 2018-9-3
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getFailReasonList($params = [])
    {
        $resonList = self::postBase("delivery-api/get-fail-reason-list", $params);
        if (!$resonList) {
            return [];
        }
        return Json::decode($resonList);
    }

    /**
     * 客服取消外卖订单
     * @author  wbq
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function cancelDeliveryOrderByCustom($params = [])
    {
        $saveResult = self::postBase("delivery-api/cancel-delivery-order-by-custom", $params);
        if (!$saveResult) {
            return [];
        }
        return Json::decode($saveResult);
    }

    /**
     * 获取配送人员列表
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryPersonList($params = [])
    {
        $personList = self::postBase("delivery-api/get-delivery-person-list", $params);
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }

    /**
     * 获取配送人员列表
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryRegionList($params = [])
    {
        $personList = self::postBase("delivery-api/region-list", $params);
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }

    /**
     * 新增/修改配送人员
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function updateDeliveryPerson($params = [])
    {
        $saveResult = self::postBase("delivery-api/update-delivery-person", $params);
        if (!$saveResult) {
            return [];
        }
        return Json::decode($saveResult);
    }

    /**
     * 获取配送人员
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getDeliveryPerson($params = [])
    {
        $person = self::postBase("delivery-api/get-delivery-person", $params);
        if (!$person) {
            return [];
        }
        return Json::decode($person);
    }

    /**
     * 删除配送人员
     * @version 2018-9-4
     * @param   array $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function delDeliveryPerson($params = [])
    {
        $saveResult = self::postBase("delivery-api/del-delivery-person", $params);
        if (!$saveResult) {
            return [];
        }
        return Json::decode($saveResult);
    }

    /**
     * 获取当日不同转态的订单数
     * @author zhenggangwei
     * @date   2018-10-12
     * @return array     当日不同状态的订单数
     */
    public static function getOrderCount()
    {
        $personList = self::getBase("delivery-api/get-order-count");
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }

    /**
     * getPersonByRegion 获取区域配送人员
     * @author  jiangfeng
     * @version 2018/11/20
     * @param $params
     * @return array|mixed
     */
    public static function getPersonByRegion($params)
    {
        $personList = self::postBase("delivery-api/get-person-by-region", $params);
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }

    /**
     * switchDeliveryOrder 转移订单
     * @author  jiangfeng
     * @version 2018/11/20
     * @param $params
     * @return array|mixed
     */
    public static function switchDeliveryOrder($params)
    {
        $personList = self::postBase("delivery-api/switch-delivery-order", $params);
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }

    /**
     * switchDeliveryOrder 修改区域状态
     * @author  jiangfeng
     * @version 2018/11/20
     * @param $params
     * @return array|mixed
     */
    public static function deliveryRegionChange($params)
    {
        $personList = self::postBase("delivery-api/delivery-region-change", $params);
        if (!$personList) {
            return [];
        }
        return Json::decode($personList);
    }
}
