<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13
 * Time: 19:20
 */
namespace frontend\controllers;

use backend\models\DistributionTask;
use backend\models\ScmMaterialStock;
use backend\models\WxMemberSearch;
use common\helpers\Tools;
use common\models\Equipments;
use common\models\WxMember;
use frontend\models\FrontendDistributionTask;
use yii;
use yii\helpers\Json;
use yii\web\Controller;

class BluetoothApiController extends Controller
{
    public $enableCsrfValidation = false; //防CSRF攻击
    /*
     * 小程序授权接口 （第一个接口）
     * @author  sulingling
     * @version  2018-3-13
     * @param  $code 【页面传过来的数据】
     * @return $arr 成功  【$openId】    失败 提示 授权失败
     */
    public function actionAuthorization($code)
    {
        $cache  = Yii::$app->cache;
        $openid = $cache->get($code);
        if (!$openid) {
            $appID            = Yii::$app->params['miniprogramAppID'];
            $secret           = Yii::$app->params['miniprogramAppSecret'];
            $authorizationUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appID . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
            $urlData          = Tools::http_get($authorizationUrl);
            $urlContentArr    = Json::decode($urlData);
            if (!$urlContentArr['session_key']) {
                return Json::encode([
                    'is_success' => 0,
                    'msg'        => 'code值是失效的',
                ]);
            }
            $code   = md5($urlContentArr['session_key'] . $urlContentArr['openid']); //生成openId的key值
            $openid = $urlContentArr['openid'];
            $cache->set($code, $openid);
        }
        $where  = ['openID' => $openid];
        $result = WxMemberSearch::getOne($where);
        if ($result) {
            $arr = [
                'is_success' => 1,
                'secretKey'  => $code,
                'msg'        => '用户授权成功',
            ];
        } else {
            $arr = [
                'is_success' => 2,
                'secretKey'  => $code,
                'msg'        => '请注册后再操作',
            ];
        }
        return Json::encode($arr);
    }

    public function actionBluetoothLogin($code)
    {
        $cache  = Yii::$app->cache;
        $openid = $cache->get($code);
        if (!$openid) {
            $appID            = Yii::$app->params['bluetoothAppId'];
            $secret           = Yii::$app->params['bluetoothAppSecret'];
            $authorizationUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appID . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
            $urlData          = Tools::http_get($authorizationUrl);
            $urlContentArr    = Json::decode($urlData);
            if (empty($urlContentArr['session_key'])) {
                return Json::encode([
                    'is_success' => 0,
                    'msg'        => 'code值是失效的',
                ]);
            }
            $code   = md5($urlContentArr['session_key'] . $urlContentArr['openid']); //生成openId的key值
            $openid = $urlContentArr['openid'];
            $cache->set($code, $openid);
        }
        $where  = ['openID' => $openid];
        $result = WxMemberSearch::getOne($where);
        if ($result) {
            $arr = [
                'is_success' => 1,
                'secretKey'  => $code,
                'msg'        => '用户授权成功',
            ];
        } else {
            $arr = [
                'is_success' => 2,
                'secretKey'  => $code,
                'msg'        => '请注册后再操作',
            ];
        }
        return Json::encode($arr);
    }

    /**
     * 蓝牙秤初始化数据
     * @author wangxiwen
     * @version 2018-06-14
     * @return json|string
     */
    public function actionBluetoothBalanceInit()
    {
        $code   = Yii::$app->request->get('code');
        $openId = Yii::$app->cache->get($code);
        //查询用户
        $userId            = WxMember::getUseridByOpenid($openId);
        $bluetoothTaskList = [];
        //通过userId获取获取楼宇已打卡且未完成的运维数据
        $taskList = DistributionTask::getBluetoothBalanceInitData($userId);
        if (empty($taskList)) {
            return '{}';
        }
        $equipId = $taskList['equip_id'];
        //通过设备查询料仓信息
        $materialInfo = DistributionTask::getMaterialInfoList($equipId);
        //获取料仓编号对应的料仓ID数组
        $stockIdArr = ScmMaterialStock::getMaterialStockCodeToId();
        //获取误差值
        $errorValue  = DistributionTask::getErrorValue($equipId);
        $materialArr = !empty($taskList['delivery_task']) ? Json::decode($taskList['delivery_task']) : [];
        //查询设备料仓最终读数
        $readingArr = !empty($taskList['reading']) ? Json::decode($taskList['reading']) : [];
        //获取空料盒重量
        $emptyBoxWeghtList = DistributionTask::getEmptyBoxWeight($equipId);
        $emptyBoxWeghtArr  = $emptyBoxWeghtList ? Json::decode($emptyBoxWeghtList) : [];
        $materialList      = [];
        foreach ($materialInfo as $stockCode => $material) {
            $materialTypeId   = $material['material_type_id'];
            $packets          = $materialArr[$stockCode]['packets'] ?? 0;
            $gram             = $materialArr[$stockCode]['gram'] ?? 0;
            $reading          = $readingArr[$stockCode] ?? 0;
            $stockId          = $stockIdArr[$stockCode] ?? 0;
            $emptyBoxWeght    = $emptyBoxWeghtArr[$stockId] ?? 0;
            $completedReading = $reading + $emptyBoxWeght;
            $normalAddAmount  = $packets * $material['weight'] + $gram;
            $materialList[]   = [
                'material_stock_code' => $stockCode,
                'material_type_id'    => $materialTypeId,
                'material_name'       => $material['material_name'],
                'normal_add_amount'   => (string) $normalAddAmount,
                'error_amount'        => (string) $errorValue,
                'completed_reading'   => (string) $completedReading,
            ];
        }
        $materialList      = FrontendDistributionTask::materialStockSort($materialList);
        $bluetoothTaskList = [
            'build_name'   => $taskList['build_name'],
            'task_id'      => $taskList['task_id'],
            'equip_id'     => $equipId,
            'build_id'     => $taskList['build_id'],
            'materialList' => $materialList,
        ];
        return Json::encode($bluetoothTaskList);
    }

    /**
     * 接收蓝牙秤上传的楼宇物料信息数据
     * @author wangxiwen
     * @version 2018-06-14
     * @return json|string
     */
    public function actionUploadMaterial()
    {
        $materialArr = Yii::$app->request->get();
        //获取设备编号
        $equipCode = Equipments::getEquipCode($materialArr['equip_id']);
        //获取设备料仓剩余物料
        $volumesList          = DistributionTask::getEquipmentVolumesArr($equipCode);
        $surplusMaterialArray = $volumesList ? Json::decode($volumesList) : [];
        //获取空料盒重量
        $emptyBoxWeghtArr  = DistributionTask::getEmptyBoxWeight($materialArr['equip_id']);
        $emptyBoxWeghtList = $emptyBoxWeghtArr ? Json::decode($emptyBoxWeghtArr) : [];
        $materialInfo      = Json::decode($materialArr['materialList']);
        $materialList      = [];
        $stockCodeToIdList = ScmMaterialStock::getMaterialStockCodeToId();
        foreach ($materialInfo as $materials) {
            $materialTypeId = $materials['material_type_id'];
            $stockCode      = $materials['material_stock_code'];
            if (empty($stockCodeToIdList[$stockCode])) {
                continue;
            }
            $beforeAmount    = (double) $materials['before_amount'];
            $afterAmount     = (double) $materials['after_amount'];
            $surplusMaterial = $surplusMaterialArray[$stockCode] ?? 0;
            //空料盒重量
            $emptyBoxWeght = $emptyBoxWeghtList[$stockCodeToIdList[$stockCode]] ?? 0;
            //添加量
            $addAmount = $afterAmount >= $beforeAmount ? round($afterAmount - $beforeAmount, 2) : 0;
            //添加量正负5g以内按0计算
            $addAmount = abs($addAmount) < 5 ? 0 : $addAmount;
            //剩余量
            $overAmount = round($afterAmount - (double) $emptyBoxWeght, 2);
            //修改量
            $changeAmount = round($afterAmount - (double) $emptyBoxWeght - (double) $surplusMaterial, 2);

            $materialList[$stockCode][$materialTypeId] = [
                'addAmount'    => (string) $addAmount,
                'overAmount'   => (string) $overAmount,
                'changeAmount' => (string) $changeAmount,
            ];
        }
        //更新运维任务
        $model                    = DistributionTask::find()->where(['build_id' => $materialArr['build_id'], 'is_sue' => 1])->one();
        $model->bluetooth_upload  = Json::encode($materialList);
        $model->end_delivery_time = time();
        $ret                      = $model->save();
        $code                     = $ret ? 1 : 0;
        return Json::encode(['code' => $code]);
    }
}
