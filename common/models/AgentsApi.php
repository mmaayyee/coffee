<?php
namespace common\models;

use common\helpers\Tools;
use Yii;
use yii\helpers\Json;

/**
 *     代理商-ERP系统 接口类
 */
class AgentsApi extends \yii\db\ActiveRecord
{
    /**
     * 服务密钥配置
     */
    public static function encrptConfig()
    {
        return [
            'agents'    => [ //代理商 密钥配置
                'screct'  => 'zUrK0u71MjdM95YU',
                'encrypt' => 'Cik8Y4j9rVJqRE6P',
            ],
            'agentsErp' => [ //erp密钥配置
                'screct'  => '90KaUxBcC81CkYtR',
                'encrypt' => 'LoISK7lxT4Y9QaTi',
            ],
        ];
    }

    /**
     * 返回的数据格式
     * @author  zgw
     * @version 2016-11-11
     * @param   integer    $errorCode 错误码 0-正确 1-错误
     * @param   string     $msg    提示信息
     * @param   array      $data   返回的数据
     * @return  json
     */
    public static function returnData($errorCode = 0, $msg = '', $data = [])
    {
        $resData = ['error_code' => $errorCode, 'msg' => $msg, 'data' => $data];
        echo json_encode($resData, JSON_UNESCAPED_UNICODE);die;
    }

    /**
     *  返回数据格式
     * @author sulingling
     * @dateTime 2018-08-29
     * @version  [version]
     * @param    array()      $data [返回的数据]
     * @return   string()           [description]
     */
    public static function returnInfo($data = [])
    {
        return Json::encode($data);
    }

    /**
     * 验证服务密钥是否合法
     * @param type $key  应用ID
     * @param type $secretString 加密串
     * @return type
     */
    public static function verifyService($key, $secretString)
    {
        $verifyResult = false;
        $config       = self::encrptConfig();
        if (array_key_exists($key, $config)) {
            //存在
            $screct        = $config[$key]['screct'];
            $encrypt       = $config[$key]['encrypt'];
            $encryptString = md5($encrypt . $screct);
            $verifyResult  = $secretString === $encryptString;
        }
        return $verifyResult;
    }

    /**
     * 请求加密
     * @author  zgw
     * @version 2016-11-17
     * @return  [type]     [description]
     */
    private static function verifyString()
    {
        return "?key=agents&secret=" . md5('Cik8Y4j9rVJqRE6PzUrK0u71MjdM95YU');
    }

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
        // echo Yii::$app->params['agentUrl'] . $action . self::verifyString();die;
        $res = Tools::http_post(Yii::$app->params['agentUrl'] . $action . self::verifyString(), json_encode($data, JSON_UNESCAPED_UNICODE));
        return json_decode($res, true);
    }

    /**
     * get提交数据共用方法
     * @author  zgw
     * @version 2016-08-30
     * @return  [type]     [description]
     */
    public static function getBase($action, $params)
    {
        $res = Tools::http_get(Yii::$app->params['agentUrl'] . $action . self::verifyString() . $params);
        return json_decode($res, true);
    }

    /**
     * 同步设备类型
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function equipTypeSync($data)
    {
        return self::postBase('equipment-types-sync', $data);
    }

    /**
     * 同步物料分类（测试通过）
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function materialTypeSync($data)
    {
        return self::postBase('material-type', $data);
    }

    /**
     * 同步代理商解锁或锁定操作
     * @param $data
     * @return http_get
     * @author zmy
     */
    public static function agentsEquipisLock($data)
    {
        return self::getBase('equip-lock-status-sync', $data);
    }

    /**
     * 同步物料信息
     * @author  zgw
     * @version 2016-08-29
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function updateMaterial($data)
    {
        return self::postBase('update-material', $data);
    }

    /**
     * 同步设备信息
     * @author  zgw
     * @version 2017-02-08
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public static function updateEquipment($data)
    {
        return self::postBase('update-equipment', $data);
    }
}
