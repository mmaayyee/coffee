<?php

namespace backend\controllers;

use backend\models\GroupBeginTeam;
use backend\models\GroupBeginTeamSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GroupBeginTeamController implements the CRUD actions for GroupBeginTeam model.
 */
class GroupBeginTeamController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all GroupBeginTeam models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('拼团活动客服查询')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new GroupBeginTeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        var_dump($dataProvider);exit;
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupBeginTeam model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('拼团活动客服查询')) {
            return $this->redirect(['site/login']);
        }
        $list = GroupBeginTeam::getOne($id);
        return $this->render('view', [
            'model' => (object) $list['searchModel'][$id],
        ]);
    }
}
