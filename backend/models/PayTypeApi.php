<?php
namespace backend\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class PayTypeApi extends \yii\db\ActiveRecord
{
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
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;die;
        // echo Json::encode($data);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }

    /**
     * get提交数据共用方法
     * @author  zmy
     * @version 2017-09-05
     * @return  array|int     接口返回的数据
     */
    private static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }

    /**
     * 获取支付方式列表
     * @author  zgw
     * @version 2018-12-12
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getPayTypeList($params = [])
    {
        $payTypeList = self::postBase("pay-type-api/index", $params);
        return !$payTypeList ? [] : Json::decode($payTypeList);
    }

    /**
     * 根据支付方式ID获取支付方式信息
     * @author  zgw
     * @version 2017-09-05
     * @param   integer     $id
     * @return  array
     */
    public static function getPayTypeInfo($id)
    {
        $payTypeInfo = self::getBase("pay-type-api/view", "&id=" . $ID);
        return !$payTypeInfo ? [] : Json::decode($payTypeInfo);
    }

    /**
     * 获取支付方式优惠策略
     * @author  zgw
     * @version 2018-12-12
     * @return  array
     */
    public static function getPayTypeHolicy()
    {
        $payTypeHolicy = self::getBase("pay-type-api/pay-type-holicy");
        return !$payTypeHolicy ? [] : Json::decode($payTypeHolicy);
    }

    /**
     * 修改支付方式
     * @author  zgw
     * @version 2018-12-12
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function updatePayType($id, $params = [])
    {
        $updateRes = self::postBase("pay-type-api/update", $params, '?id=' . $id);
        return !$updateRes ? [] : Json::decode($updateRes);
    }

    /**
     * 获取支付方式ID对应的名称数组
     * @author zhenggangwei
     * @date   2018-12-13
     * @param  string/integer     $isAll  0-全部 1-支持优惠策略的支付方式
     * @return array
     */
    public static function getPayTypeIdNameList($isAll = 0)
    {
        $payTypeIdNameList = self::getBase("pay-type-api/pay-type-id-name-list", '?isAll=' . $isAll);
        return !$payTypeIdNameList ? [] : Json::decode($payTypeIdNameList);
    }

    /**
     * 根据批次ID获取楼宇支付方式策略
     * @author zhenggangwei
     * @date   2018-12-13
     * @param  integer     $buildPayTypeId  楼宇支付方式ID
     * @return array
     */
    public static function getBuildPayTypeHolicy($buildPayTypeId)
    {
        $buildPayTypeHolicy = self::getBase("pay-type-api/build-pay-type-holicy", '?buildPayTypeId=' . $buildPayTypeId);
        return !$buildPayTypeHolicy ? [] : Json::decode($buildPayTypeHolicy);
    }

    /**
     * 根据批次ID获取楼宇支付方式策略
     * @author zhenggangwei
     * @date   2018-12-13
     * @return array
     */
    public static function getBuildPayTypeList($params)
    {
        $buildPayTypeList = self::postBase("pay-type-api/build-pay-type-list", $params);
        return !$buildPayTypeList ? [] : Json::decode($buildPayTypeList);
    }

    /**
     * 根据批次ID获取楼宇支付方式策略
     * @author zhenggangwei
     * @date   2018-12-13
     * @param  integer     $buildPayTypeId  楼宇支付方式ID
     * @return array
     */
    public static function deleteBuildPayType($buildPayTypeId)
    {
        $buildPayTypeDel = self::getBase("pay-type-api/delete-build-pay-type", '?buildPayTypeId=' . $buildPayTypeId);
        return !$buildPayTypeDel ? [] : Json::decode($buildPayTypeDel);
    }

    /**
     * 获取楼宇支付方式名称列表
     * @author zhenggangwei
     * @date   2018-12-13
     * @return array
     */
    public static function getBuildPayTypeNameList()
    {
        $buildPayTypeNameList = self::getBase("pay-type-api/get-build-pay-type-name-list");
        return !$buildPayTypeNameList ? [] : Json::decode($buildPayTypeNameList);
    }

    /**
     * 获取默认打开的支付方式列表
     * @author zhenggangwei
     * @date   2018-12-18
     * @return array
     */
    public static function getDefaultOpenPayType()
    {
        $defaultOpenPayType = self::getBase("pay-type-api/default-open-pay-type");
        return !$defaultOpenPayType ? [] : Json::decode($defaultOpenPayType);
    }

    /**
     * 根据楼宇支付策略ID获取对应的支付方式及优惠策略列表
     * @author zhenggangwei
     * @date   2018-12-27
     * @param  integer     $buildPayTypeId 楼宇支付方式ID
     * @return string
     */
    public static function getBuildPayHolicyList($buildPayTypeId)
    {
        return self::getBase("pay-type-api/build-pay-holicy-list", '?buildPayTypeId=' . $buildPayTypeId);
    }
}
