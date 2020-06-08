<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class CoffeeProductApi extends \yii\db\ActiveRecord
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
     * 获取咖啡单品数据列表 index 列表
     * @author  zmy
     * @version 2017-09-01
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getCoffeeProductList($params = [])
    {
        $page     = isset($params['page']) ? $params['page'] : 0;
        $taskList = self::postBase("coffee-product-api/index", $params, '&page=' . $page);
        return !$taskList ? [] : Json::decode($taskList);
    }

    /**
     * 获取单品配方发布状态
     * @author  wangxiwen
     * @version 2018-12-17
     * @return
     */
    public static function getProductRelaseStatus()
    {
        $releaseStatusStr = self::getBase("coffee-product-api/get-product-release-status");
        return !$releaseStatusStr ? [] : Json::decode($releaseStatusStr);
    }

    /**
     * 发布单品配方
     * @author  wangxiwen
     * @version 2018-12-17
     * @return
     */
    public static function releaseProductFormula($productId)
    {
        return self::getBase("coffee-product-api/release-product-formula", '&productId=' . $productId);
    }

    /**
     *  通过单品ID，查询单品信息接口
     * @author  zmy
     * @version 2017-09-05
     * @param   [string]     $ID [单品ID]
     * @return  [string]         [单品json数据]
     */
    public static function getCoffeeProductInfo($ID)
    {
        $cofProInfo = self::getBase("coffee-product-api/get-coffee-product-info", "&id=" . $ID);
        return !$cofProInfo ? [] : Json::decode($cofProInfo);
    }

    /**
     * 通过单品ID，查询出单品、设备类型、配方数组
     * @author  zmy
     * @version 2017-09-05
     * @param   [string]     $ID [单品ID]
     * @return  [string]         [数组]
     */
    public static function getCofProStockRecipeList($ID = 0)
    {
        $cofProInfo = self::getBase("coffee-product-api/get-cof-pro-stock-recipe-list", "&id=" . $ID);
        return !$cofProInfo ? [] : Json::decode($cofProInfo);
    }

    /**
     * 删除任务数据
     * @author  zmy
     * @version 2017-09-05
     * @param   integet     $ID 任务ID
     * @return  integer         删除结果 1-成功 0-失败
     */
    public static function delCoffeeProduct($ID)
    {
        return self::getBase("coffee-product-api/del-coffee-product", '&id=' . $ID);
    }

    /**
     * 获取设备类型列表
     * @author wxl
     * @return array|int
     */
    public static function getEquipmentTypeList()
    {
        return self::getBase("coffee-product-api/get-equipment-type-list");
    }
    /**
     * 获取产品标签列表
     * @author wbq
     * @return array|int
     */
    public static function getCoffeeLabelList($params)
    {
        $page      = isset($params['page']) ? $params['page'] : 0;
        $labelList = self::postBase("coffee-product-api/get-coffee-label-list", $params, '&page=' . $page);
        return !$labelList ? [] : Json::decode($labelList);
    }
    /**
     * 获取产品标签列表
     * @author wbq
     * @return array|int
     */
    public static function getCoffeeLabelDetail($params)
    {
        $labelDetail = self::postBase("coffee-product-api/get-coffee-label-detail", $params);
        return !$labelDetail ? [] : Json::decode($labelDetail);
    }
    /**
     * 新增/修改 产品标签
     * @author wbq
     * @return array|int
     */
    public static function updateCoffeeLabel($data)
    {
        $result = self::postBase("coffee-product-api/update-coffee-label", $data);
        return Json::decode($result);
    }
    /**
     * 修改产品标签字段
     * @author wbq
     * @return array|int
     */
    public static function updateFieldCoffeeLabel($data)
    {
        $result = self::postBase("coffee-product-api/update-field-coffee-label", $data);
        return Json::decode($result);
    }
    /**
     * 删除产品标签
     * @author wbq
     * @return array|int
     */
    public static function delCoffeeLabel($data)
    {
        $result = self::postBase("coffee-product-api/del-coffee-label", $data);
        return Json::decode($result);
    }
    /**
     * 根据条件获取产品信息
     * @author wbq
     * @return array|int
     */
    public static function getCoffeeProductFieldList($data)
    {
        $result = self::postBase("coffee-product-api/get-coffee-product-field-list", $data);
        return Json::decode($result);
    }
}
