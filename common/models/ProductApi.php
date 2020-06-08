<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class ProductApi extends \yii\db\ActiveRecord
{
    /**
     * post提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @param   string     $action 请求的方法名
     * @param   array      $data   发送的数据
     * @return  boole              返回的数据
     */
    private static function postBase($action, $data)
    {
        // echo  Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString();die();
        $res = Tools::http_post(Yii::$app->params['fcoffeeUrl'] . 'product-api/' . $action . Api::verifyString(), Json::encode($data));
        if ($res === 'true' || $res == 1) {
            return true;
        }
        return false;
    }

    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    private static function getBase($action, $params = '')
    {
        if (is_array($params)) {
            $params = Tools::getParamsFormat($params);
        }
        // echo Yii::$app->params['fcoffeeUrl'] . 'product-api/' . $action . Api::verifyString() . $params;die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . 'product-api/' . $action . Api::verifyString() . $params);
    }

    /**
     * 获取城市优惠策略
     * @author  zgw
     * @version 2017-07-03
     * @return  array     城市优惠策略
     */
    public static function getProduct($params)
    {
        return self::getBase("index", $params);
    }

    /**
     * 保存城市优惠策略
     * @author  zgw
     * @version 2017-07-03
     * @param   array     $data 要保存的数据
     * @return  int             保存结果 1-保存成功 0-保存失败
     */
    public static function saveCityPreferentialStrategy($data)
    {
        return self::postBase("save-city-preferential-strategy", $data);
    }

    /**
     * 删除城市优惠策略
     * @author  zgw
     * @version 2017-07-05
     * @param   int     $id 城市优惠策略id
     * @return  boole        删除结果
     */
    public static function delCityPreferentialStrategy($id)
    {
        return self::postBase("del-city-preferential-strategy", ['id' => $id]);
    }
}
