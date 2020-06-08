<?php

namespace backend\controllers;

use backend\models\Activity;
use backend\models\ActivitySearch;
use backend\models\ManagerLog;
use common\models\ActivityApi;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * ActivityController implements the CRUD actions for Activity model.
 */
class GetCouponActivityController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all Activity models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('领券活动')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ActivitySearch();
        $dataProvider = $searchModel->couponActivitySearch(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 中奖信息页面
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看领券活动')) {
            return $this->redirect(['site/login']);
        }
        $activity       = ActivityApi::getCouponActivityInfo($id);
        $activityStatus = Activity::activityStatusList();
        unset($activityStatus['']);
        $isVerifySubscribeLsit = Activity::$isVerifySubscribe;
        unset($isVerifySubscribeLsit['']);
        $activity['activityData']['status'] = empty($activityStatus[$activity['activityData']['status']]) ? '' : $activityStatus[$activity['activityData']['status']];
        $activity['is_verify_subscribe']    = empty($isVerifySubscribeLsit[$activity['is_verify_subscribe']]) ? '' : $isVerifySubscribeLsit[$activity['is_verify_subscribe']];
        return $this->render('view', ['activity' => Json::encode($activity)]);
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加领券活动')) {
            return $this->redirect(['site/login']);
        }
        $data = file_get_contents("php://input");
        if ($data) {
            $data = Json::decode($data);
            ManagerLog::saveLog(Yii::$app->user->id, "领券活动", ManagerLog::CREATE, "添加领券活动");
            return Activity::saveCouponActivity($data);
        }
        $data = Activity::updateCouponActivity(1);
        $data = Activity::updateCouponActivity(0);
        foreach ($data['couponGroupList'] as $gid => &$gname) {
            $gname = $gid . "_" . $gname;
        }
        unset($gname);
        foreach ($data['couponList'] as $cid => &$cname) {
            $cname = $cid . "_" . $cname;
        }
        unset($cname);
        return $this->render('update', ['data' => Json::encode($data)]);
    }

    /**
     * Updates an existing Activity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        if (!Yii::$app->user->can('编辑领券活动')) {
            return $this->redirect(['site/login']);
        }
        $data = file_get_contents("php://input");
        if ($data) {
            $data = Json::decode($data);
            ManagerLog::saveLog(Yii::$app->user->id, "领券活动", ManagerLog::UPDATE, "编辑领券活动");
            return Activity::saveCouponActivity($data);
        }
        $data = Activity::updateCouponActivity(0);
        foreach ($data['couponGroupList'] as $gid => &$gname) {
            $gname = $gid . "_" . $gname;
        }
        unset($gname);
        foreach ($data['couponList'] as $cid => &$cname) {
            $cname = $cid . "_" . $cname;
        }
        unset($cname);
        return $this->render('update', ['data' => Json::encode($data)]);
    }

}
