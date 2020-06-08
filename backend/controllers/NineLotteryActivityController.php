<?php

namespace backend\controllers;

use Yii;
use backend\models\Activity;
use backend\models\ActivitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ActivityApi;
use yii\helpers\Json;
use backend\models\LotteryWinningRecord;
use backend\models\LotteryWinningRecordSearch;
/**
 * ActivityController implements the CRUD actions for Activity model.
 */
class NineLotteryActivityController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * ajax  页面请求，接口获取联动数据
     * @author  zmy
     * @version 2017-11-25
     * @return  [type]     [description]
     */
    public function actionGetNineActivityLinkageJson()
    {
        $awardsNum    = Yii::$app->request->post('awards_num', 0);
        $activityId   = Yii::$app->request->post('activity_id', 0);
        $activityList = ActivityApi::getNineLotteryAwardsSetList($awardsNum, $activityId);
        echo Json::encode($activityList);
    }

}
