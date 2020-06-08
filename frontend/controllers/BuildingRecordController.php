<?php

namespace frontend\controllers;

use backend\models\BuildingRecord;
use common\models\BuildingRecordApi;
use frontend\models\JSSDK;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildingRecordController implements the CRUD actions for BuildingRecord model.
 */
class BuildingRecordController extends BaseController
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
     * 企业微信端楼宇记录创建初始化渲染页面
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @return    [type]     [description]
     */
    public function actionIndex()
    {
        $this->layout = false;
        $this->returnHeader();
        $agentId     = Yii::$app->params['building_record'];
        $jssdk       = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret'][$agentId]);
        $signPackage = $jssdk->getSignPackage();
        return $this->render('index', ['signPackage' => $signPackage]);
    }

    /**
     * 创建楼宇记录方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-19
     * @return    [type]     [description]
     */
    public function actionCreateRecord()
    {
        $this->returnHeader();
        //获取提交的数据
        $recordParams = file_get_contents("php://input");
        $recordParams = !$recordParams ? [] : Json::decode($recordParams);
        if (empty($recordParams)) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请输入要提交的数据';
            return Json::encode($buildingRecordInfo);
        }
        if (!empty($recordParams['buildAppearPic'])) {
            $recordParams['buildAppearPic'] = BuildingRecord::uploadRecordImg($recordParams['buildAppearPic']);
        }
        if (!empty($recordParams['buildHallPic'])) {
            $recordParams['buildHallPic'] = BuildingRecord::uploadRecordImg($recordParams['buildHallPic']);
        }
        return BuildingRecord::saveBuildingRecord($recordParams);
    }

    /**
     * 企业微信端楼宇记录列表查看
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @return    [type]     [description]
     */
    public function actionList()
    {
        $this->returnHeader();
        $agentId     = Yii::$app->params['building_record'];
        $jssdk       = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret'][$agentId]);
        $signPackage = $jssdk->getSignPackage();
        return $this->redirect(['index', '#' => '/buildingRecord', 'signPackage' => $signPackage]);
    }
    /**
     * 获取查询的列表数据
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-27
     * @return    [type]     [description]
     */
    public function actionFindRecordList()
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
        $recordSearch['org_id'] = \common\models\WxMember::getOrgIdByUserId($this->userinfo['userid']);
        if (!empty($recordSearch['BuildingRecordSearch']['org_id'])) {
            $recordSearch['BuildingRecordSearch']['org_id'] = $recordSearch['org_id'];
        } else {
            $recordSearch['BuildingRecordSearch']['creator_id'] = $this->userinfo['userid'];
        }
        return BuildingRecordApi::getRecordList($recordSearch);
    }
    /**
     * 查看详情
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-22
     * @param     [type]     $id [description]
     * @return    [type]         [description]
     */
    public function actionView($id)
    {
        $this->returnHeader();
        if (!empty($id)) {
            $buildingList                        = BuildingRecord::getBuildingRecordInfo($id);
            $buildingInfo                        = Json::decode($buildingList)['data'];
            $info['recordInfo']                  = $buildingInfo['recordInfo'];
            $info['recordInfo']['buildRate']     = $buildingInfo['buildRate'];
            $info['recordInfo']['buildTypeList'] = BuildingRecordApi::getBuildTypeList();
            return Json::encode($info);
        }
        $buildingRecordInfo['error_code'] = 1;
        $buildingRecordInfo['msg']        = '无当前楼宇记录.';
        return Json::encode($buildingRecordInfo);
    }

    /**
     * 创建楼宇列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param:    [param]
     * @return
     * @return    [type]     [description]
     */
    public function actionCreate()
    {
        $this->returnHeader();
        if ($this->userinfo['userid']) {
            return BuildingRecord::getCreateBuildingRecord($this->userinfo['userid']);
        } else {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '当前用户未登陆.';
            return Json::encode($buildingRecordInfo);
        }
    }

    /**
     * 修改已经创建的楼宇信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [int]     $id [楼宇ID]
     */
    public function actionUpdate($id)
    {
        $this->returnHeader();
        if (empty($id)) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '请输入要查询的楼宇ID.';
            return Json::encode($buildingRecordInfo);
        }
        $buildingList = BuildingRecord::weChatUpdateRecord($id, $this->userinfo['userid']);
        if (!empty($buildingList['error_code'])) {
            $buildingRecordInfo['error_code'] = 1;
            $buildingRecordInfo['msg']        = '无当前楼宇记录.';
            return Json::encode($buildingRecordInfo);
        }
        if (is_array($buildingList['data'])) {
            $buildingList['data']['buildTypeList'] = BuildingRecordApi::getBuildTypeList();
            return Json::encode($buildingList['data']);
        }
        $buildingRecordInfo['error_code'] = 1;
        $buildingRecordInfo['msg']        = '无当前楼宇记录.';
        return Json::encode($buildingRecordInfo);
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

    /**
     * Finds the BuildingRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BuildingRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BuildingRecord::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
