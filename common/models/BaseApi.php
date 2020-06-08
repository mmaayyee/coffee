<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 接口类
 */
class BaseApi extends \yii\db\ActiveRecord
{

    /**
     * curlPost
     * @author zhenggangwei
     * @date   2019-04-08
     * @param  string     $action 请求的接口地址
     * @param  array      $data   传递的参数
     * @param  string     $params get传参
     * @return json
     */
    public static function postBase($action, $data, $params = "")
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . ".html" . $params, Json::encode($data);die;
        return Tools::http_post(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params, Json::encode($data));
    }

    /**
     * curlget
     * @author zhenggangwei
     * @date   2019-04-08
     * @param  string     $action 请求的接口地址
     * @param  string     $params get传参
     * @return json
     */
    public static function getBase($action, $params = '')
    {
        // echo Yii::$app->params['fcoffeeUrl'] . $action . $params . '.html';die;
        return Tools::http_get(Yii::$app->params['fcoffeeUrl'] . $action . '.html' . $params);
    }
}
