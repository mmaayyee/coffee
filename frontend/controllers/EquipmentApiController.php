<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/14
 * Time: 16:39
 */
namespace frontend\controllers;

use yii;
use yii\helpers\Json;
use yii\web\Controller;
use common\models\Building;
use common\models\Equipments;
use common\models\SendNotice;
use backend\models\EquipRfidCard;
use backend\models\WxMemberSearch;
use backend\models\EquipRfidCardRecord;
use backend\models\TemporaryAuthorization;

class EquipmentApiController extends Controller
{
    public $enableCsrfValidation = false;

    /*
     * 设备列表接口
     * @author sulingling
     * @param $arr Array() 蓝牙锁的名称
     * @return Array()  需要从数据库中读取数据
     */
    public function actionDetails()
    {
        $bluetoothNameArr = yii::$app->request->post();
        $cache = Yii::$app->cache;
        $data = [];
        $secretKey = $bluetoothNameArr['secretKey'];
        $openId = $cache->get($secretKey);
        unset($bluetoothNameArr['secretKey']);
        foreach ($bluetoothNameArr as $key=>$var)
        {
                $equipmentData = Equipments::getOne(['bluetooth_name'=>$var],'build_id,factory_code,bluetooth_name,org_id,equip_code');
                if ($equipmentData) {
                    $equipmentData['factory_number'] = empty($equipmentData['factory_code']) ? '' : $equipmentData['factory_code'];
                    unset($equipmentData['factory_code']);
                    $buildingArr = Building::getOne(['id'=>$equipmentData['build_id']],'name');
                    $equipmentData['building_name'] = $buildingArr['name'] == '' ? '' : $buildingArr['name'];
                    $wxMemberSearch = WxMemberSearch::getOne(['openID' => $openId]);
                    $userName = $wxMemberSearch->name;
                    $equipmentData['jurisdiction'] = TemporaryAuthorization::getOne(['wx_member_name' => $userName,'state' => 1,'build_name'=>$buildingArr['name']]) ? 1 :(EquipRfidCard::isOpenDoorJurisdiction($wxMemberSearch,$equipmentData) ? 1 : 0);//判断该用户是否有打开设备的权限   0 没有 1 有
                } else {
                    $equipmentData['factory_number'] = '';
                    $equipmentData['building_name']  = '';
                    $equipmentData['jurisdiction']  = 1;
                }
                $equipmentData['bluetooth_name'] = $var;
                $equipmentData['deviceid'] = $key;
                array_push($data,$equipmentData);
        };
        if ($data) {
            $msg = ['is_success' => 1, 'msg' => $data];
        } else {
            $msg = ['is_success' => 0, 'msg' => '附近没有数据'];
        }
        return Json::encode($msg);
    }

    /**
     * 蓝牙绑定接口
     * @author sulingling
     * @param Array()  出厂编号
     * @return json格式  error 1 绑定蓝牙 2 没有绑定蓝牙
     */
    public function actionConnect()
    {
        $data = yii::$app->request->post();//数据
        $equipmentsData = Equipments::getOne(['factory_code' => $data['number']]);
        if (!$equipmentsData) {
            $arr = ['is_success' => 2,'msg' => '出厂编号错误'];
        } else {
            $building = Building::findOne(['id' => $equipmentsData['build_id']],'name');
            $equipments = Equipments::findOne($equipmentsData['id']);
            $equipments->bluetooth_name = $data['bluetooth_name'];
            $result = $equipments->save();
            //判断是否 绑定蓝牙
            if ($result) {
                $cache = Yii::$app->cache;
                $secretKey = $data['secretKey'];
                $openId = $cache->get($secretKey);
                $wxMemberSearch = WxMemberSearch::getOne(['openID' => $openId]);
                $userName = $wxMemberSearch->name;
                $jurisdiction = TemporaryAuthorization::getOne(['wx_member_name' => $userName,'state' => 1,'build_name'=>$building['name']]) ? 1 :(EquipRfidCard::isOpenDoorJurisdiction($wxMemberSearch,$equipmentsData) ? 1 : 0);
                $arr = ['is_success' => 1, 'msg' => [
                    'factory_code' =>  $equipmentsData['factory_code'],
                    'building_name' => $building['name'],
                    'bluetooth_name' => $data['bluetooth_name'],
                    'jurisdiction' => $jurisdiction,
                ],];
            } else {
                $arr = ['is_success' => 0, 'msg' => '蓝牙绑定失败'];
            }
        }
        return Json::encode($arr);
    }

    /*
     * 申请临时开门
     * @author sulingling
     * @param $secretKey 获取openid
     * @param $number 出厂编号
     * @return json
     */
    public function actionOpen()
    {
        $number = yii::$app->request->post('number');
        $secretKey = yii::$app->request->post('secretKey');
        $cache = Yii::$app->cache;
        $openId = $cache->get($secretKey);
        $wxMemberData = WxMemberSearch::getOne(['openID' => $openId]);
        $wxMemberName = $wxMemberData->name;
        $wxMemberUserid = $wxMemberData->userid;
        $buildId = Equipments::getOne(['factory_code' => $number]);
        if (!$buildId) {
            return Json::encode(['is_success' => 2,'msg'=>'出厂编号错误']);
        }
        $buildNname = Building::getOne(['id' => $buildId['build_id']])['name'];
        /*
         * 在同一个用户，同一个楼宇名称，
         * 在主管没有审核的情况下要修改申请时间，否则就添加数据
         */
        $where = ['build_name' => $buildNname,'wx_member_name' => $wxMemberName,'audit_time' => 0];
        $result = TemporaryAuthorization::findWhere($where);
        if ($result) {
            $temporaryAuthorization = TemporaryAuthorization::findOne($result->id);
        } else {
            $temporaryAuthorization = new TemporaryAuthorization();
            $temporaryAuthorization->build_name = $buildNname;
            $temporaryAuthorization->userid = $wxMemberUserid;
            $temporaryAuthorization->wx_member_name = $wxMemberName;
        }
        $temporaryAuthorization->application_time = time();
        $temporaryAuthorization->state              = 0;
        $data = $temporaryAuthorization->save();
       if ($data)
       {
           $temporaryAuthorizationId = isset($result->id) ? $result->id : $temporaryAuthorization->id;
           $content = '你收到一条临时开门申请';
           $url = 'temporary-authorization/index?temporaryAuthorizationId='.$temporaryAuthorizationId;
           SendNotice::sendWxNotice($wxMemberData->parent_id ,$url, $content, Yii::$app->params['distribution_agentid']);
           return Json::encode(['is_success'=>1,'msg'=>'请求成功']);
       } else {
           return Json::encode(['is_success'=>0,'msg'=>'请求失败']);
       }
    }

    /**
     * 添加开门记录
     * @author sulingling
     * @param $secretKey 获取openid
     * @param $equipCode 设备编号
     * @return json
     */
    public function actionCreate()
    {
        $data = yii::$app->request->post();
        $cache = Yii::$app->cache;
        $openId = $cache->get($data['secretKey']);
        $wxMember = WxMemberSearch::getOne(['openID'=>$openId]);
        $factoryCode = $data['factory_code'] ? $data['factory_code'] : '';
        $equipments = $factoryCode ? Equipments::getOne(['factory_code'=>$factoryCode]) : '';
        $equipmentCode = $equipments ? $equipments['equip_code'] : '';
        $equipmentData = $equipmentData = Equipments::getOne(['equip_code'=>$equipmentCode],'build_id,factory_code,bluetooth_name,org_id,equip_code');
        $openType = $data['factory_code'] ? (EquipRfidCard::isOpenDoorJurisdiction($wxMember,$equipmentData) ? 3 :4) : 3 ;
        $result = EquipRfidCardRecord::saveRfidData($wxMember,$equipmentCode,$openType,2);
        if ($result) {
            $msg = ['is_success' => 1, 'msg' => '添加成功'];
        } else {
            $msg = ['is_success' => 2, 'msg' => '添加失败'];
        }
        return Json::encode($msg);
    }

    /**
     * 临时开门权限是否到期接口
     * @author sulingling
     * @param $secretKey 获取openid
     * @param $bluetoothNname 蓝牙名称
     * @return Json
     */
    public function actionJurisdiction()
    {
        $data = yii::$app->request->post();
        $cache = Yii::$app->cache;
        $bluetoothName = $data['bluetooth_name'];
        $secretKey = $data['secretKey'];
        $openId = $cache->get($secretKey);
        $equipmentData = Equipments::getOne(['bluetooth_name'=>$bluetoothName],'build_id,factory_code,bluetooth_name,org_id,equip_code');
        if ($equipmentData) {
            $wxMemberSearch = WxMemberSearch::getOne(['openID' => $openId]);
            $userName = $wxMemberSearch->name;
            $building = Building::getOne(['id' => $equipmentData['build_id']]);
            $jurisdiction = TemporaryAuthorization::getOne(['wx_member_name' => $userName,'state' => 1,'build_name'=>$building['name']]) ? 1 :(EquipRfidCard::isOpenDoorJurisdiction($wxMemberSearch,$equipmentData) ? 1 : 0);
            if ($jurisdiction) {
                $msg = ['is_success' => 1, 'msg' => '开门'];
            } else {
                $msg = ['is_success' => 0, 'msg' => '申请临时开门'];
            }
        } else {
            $msg = ['is_success' => 1, 'msg' => '开门'];
        }
        return Json::encode($msg);
    }
}