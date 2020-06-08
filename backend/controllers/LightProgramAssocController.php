<?php

namespace backend\controllers;

use Yii;
use backend\models\LightProgramAssoc;
use backend\models\LightProgramAssocSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Api;
/**
 * LightProgramAssocController implements the CRUD actions for LightProgramAssoc model.
 */
class LightProgramAssocController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Displays a single LightProgramAssoc model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * json获取传输的数据，去APi搜索符合条件楼宇
     * @author  zmy
     * @version 2017-06-30
     * @return  [type]     [description]
     */
    public function actionGetSearchBuild()
    {
        $param  =   Yii::$app->request->post();
        $page   =   Yii::$app->request->post('page', 1);
        $pageSize = Yii::$app->request->post("pageSize", 20);
        $programID= Yii::$app->request->post("programID");
        $selectType = Yii::$app->request->post("selectType", 1);
        
        unset($param['page']);
        unset($param['pageSize']);
        unset($param['programID']);
        $ret = json_decode(Api::getSearchBuildByWhere($programID, $param, $page, $pageSize, $selectType), true);
        
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    }



    /**
     * 批量添加楼宇
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionBatchAdd()
    {
        if (!Yii::$app->user->can('批量添加楼宇')) {
            return $this->redirect(['site/login']);
        }
        $id = Yii::$app->request->get('id', 0);
        $param = Yii::$app->request->post();
        if($param)
        {
            $param['programID']    =   $id;
            $ret= Api::saveLightProgramAssoc($param);
            if($ret)
            {
                return $this->redirect(['light-belt-program/index']);
            }
        }
        return $this->render('batch-add', [
            'id'    =>  $id,
            ]);
    }


    /**
     * 批量移除楼宇方案
     * @author  zmy
     * @version 2017-06-27
     * @return  [type]     [description]
     */
    public function actionBatchRemove()
    {
        if (!Yii::$app->user->can('批量移除楼宇')) {
            return $this->redirect(['site/login']);
        }
        $id = Yii::$app->request->get('id', 0);
        $param = Yii::$app->request->post();
        if($param)
        {
            $param['programID'] =   $id;
            $ret = Api::delLightProgramAssoc($param);
            if($ret)
            {
                return $this->redirect(['light-belt-program/index']);
            }
        }
        return $this->render('batch-remove', [
            'id'    =>  $id,
            ]);
    }

    /**
     * Finds the LightProgramAssoc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LightProgramAssoc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LightProgramAssoc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
