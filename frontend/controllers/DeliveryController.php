<?php
namespace frontend\controllers;

use common\models\DeliveryApi;
use frontend\controllers\BaseController;
use frontend\models\JSSDK;
use Yii;
use yii\helpers\Json;

/**
 * 外卖配送端 Controller
 */
class DeliveryController extends BaseController
{
    public $enableCsrfValidation = false;
    //定义成功标识
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';
    //定义成功
    /**
     * 跨域提交完成后进行header信息跳转。
     * @author  zmy
     * @version 2018-03-01
     * @return  [type]     [description]
     */
    public function returnHeader()
    {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
            exit;
        }
    }
    /**
     * 获取待接单列表  delivery/not-accept-order
     * @version 2018-08-08
     * @method POST
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionNotAcceptOrder()
    {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $jssdk        = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret']['address_book']);
            $signPackage  = $jssdk->getSignPackage();
            //加载页面
            return $this->render('not-accept-order', ['signPackage' => Json::encode($signPackage)]);
        }
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取待接单列表
        $deliveryList = DeliveryApi::getDeliWaitList($where);
        //获取统计数据
        $deliveryListCount = DeliveryApi::getDeliveryOrderCount($where);
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'data'   => [
                'data_list'  => $deliveryList,
                'list_count' => $deliveryListCount,
            ],
        ]);
    }
    /**
     * 执行接单操作  delivery/receiv-order
     * @version 2018-08-08
     * @method POST
     * @param [string]         delivery_order_id     配送单id
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionReceivOrder()
    {
        if (!Yii::$app->request->isPost) {
            exit();
        }
        //执行接单操作
        $deliveryOrderId = Yii::$app->request->post('delivery_order_id');
        if (!$deliveryOrderId) {
            return Json::encode([
                'status' => self::STATUS_ERROR,
                'msg'    => '参数有误!',
            ]);
        }
        $where['delivery_order_id'] = $deliveryOrderId;
        $where['delivery_person']   = $this->userinfo['userid'];
        //执行接单操作
        $acceptResultStatus = DeliveryApi::acceptDeli($where);
        return Json::encode($acceptResultStatus);
    }
    /**
     * 获取已接单列表  delivery/doing-order
     * @version 2018-08-13
     * @method POST
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionDoingOrder()
    {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $jssdk        = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret']['address_book']);
            $signPackage  = $jssdk->getSignPackage();
            //加载页面
            return $this->render('doing-order', ['signPackage' => Json::encode($signPackage)]);
        }
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取已接单列表
        $deliveryList = DeliveryApi::getDeliList($where);
        //获取统计列表
        $deliveryListCount = DeliveryApi::getDeliveryOrderCount($where);
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'data'   => [
                'data_list'  => $deliveryList,
                'list_count' => $deliveryListCount,
            ],
        ]);
    }
    /**
     * 获取已完成列表  delivery/completed-order
     * @version 2018-08-13
     * @method POST
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionCompletedOrder()
    {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $jssdk        = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret']['address_book']);
            $signPackage  = $jssdk->getSignPackage();
            //加载页面
            return $this->render('completed-order', ['signPackage' => Json::encode($signPackage)]);
        }
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取已接单列表
        $deliveryList = DeliveryApi::getDeliCompleteList($where);
        //获取统计列表
        $deliveryListCount = DeliveryApi::getDeliveryOrderCount($where);
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'data'   => [
                'data_list'  => $deliveryList,
                'list_count' => $deliveryListCount,
            ],
        ]);
    }
    /**
     * 获取接单详情  delivery/get-deli-detail
     * @version 2018-08-14
     * @method POST
     * @param [string]         delivery_order_id     配送单id
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionGetDeliDetail()
    {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $jssdk        = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret']['address_book']);
            $signPackage  = $jssdk->getSignPackage();
            //加载页面
            return $this->render('deli-detail', ['signPackage' => Json::encode($signPackage)]);
        }
        //查看接单详情
        $deliveryOrderId = Yii::$app->request->post('delivery_order_id');
        if (!$deliveryOrderId) {
            return Json::encode([
                'status' => self::STATUS_ERROR,
                'msg'    => '参数有误!',
            ]);
        }
        $where['delivery_order_id'] = $deliveryOrderId;
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取已接单信息
        $deliveryData = DeliveryApi::getDeliDetail($where);
        return Json::encode($deliveryData);
    }
    /**
     * 完成制作  delivery/make-complete-deli
     * @version 2018-08-14
     * @method POST
     * @param [string]         delivery_order_id     配送单id
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionMakeCompleteDeli()
    {
        if (!Yii::$app->request->isPost) {
            exit();
        }
        //查看接单详情
        $deliveryOrderId = Yii::$app->request->post('delivery_order_id');
        if (!$deliveryOrderId) {
            return Json::encode([
                'status' => self::STATUS_ERROR,
                'msg'    => '参数有误!',
            ]);
        }
        $where['delivery_order_id'] = $deliveryOrderId;
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取操作结果
        $deliveryData = DeliveryApi::makeCompleteDeli($where);
        return Json::encode($deliveryData);
    }
    /**
     * 确认送达  delivery/complete-delivery-order
     * @version 2018-08-14
     * @method POST
     * @param  [string]    delivery_order_id     配送单id
     * @author wbq
     * @return [json]          [成功/失败]
     */
    public function actionCompleteDeliveryOrder()
    {
        if (!Yii::$app->request->isPost) {
            exit();
        }
        //查看接单详情
        $deliveryOrderId = Yii::$app->request->post('delivery_order_id');
        if (!$deliveryOrderId) {
            return Json::encode([
                'status' => self::STATUS_ERROR,
                'msg'    => '参数有误!',
            ]);
        }
        $where['delivery_order_id'] = $deliveryOrderId;
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取操作结果
        $deliveryData = DeliveryApi::completeDeliveryOrder($where);
        return Json::encode($deliveryData);
    }
    /**
     * 修改预计送达时间  delivery/save-expect-time
     * @version 2018-10-24
     * @method POST
     * @param  [string]    delivery_order_id     配送单id
     * @param  [number]    minute     预计时长
     * @author jiangfeng
     * @return [json]          [成功/失败]
     */
    public function actionSaveExpectTime()
    {
        if (!Yii::$app->request->isPost) {
            exit();
        }
        //查看接单详情
        $deliveryOrderId = Yii::$app->request->post('delivery_order_id');
        $minute = Yii::$app->request->post('minute');
        if (!$deliveryOrderId || !$minute) {
            return Json::encode([
                'status' => self::STATUS_ERROR,
                'msg'    => '参数有误!',
            ]);
        }
        $where['delivery_order_id'] = $deliveryOrderId;
        $where['minute'] = $minute;
        //获取企业微信用户信息
        $where['delivery_person'] = $this->userinfo['userid'];
        //获取操作结果
        $deliveryData = DeliveryApi::saveExpectTime($where);
        return Json::encode($deliveryData);
    }
}
