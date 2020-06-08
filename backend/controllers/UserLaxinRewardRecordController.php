<?php

namespace backend\controllers;

use backend\models\UserLaxinRewardRecord;
use backend\models\UserLaxinRewardRecordSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserLaxinRewardRecordController implements the CRUD actions for UserLaxinRewardRecord model.
 */
class UserLaxinRewardRecordController extends Controller
{
    public $share_mobile;
    public $bind_time;
    public $bind_mobile;
    public $coupon_group_id;
    public $beans_number;
    public $created_at;
    public $is_register;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['laxin_reward_record_id', 'share_userid', 'laxin_userid', 'beans_number', 'coupon_group_id', 'coupon_number', 'reward_time', 'share_mobile', 'bind_time', 'bind_mobile','group_name','beans_number'], 'integer'],
            [['is_register','created_at'],'safe'],
        ];
    }
    /**
     * 分享者绑定用户列表
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('拉新活动绑定用户列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new UserLaxinRewardRecordSearch();
        $dataProvider = $searchModel->bindSearch(Yii::$app->request->queryParams);

//        echo "<pre>";
//        print_r($dataProvider);
//        die;

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 分享者奖励列表
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionShareReward()
    {
        if (!Yii::$app->user->can('拉新活动奖励列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new UserLaxinRewardRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('share-reward', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single UserLaxinRewardRecord model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the UserLaxinRewardRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserLaxinRewardRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLaxinRewardRecord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
