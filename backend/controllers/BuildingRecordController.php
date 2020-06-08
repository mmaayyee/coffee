<?php

namespace backend\controllers;

use backend\models\BuildingRecord;
use backend\models\Manager;
use common\models\BuildingRecordApi;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

header("Content-type: text/html; charset=utf-8");

/**
 * BuildingRecordController implements the CRUD actions for BuildingRecord model.
 */
class BuildingRecordController extends Controller
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

    /**
     * 楼宇列表
     * @return mixed
     */
    public function actionIndex()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇列表查看')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('index', ['buildingRecordList' => '{}']);
    }

    /**
     * web端进入初始化楼宇列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-26
     * @return    [type]     [description]
     */
    public function actionList()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇列表查看')) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前登陆的用户无搜索权限.';
            return Json::encode($buildingRecordInfo);
        }
        $orgID = Manager::getManagerBranchID();
        // 判断是不是总部登陆
        if ($orgID > 1) {
            return BuildingRecord::webGetBuildingRecordList($orgID);
        } else {
            return BuildingRecord::webGetBuildingRecordList();
        }
    }
    /**
     * web端楼宇记录搜索
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-04
     * @return    [type]     [description]
     */
    public function actionSearchRecord()
    {
        $this->returnHeader();
        //获取提交的数据
        $recordSearch = file_get_contents("php://input");
        $recordSearch = !$recordSearch ? [] : Json::decode($recordSearch);
        if (empty($recordSearch)) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请输入要搜索的条件的数据';
            return Json::encode($buildingRecordInfo);
        }
        $orgID = Manager::getManagerBranchID();
        // 判断是不是总部登陆
        if ($orgID > 1) {
            $recordSearch['BuildingRecordSearch']['org_id'] = $orgID;
        }
        return BuildingRecordApi::searchRecord($recordSearch);
    }
    /**
     * 创建楼宇
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-19
     * @return    [type]     [description]
     */
    public function actionCreateRecord()
    {
        $this->returnHeader();
        return $this->render('index', ['#' => '/build']);
    }
    /**
     * Displays a single BuildingRecord model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /**
     * 查询详情接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-30
     * @param     [int]     $id [楼宇iD]
     * @return    [json]         [楼宇记录详情]
     */
    public function actionView($id)
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇详情查看')) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前登陆的用户无查看权限.';
            return Json::encode($buildingRecordInfo);
        }
        if (empty($id)) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请输入要查询的楼宇ID.';
            return Json::encode($buildingRecordInfo);
        }
        return BuildingRecord::getBuildingRecordInfo($id);
    }
    // /**
    //  * 楼宇记录渲染页面方法
    //  * @Author:   GaoYongLi
    //  * @DateTime: 2018-11-30
    //  * @param     [int]     $id  [楼宇iD]
    //  * @return    [json]         [楼宇记录详情]
    //  */
    // public function actionInfo($id)
    // {
    //     $this->returnHeader();
    //     if (!Yii::$app->user->can('楼宇详情查看')) {
    //         $buildingRecordInfo['error_code'] = 1;
    //         $buildingRecordInfo['msg']        = '当前登陆的用户无查看权限.';
    //         return Json::encode($buildingRecordInfo);
    //     }
    //     if (empty($id)) {
    //         $buildingRecordInfo['error_code'] = 1;
    //         $buildingRecordInfo['msg']        = '请输入要查询的楼宇ID.';
    //         return Json::encode($buildingRecordInfo);
    //     }
    //     return $this->redirect(['index', '#' => '/detail', 'reocrdID' => $id]);
    // }
    /**
     * pc端后台创建楼宇
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-16
     * @return    [type]     [description]
     */
    public function actionCreate()
    {
        $this->returnHeader();
        $buildingRecordInfo = [];
        if (Yii::$app->user->identity->userid) {
            return BuildingRecord::getCreateBuildingRecord(Yii::$app->user->identity->userid);
        } else {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前用户未登陆.';
            return Json::encode($buildingRecordInfo);
        }
    }

    /**
     * pc端后台更新楼宇
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-16
     * @return    [type]     [description]
     */
    public function actionUpdate($id)
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇修改')) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前登陆的用户无查看权限.';
            return Json::encode($buildingRecordInfo);
        }
        if (empty($id)) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请输入要查询的楼宇ID.';
            return Json::encode($buildingRecordInfo);
        }
        $buildingList = BuildingRecord::updateBuildingRecordInfo($id);
        if (!empty($buildingList['error_code'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '无当前楼宇记录.';
            return Json::encode($buildingRecordInfo);
        }
        return Json::encode($buildingList);
    }
    /**
     * 修改楼宇联系信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-07
     * @return    [type]     [description]
     */
    public function actionUpdateContactInfo()
    {
        $this->returnHeader();
        $contactInfo = file_get_contents("php://input");
        $contactInfo = !$contactInfo ? [] : Json::decode($contactInfo);
        if (empty($contactInfo['id'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请选择要修改联系人的楼宇.';
            return Json::encode($buildingRecordInfo);
        }
        return BuildingRecordApi::updateContactInfo($contactInfo);
    }

    /**
     * 转交方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-30
     * @return    [type]     [description]
     */
    public function actionTransfer()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇转交')) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前登陆的用户无初评权限.';
            return Json::encode($buildingRecordInfo);
        }
        if (!Yii::$app->user->identity->userid || !Yii::$app->user->identity->role) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请登录后再进行操作.';
            return Json::encode($buildingRecordInfo);
        }
        $transferInfo = file_get_contents("php://input");
        $transferInfo = !$transferInfo ? [] : Json::decode($transferInfo);
        if (empty($transferInfo['transferInfo']['new_creator_name'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请选择要转交的创建人.';
            return Json::encode($buildingRecordInfo);
        }
        $roleName  = Yii::$app->user->identity->role;
        $roleID    = Yii::$app->user->identity->userid;
        $newUserId = $transferInfo['transferInfo']['new_creator_name'];
        $transfer  = [
            'transferBuild' => [
                'transferInfo'   => [
                    'new_creator_name' => $newUserId,
                    'role_ame'         => $roleName,
                    'transfer_id'      => $roleID,
                    'org_id'           => Manager::getOrgIDByUser($newUserId),
                ],
                'buildingIDList' => $transferInfo['buildingIDList'],
            ],
        ];
        return BuildingRecordApi::transferBuilding($transfer);
    }
    /**
     * 楼宇初评接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-30
     * @return    [type]     [description]
     */
    public function actionRateBuildingRecord()
    {
        $this->returnHeader();
        if (!Yii::$app->user->can('楼宇初评')) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前登陆的用户无初评权限.';
            return Json::encode($buildingRecordInfo);
        }
        $rateInfo = file_get_contents("php://input");
        $rateInfo = !$rateInfo ? [] : Json::decode($rateInfo);
        if (empty($rateInfo['build_record_id'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '无当前楼宇记录.';
            return Json::encode($buildingRecordInfo);
        }
        if (!Yii::$app->user->identity->userid || !Yii::$app->user->identity->role) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请登录后再进行操作.';
            return Json::encode($buildingRecordInfo);
        }
        $roleName = Yii::$app->user->identity->role;
        $roleID   = Yii::$app->user->identity->userid;
        $info     = [
            'build_record_id' => $rateInfo['build_record_id'],
            'rate_info'       => $rateInfo['rate_info'],
            'rate_status'     => $rateInfo['rate_status'],
            'rate_id'         => $roleID,
            'role_name'       => $roleName,
        ];
        return BuildingRecordApi::rateBuildingRecord($info);
    }

    /**
     * Deletes an existing BuildingRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

}
