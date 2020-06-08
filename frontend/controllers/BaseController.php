<?php
namespace frontend\controllers;

use common\helpers\WXApi\Auth;
use common\helpers\WXApi\User;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class BaseController extends Controller
{
    public $userinfo = [];
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $this->userinfo = Yii::$app->session->get('userinfo');
        $agentID        = Yii::$app->request->get('agentId');
        if ($agentID) {
            Yii::$app->session->set('agentId', $agentID);
        } else {
            $agentID = Yii::$app->session->get('agentId');
        }
        if ($this->userinfo) {
            return true;
        }
        $authObj = new Auth();
        if (isset($_GET['code'])) {
            $userRes = $authObj->getUserInfo($_GET['code'], $agentID);
            if (isset($userRes['UserId']) && $userRes['UserId']) {
                $userObj    = new User();
                $userDetail = $userObj->userDetail($userRes['UserId']);
                if ($userDetail['errcode'] == 0) {
                    $this->userinfo = $userDetail;
                    Yii::$app->session->set('userinfo', $userDetail);
                    return true;
                } else {
                    $this->redirect(['/site/error', 'message' => '微信授权失败，请重试']);
                    return false;
                }
            } else {
                $this->redirect(['/site/error', 'message' => '请先关注企业微信']);
                return false;
            }
        } else {
            $authObj->getCode();
        }
    }

    public function actionIndex()
    {
        echo "<pre/>";
        print_r($this->userinfo);
    }

    public function actionClearUserInfo()
    {
        Yii::$app->session->set('userinfo', null);
    }
    public function actionClearAgentid()
    {
        $agentid = Yii::$app->session->get('agentId');
        var_dump($agentid);
        Yii::$app->session->set('agentId', null);
    }
    public function actionClearJsApiTicket()
    {
        Yii::$app->cache->set('jsApiTicket', null);
    }
    public function actionGetJsApiTicket()
    {
        var_dump(Yii::$app->cache->get('jsApiTicket'));
    }
}
