<?php

namespace backend\controllers;

use backend\models\Manager;
use backend\models\PointEvaluation;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

header("Content-type: text/html; charset=utf-8");

/**
 * PointEvaluationApiController implements the CRUD actions for PointEvaluation model.
 */
class PointEvaluationController extends Controller
{

    public $enableCsrfValidation = false;
    public function returnHeader()
    {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
            exit;
        }
    }
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

    public function actionList()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('点位评估列表查看')) {
            return $this->redirect(['site/login']);
        }
        $orgID = Manager::getManagerBranchID();
        // 判断是不是总部登陆
        if ($orgID > 1) {
            return PointEvaluation::getIndex($orgID);
        } else {
            return PointEvaluation::getIndex('');
        }
    }

    public function actionIndex()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('点位评估列表查看')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('index');
    }

    /**
     * 搜索接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-19
     * @return    [type]     [description]
     */
    public function actionSearchPoint()
    {
        $this->returnHeader();
        $searchPoint = file_get_contents("php://input");
        $searchPoint = !$searchPoint ? [] : Json::decode($searchPoint);
        $orgID       = Manager::getManagerBranchID();
        if (empty($searchPoint['org_id'])) {
            // 判断是不是总部登陆
            if ($orgID > 1) {
                $searchPoint['org_id'] = $orgID;
            }
        }
        return PointEvaluation::webSearchPointList($searchPoint);
    }

    /**
     * 导出接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @return    [type]     [description]
     */
    public function actionExportPoint()
    {
        $this->returnHeader();
        $searchPoint = file_get_contents("php://input");
        $searchPoint = !$searchPoint ? [] : Json::decode($searchPoint);
        $orgID       = Manager::getManagerBranchID();
        if (empty($searchPoint['org_id'])) {
            // 判断是不是总部登陆
            if ($orgID > 1) {
                $searchPoint['org_id'] = $orgID;
            }
        }
        return PointEvaluation::webExportPoint($searchPoint);
    }

    /**
     * 点位评分转交方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-30
     * @return    [type]     [description]
     */
    public function actionTransfer()
    {
        $this->returnHeader();
        $promptMessage = [
            'error_code' => 1,
            'msg'        => '当前登陆的用户无转交权限.',
        ];
        if (!Yii::$app->user->can('点位评估转交')) {
            return Json::encode($promptMessage);
        }
        if (!Yii::$app->user->identity->userid || !Yii::$app->user->identity->role) {
            $promptMessage['msg'] = '请登录后再进行操作.';
            return Json::encode($promptMessage);
        }
        $transferInfo = file_get_contents("php://input");
        $transferInfo = !$transferInfo ? [] : Json::decode($transferInfo);
        if (empty($transferInfo['transferInfo']['new_creator_name'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请选择要转交的创建人.';
            return Json::encode($buildingRecordInfo);
        }
        return PointEvaluation::transferPointList($transferInfo);
    }

    /**
     * web 端创建点位评分需要的渠道类型列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @return    [json]     [渠道类型列表]
     */
    public function actionGetBuildTypeList()
    {
        return PointEvaluation::getBuildTypeList();
    }

    /**
     * web 端创建点位评分 根据选择的渠道类型 返回相应的楼宇名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @return    [json]     [楼宇名称列表]
     */
    public function actionGetBuildList()
    {
        $buildTypeID = Yii::$app->request->get('build_type_id');
        $orgID       = Manager::getManagerBranchID();
        // 判断是不是总部登陆
        if ($orgID > 1) {
            // 登陆人的分公司ID
            return PointEvaluation::getBuildNameList($buildTypeID, $orgID);
        } else {
            // 登陆人的分公司ID
            return PointEvaluation::getBuildNameList($buildTypeID, '');
        }
    }
    /**
     * 选择了指定的楼宇来创建点位评分，返回相应的楼宇详细信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @return    [type]     [description]
     */
    public function actionGetBuildRecordInfo()
    {
        $buildingRecordID = Yii::$app->request->get('record_id');
        if ($buildingRecordID) {
            return PointEvaluation::getCreateBuildRecordInfo($buildingRecordID);
        }
        $buildingRecordInfo['error_code'] = 1;
        $buildingRecordInfo['msg']        = '请选择要创建点位评分的相关楼宇.';
        return Json::encode($buildingRecordInfo);

    }
    /**
     * Displays a single PointEvaluation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->returnHeader();
        return PointEvaluation::getView($id);
    }

    /**
     * 创建接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @return    [type]     [description]
     */
    public function actionCreate()
    {
        $promptMessage = [
            'error_code' => 1,
            'msg'        => '当前登陆的用户无创建权限.',
        ];
        if (!Yii::$app->user->can('点位评估创建')) {
            return Json::encode($promptMessage);
        }
        $this->returnHeader();
        $params = file_get_contents("php://input");
        $params = !$params ? [] : Json::decode($params);
        if (empty($params)) {
            $promptMessage['msg'] = '请写入要创建的信息。';
            return Json::encode($promptMessage);
        }
        // 创建的分公司
        $params['org_id'] = Manager::getManagerBranchID();
        // 创建人
        $params['point_applicant'] = Yii::$app->user->identity->userid;
        $result                    = PointEvaluation::insertPointEvaluation($params);
        $backError                 = $result ? Json::decode($result) : [];
        if (!empty($backError) && !$backError['error_code']) {
            if ($params['point_id']) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "点位评估管理", \backend\models\ManagerLog::UPDATE, "编辑点位评估");
            } else {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "点位评估管理", \backend\models\ManagerLog::CREATE, "添加点位评估");
            }
        }
        return $result;
    }

    /**
     * 点位修改更新
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-21
     * @param     [type]     $id [description]
     * @return    [type]         [description]
     */
    public function actionUpdate()
    {
        $this->returnHeader();
        $pointID = Yii::$app->request->get('point_id');
        if ($pointID) {
            return PointEvaluation::getUpdatePoint($pointID);
        }
        $buildingRecordInfo['error_code'] = 1;
        $buildingRecordInfo['msg']        = '请选择要修改的点位评分记录.';
        return Json::encode($buildingRecordInfo);

    }

    /**
     * 点位评审
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-19
     * @return    [type]     [description]
     */
    public function actionPointApproval()
    {
        $this->returnHeader();
        $promptMessage = [
            'error_code' => 1,
            'msg'        => '当前登陆的用户无审核权限.',
        ];
        if (!Yii::$app->user->can('点位评估审核')) {
            return Json::encode($promptMessage);
        }
        $params = file_get_contents("php://input");
        $params = !$params ? [] : Json::decode($params);
        if (empty($params)) {
            $promptMessage['msg'] = '请填入要审核的信息。';
            return Json::encode($promptMessage);
        }
        return PointEvaluation::pointApproval($params);
    }

    /**
     * Deletes an existing PointEvaluation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "点位评估管理", \backend\models\ManagerLog::DELETE, "删除点位评估");
        return $this->redirect(['index']);
    }

    /**
     * Finds the PointEvaluation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PointEvaluation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PointEvaluation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
