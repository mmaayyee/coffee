<?php

namespace backend\controllers;

use backend\models\ActivityCombinPackageAssoc;
use backend\models\ActivityCombinPackageAssocSearch;
use backend\models\ManagerLog;
use common\models\ActivityApi;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ActivityCombinPackageAssocController implements the CRUD actions for ActivityCombinPackageAssoc model.
 */
class ActivityCombinPackageAssocController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ActivityCombinPackageAssoc models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('自组合套餐活动管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ActivityCombinPackageAssocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ActivityCombinPackageAssoc model.
     * @param integer $combin_package_id
     * @param integer $activity_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('自组合套餐活动查看')) {
            return $this->redirect(['site/login']);
        }
        $combinPackageList['ActivityCombinPackageAssoc'] = ActivityApi::getCombinPackageAssocView($id, 'view');
        $model                                           = new ActivityCombinPackageAssoc();
        $model->load($combinPackageList);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new ActivityCombinPackageAssoc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('自组合套餐活动添加')) {
            return $this->redirect(['site/login']);
        }
        $model = new ActivityCombinPackageAssoc();
        // 获取组合的单品数组
        $pointProductList = ActivityCombinPackageAssoc::getPointProductList();
        // 获取优惠券套餐接口
        // $couponPackageList = QuickSendCoupon::getCouponValidPackage();
        $couponPackageList = Api::getCouponGroupValidList();
        foreach ($couponPackageList as &$group) {
            $group['group_name'] = $group['group_id'] . '_' . $group['group_name'];
        }
        unset($group);
        // 获取优惠券接口
        $couponSingleList = Api::activityCombinGetCouponList();
        foreach ($couponSingleList as $couponId => &$couponName) {
            $couponName = $couponId . '_' . $couponName;
        }
        unset($couponName);
        // 城市数组
        $mechanismList = Api::getOrgMechanismList();
        return $this->render('_form', [
            'model'             => $model,
            'pointProductList'  => $pointProductList ? Json::encode($pointProductList) : '{}',
            'mechanismList'     => $mechanismList ? Json::encode($mechanismList) : "{}",
            'singleCouponList'  => $couponSingleList ? Json::encode($couponSingleList) : "{}",
            'packageCouponList' => $couponPackageList ? Json::encode($couponPackageList) : "{}",
            'updateTemp'        => "{}",
            'is_update'         => '1',
        ]);
    }

    /**
     * Updates an existing ActivityCombinPackageAssoc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $combin_package_id
     * @param integer $activity_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('自组合套餐活动编辑')) {
            return $this->redirect(['site/login']);
        }

        $model = new ActivityCombinPackageAssoc();
        // 获取组合的单品数组
        $pointProductList = ActivityCombinPackageAssoc::getPointProductList();
        // 城市数组
        $mechanismList = Api::getOrgMechanismList();
        // 获取优惠券套餐接口
        $couponPackageList = Api::getCouponGroupValidList();
        foreach ($couponPackageList as &$group) {
            $group['group_name'] = $group['group_id'] . '_' . $group['group_name'];
        }
        unset($group);

        // 获取优惠券接口
        $couponSingleList = Api::activityCombinGetCouponList();
        foreach ($couponSingleList as $couponId => &$couponName) {
            $couponName = $couponId . '_' . $couponName;
        }
        unset($couponName);
        // 修改时的模版数据，也是详情页面的数据
        $updateTemp = ActivityApi::getCombinPackageAssocView($id, 'edit');
        return $this->render('_form', [
            'model'             => $model,
            'pointProductList'  => $pointProductList ? Json::encode($pointProductList) : '{}',
            'singleCouponList'  => $couponSingleList ? Json::encode($couponSingleList) : "{}",
            'packageCouponList' => $couponPackageList ? Json::encode($couponPackageList) : "{}",
            'mechanismList'     => $mechanismList ? Json::encode($mechanismList) : "{}",
            'updateTemp'        => $updateTemp ? Json::encode($updateTemp) : "{}",
            'is_update'         => '2',
        ]);
    }

    /**
     * 接口展示使用，上线删除
     * 获取所有单品信息数组,  组合并返回特定格式。
     * @author  zmy
     * @version 2018-03-27
     * @return  [type]     [description]
     */
    public function actionGetPointProduct()
    {
        return ActivityCombinPackageAssoc::getPointProductList();
    }

    /**
     * 接口展示使用，上线删除
     * 获取城市数组
     * @author  zmy
     * @version 2018-03-27
     * @return  [type]     [description]
     */
    public function actionGetCity()
    {
        return Json::encode(Api::getOrgCityList());
    }
    /**
     * 营销游戏添加日志记录
     * @author zhenggangwei
     * @date   2019-04-16
     * @return [type]     [description]
     */
    public function actionCreateActivityLog()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $type       = Yii::$app->request->get('type');
        $moduleType = Yii::$app->request->get('moduleType');
        $moduleName = '';
        $actionName = '';
        switch ($moduleType) {
            case 1:
                $moduleName = '自组合套餐活动';
                break;
            case 2:
                $moduleName = '拼团活动';
                break;
            case 3:
                $moduleName = '用户筛选任务';
                break;
            case 4:
                $moduleName = '发券任务';
                break;
            case 5:
                $moduleName = '营销游戏';
                break;
            case 6:
                $moduleName = '活动提示语';
                break;
            case 7:
                $moduleName = '周边商城-商品管理';
                $actionName = '商品';
                break;
            case 8:
                $moduleName = '配送区域';
                break;
            case 9:
                $moduleName = '自动回复-问题管理';
                $actionName = '问题';
                break;
            case 10:
                $moduleName = '自动回复-问题类别管理';
                $actionName = '问题类别';
                break;
            case 11:
                $moduleName = '客服系统-客诉记录';
                $actionName = '客诉';
                break;
            default:
                break;
        }
        $actionName = $actionName ? $actionName : $moduleName;
        switch ($type) {
            case 1:
                ManagerLog::saveLog(Yii::$app->user->id, "{$moduleName}", ManagerLog::CREATE, "添加{$actionName}");
                break;
            case 2:
                ManagerLog::saveLog(Yii::$app->user->id, "{$moduleName}", ManagerLog::UPDATE, "编辑{$actionName}");
                break;
            case 3:
                ManagerLog::saveLog(Yii::$app->user->id, "{$moduleName}", ManagerLog::DELETE, "删除{$actionName}");
                break;

            default:
                break;
        }
    }
}
