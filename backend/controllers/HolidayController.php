<?php

namespace backend\controllers;

use backend\models\Holiday;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * HolidayController implements the CRUD actions for Holiday model.
 */
class HolidayController extends Controller
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
     * 节假日不运维管理.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('节假日不运维管理')) {
            return $this->redirect(['site/login']);
        }
        if ($param = Yii::$app->request->post()) {
            $dateArray = explode(',', $param['exist_date']);
            if (empty($dateArray[0])) {
                Yii::$app->getSession()->setFlash('error', '请选择日期,日期不能为空');
                return $this->redirect(['index']);
            }
            $result = Holiday::addAll($dateArray);
            if (!$result) {
                Yii::$app->getSession()->setFlash('error', '节假日设置失败');
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "节假日不运维管理", \backend\models\ManagerLog::UPDATE, "编辑节假日不运维");
            return $this->redirect(['index']);
        }
        $holiday = Holiday::getFiled('date_day', ['is_holiday' => Holiday::NO_HOLIDAY]);
        $holiday = json_encode($holiday);
        return $this->render('index', [
            'holiday' => $holiday,
        ]);
    }
    /**
     * 节假日管理.
     * @return mixed
     */
    public function actionHoliday()
    {
        if (!Yii::$app->user->can('节假日管理查看')) {
            return $this->redirect(['site/login']);
        }
        $param = Yii::$app->request->post();
        if ($param) {
            $dateArray = explode(',', $param['exist_date']);
            if (empty($dateArray[0])) {
                Yii::$app->getSession()->setFlash('error', '请选择日期,日期不能为空');
                return $this->redirect(['index']);
            }
            $result = Holiday::addHolidayAll($dateArray);
            if (!$result) {
                Yii::$app->getSession()->setFlash('error', '节假日设置失败');
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "节假日管理", \backend\models\ManagerLog::UPDATE, "编辑节假日");
            return $this->redirect(['holiday']);
        }
        $holiday = Holiday::getFiled('date_day', ['in', 'is_holiday', [Holiday::IS_HOLIDAY, Holiday::NO_HOLIDAY]]);
        $holiday = json_encode($holiday);
        return $this->render('holiday', [
            'holiday' => $holiday,
        ]);
    }

    /**
     * Finds the Holiday model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Holiday the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Holiday::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
