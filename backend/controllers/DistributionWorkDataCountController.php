<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionWorkDataCountController extends Controller {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'search', 'create', 'update', 'delete'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     *    查询后返回index主页显示table
     **/
    public function actionIndex() {
        if (!Yii::$app->user->can('配送工作数据统计')) {
            return $this->redirect(['site/login']);
        }
        $param                      = Yii::$app->request->get('DistributionTask');
        $workDataCountArr           = DistributionTask::getTaskData($param);
        $model                      = new DistributionTask();
        $model->start_delivery_time = isset($param['start_delivery_time']) ? $param['start_delivery_time'] : date('Y-m') . '-01';
        $model->end_delivery_time   = isset($param['end_delivery_time']) ? $param['end_delivery_time'] : date('Y-m-d');
        return $this->render('index', [
            'model'            => $model,
            'workDataCountArr' => $workDataCountArr,
        ]);
    }
}
