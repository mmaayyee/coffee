<?php

namespace backend\controllers;

use backend\models\ServiceCategory;
use backend\models\ServiceQuestion;
use backend\models\ServiceQuestionSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ServiceQuestionController implements the CRUD actions for ServiceQuestion model.
 */
class ServiceQuestionController extends Controller
{
    public $question_key;
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
     * Lists all ServiceQuestion models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看问题')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ServiceQuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // 获取分类
        $params    = '';
        $categorys = ServiceCategory::getCategoryList($params);
        $category  = ['' => '请选择问题分类'];
        foreach ($categorys['categoryList'] as $key => $val) {
            $category[$val['id']] = $val['category'];
        }
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'category'     => $category,
        ]);
    }

    /**
     * Displays a single ServiceQuestion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('问题详情')) {
            return $this->redirect(['site/login']);
        }
        $questionid   = Yii::$app->request->get('id', 1);
        $model        = new ServiceQuestion();
        $questionInfo = ServiceQuestion::getQuesitonByID($questionid);
        $question     = $questionInfo['data'];
        $keys         = isset($questionInfo['data']['key']) ? $questionInfo['data']['key'] : [];
        $key          = implode(',', $keys);
        return $this->render('view', [
            'model'    => $model,
            'question' => $question,
            'key'      => $key,
        ]);
    }

    /**
     * Creates a new ServiceQuestion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加问题')) {
            return $this->redirect(['site/login']);
        }
        $model = new ServiceQuestion();
        // 获取分类
        $params    = '';
        $categorys = ServiceCategory::getCategoryList($params);
        $category  = ['' => '请选择问题分类'];
        foreach ($categorys['categoryList'] as $key => $val) {
            if ($val['status'] == 1) {
                $category[$val['id']] = $val['category'];
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "问题管理", \backend\models\ManagerLog::CREATE, "添加问题信息");
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model'    => $model,
            'category' => $category,
        ]);
    }

    /**
     * Updates an existing ServiceQuestion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        if (!Yii::$app->user->can('修改问题')) {
            return $this->redirect(['site/login']);
        }
        $questionID = Yii::$app->request->get('id');
        $question   = ServiceQuestion::getQuestionInfo($questionID);
        foreach ($question['data']['key'] as $key => $value) {
            $questionKey[] = $value['key'];
        }
        $questionKey = implode(',', $questionKey);
        $params      = '';
        $category    = ['' => '请选择问题分类'];
        // 获取分类
        $categorys = ServiceCategory::getCategoryList($params);
        foreach ($categorys['categoryList'] as $key => $val) {
            if ($val['status'] == 1) {
                $category[$val['id']] = $val['category'];
            }
        }
        $model = new ServiceQuestion;
        $model->load($question);
        $model->id           = $question['data']['id'];
        $model->question_key = $questionKey;
        $model->question     = $question['data']['question'];
        $model->answer       = $question['data']['answer'];
        $model->static       = $question['data']['static'];
        $model->s_c_id       = $question['data']['s_c_id'];
        return $this->render('update', ['model' => $model, 'category' => $category]);
    }

    /**
     * Deletes an existing ServiceQuestion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除问题')) {
            return $this->redirect(['site/login']);
        }
        $questionId = Yii::$app->request->post('id');
        $delData    = ServiceQuestion::getDeleteQuestionID($questionId);
        $delResult  = $delData ? Json::decode($delData) : [];
        if (!empty($delResult) && isset($delResult['code'])) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "自动回复-问题管理", \backend\models\ManagerLog::DELETE, "删除问题信息");
        }
    }

    /**
     * Finds the ServiceQuestion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServiceQuestion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceQuestion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
