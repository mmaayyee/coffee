<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 * 设备类型参数 接口类
 */
class EquipmentTypeParameterApi extends \yii\db\ActiveRecord
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
//        echo Yii::$app->params['fcoffeeUrl'] . $action . self::verifyString();Json::encode($data);die();
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
     * 获取设备类别参数数据列表 index 列表
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getEquipmentTypeParameterList($params = [])
    {
        $parameterList = self::postBase("equipment-type-parameter-api/get-equipment-type-parameter-list", $params);
        return Json::decode($parameterList);
    }
    /**
     * 获取设备类别数据列表
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getEquipmentTypeList($params = [])
    {
        $typeList = self::postBase("equipment-type-parameter-api/get-equipment-type-list", $params);
        return Json::decode($typeList);
    }
    /**
     * 创建/修改设备参数
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $data [新增的数据数组]
     * @return  [array]           [数组]
     */
    public static function updateEquipmentTypeParameter($data)
    {
        $result = self::postBase("equipment-type-parameter-api/update-equipment-type-parameter", $data);
        return Json::decode($result);
    }
    /**
     * 删除设备参数
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $data [删除条件数组]
     * @return  [array]           [数组]
     */
    public static function delEquipmentTypeParameter($data)
    {
        $result = self::postBase("equipment-type-parameter-api/del-equipment-type-parameter", $data);
        return Json::decode($result);
    }
    /**
     * 获取设备类别参数值数据列表 index 列表
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getEquipTypeParamValList($params = [])
    {
        $parameterList = self::postBase("equipment-type-parameter-api/get-equip-type-param-val-list", $params);
        $result        = Json::decode($parameterList);
        if (empty($result)) {
            return ['typeParamList' => [], 'logList' => []];
        }
        return $result;
    }
    /**
     * 获取地区列表列表 index 列表
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $param [传输的数组]
     * @return  [array]           [数组]
     */
    public static function getGetOrgList($params = [])
    {
        $orgList = self::postBase("equipment-type-parameter-api/get-org-list", $params);
        return Json::decode($orgList);
    }
    /**
     * 创建/修改设备参数值
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $data [新增的数据数组]
     * @return  [array]           [数组]
     */
    public static function updateEquipTypeParamVal($data)
    {
        $result = self::postBase("equipment-type-parameter-api/update-equip-type-param-val", $data);
        return Json::decode($result);
    }
    /**
     * 更新设备参数值
     * @author  wbq
     * @version 2018-7-16
     * @param   array      $data [新增的数据数组]
     * @return  [array]           [数组]
     */
    public static function updateEquipParamVal($data)
    {
        $result = self::postBase("equipment-type-parameter-api/update-equip-param-val", $data);
        return Json::decode($result);
    }
}
