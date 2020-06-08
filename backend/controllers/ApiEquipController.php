<?php
namespace backend\controllers;

use backend\models\EquipBrew;
use backend\models\EquipVersion;
use yii\helpers\Json;
use yii\web\Controller;

class ApiEquipController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 设备版本信息回传接口
     * @author  zgw
     * @version 2016-11-04
     * @return  [type]     [description]
     */
    public function actionEquipVersion()
    {
        $data = file_get_contents("php://input");
         //$data = '{"equip_code":"0011003","main_control_version":"V32","io_version":"V47","groupId":23,"groupVersion":"v1.11"}';
        if (!$data) {
            echo Json::encode(['error_code' => 1, 'msg' => '设备编号不能为空']);die;
        }
        $data = Json::decode($data, 1);
        if (!$data['equip_code']) {
            echo Json::encode(['error_code' => 1, 'msg' => '设备编号不能为空']);die;
        }
        $saveRes = EquipVersion::addData($data);
        if (!$saveRes) {
            echo Json::encode(['error_code' => 1, 'msg' => '数据保存失败']);die;
        }
        echo Json::encode(['error_code' => 0, 'msg' => '操作成功']);
    }

    /**
     * 设备版本信息回传接口
     * @author  zgw
     * @version 2016-11-04
     * @return  [type]     [description]
     */
    public function actionEquipBrew()
    {
        $data = file_get_contents("php://input");
        // $data = '{"equip_code":"0011003","product_id":"1","brew_time":"47"}';
        if (!$data) {
            echo Json::encode(['error_code' => 1, 'msg' => '设备编号不能为空']);die;
        }
        $data = Json::decode($data, 1);
        if (!$data['equip_code']) {
            echo Json::encode(['error_code' => 1, 'msg' => '设备编号不能为空']);die;
        }
        if (!$data['product_id']) {
            echo Json::encode(['error_code' => 1, 'msg' => '产品id不能为空']);die;
        }
        if (!$data['brew_time']) {
            echo Json::encode(['error_code' => 1, 'msg' => '冲泡器时间不能为空']);die;
        }
        $saveRes = EquipBrew::addData($data);
        if (!$saveRes) {
            echo Json::encode(['error_code' => 1, 'msg' => '数据保存失败']);die;
        }
        echo Json::encode(['error_code' => 0, 'msg' => '操作成功']);
    }
}
