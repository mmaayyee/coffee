<?php

namespace frontend\controllers;

use backend\models\BuildingRecord;
use backend\models\PointEvaluation;
use common\models\PointEvaluationApi;
use frontend\models\JSSDK;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PointEvaluationApiController implements the CRUD actions for PointEvaluation model.
 */
class PointEvaluationController extends BaseController
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
     * 创建初始化楼宇名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @return    [type]     [description]
     */
    public function actionGetBuildingNameList()
    {
        $this->returnHeader();
        if (!$this->userinfo['userid']) {
            $recordInfo['error_code'] = 1;
            $recordInfo['msg']        = '当前用户未登陆.';
            return Json::encode($recordInfo);
        }
        $userID   = $this->userinfo['userid'];
        $userInfo = BuildingRecord::getUserRoleAndOrg($userID);
        $orgID    = isset($userInfo['branch']) ? $userInfo['branch'] : '';
        $orgID    = 1;
        if ($orgID > 1) {
            return PointEvaluationApi::getRecordNameList($orgID);
        } else {
            return PointEvaluationApi::getRecordNameList('');
        }
        // 判断进入的角色
        if (isset($userInfo['role'])) {
            if ($userInfo['role'] == 'BD') {
                return BuildingRecord::getBuildingRecordList($userID, $orgID);
            }
            return BuildingRecord::getBuildingRecordList('', $orgID);
        }
        $buildingRecordInfo['error_code'] = 1;
        $buildingRecordInfo['msg']        = '当前登陆用户无数据.';
        return Json::encode($buildingRecordInfo);
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
        $buildTypeID                 = Yii::$app->request->get('build_type_id');
        $promptMessage['error_code'] = 1;
        $promptMessage['msg']        = '当前用户未登陆.';
        if (!$buildTypeID) {
            $promptMessage['msg'] = '请输入指定的渠道类型.';
            return Json::encode($promptMessage);
        }
        if (!$this->userinfo['userid']) {
            return Json::encode($promptMessage);
        }
        $userInfo = BuildingRecord::getUserRoleAndOrg($this->userinfo['userid']);
        // 创建的分公司
        $orgID = $userInfo['branch'];
        // 判断是不是总部登陆
        if ($orgID > 1) {
            // 登陆人的分公司ID
            $buildList = PointEvaluation::getBuildNameList($buildTypeID, $orgID);
        } else {
            $buildList = PointEvaluation::getBuildNameList($buildTypeID, '');
        }
        $buildList = Json::decode($buildList);
        // 因前端需要的数据组装
        if (empty($buildList['error_code'])) {
            $building               = [];
            $building['error_code'] = $buildList['error_code'];
            $building['msg']        = $buildList['msg'];
            $building['data']       = [];
            foreach ($buildList['data'] as $key => $build) {
                $building['data'][$key] = [
                    'id'    => $build['id'],
                    'value' => $build['buildNameStatus'],
                ];
            }
            return Json::encode($building);
        } else {
            return Json::encode($buildList);
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
     * 点位创建 企业微信
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-21
     * @return    [type]     [description]
     */
    public function actionCreate()
    {
        $this->returnHeader();
        $promptMessage['error_code'] = 1;
        $promptMessage['msg']        = '当前用户未登陆.';
        if (!$this->userinfo['userid']) {
            return Json::encode($recordInfo);
        }
        $params = file_get_contents("php://input");
        $params = !$params ? [] : Json::decode($params);
        if (empty($params)) {
            $promptMessage['msg'] = '请写入要创建的信息。';
            return Json::encode($promptMessage);
        }
        $userID   = $this->userinfo['userid'];
        $userInfo = BuildingRecord::getUserRoleAndOrg($userID);
        // 创建的分公司
        $params['org_id'] = $userInfo['branch'];
        // 创建人
        $params['point_applicant'] = $userID;
        $res                       = PointEvaluation::insertPointEvaluation($params);
        $resList                   = Json::decode($res);
        if (!empty($resList['data']['delPicUrlList'])) {
            foreach ($resList['data']['delPicUrlList'] as $picUrl) {
                @unlink(Yii::getAlias("@webroot") . parse_url($picUrl)['path']);
            }
        }
        unset($resList['data']);
        return Json::encode($resList);
    }
    /**
     * 显示点位评分
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @return    [type]     [description]
     */
    public function actionIndex()
    {
        $this->layout = false;
        $this->returnHeader();
        if (!$this->userinfo['userid']) {
            $this->redirect(['/site/error', 'message' => '微信授权失败，请重试']);
        }
        $pointApplicant = $this->userinfo['userid'];
        $userInfo       = BuildingRecord::getUserRoleAndOrg($pointApplicant);
        $pointList      = PointEvaluation::weChatPointList($userInfo, $pointApplicant);
        $pointList      = Json::decode($pointList);
        $agentId        = Yii::$app->params['building_record'];
        $jssdk          = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret'][$agentId]);
        $signPackage    = $jssdk->getSignPackage();
        return $this->render('index', ['data' => Json::encode($pointList['data']), 'signPackage' => $signPackage]);
    }

    /**
     * 添加点位评分
     * @author zhenggangwei
     * @date   2019-02-13
     * @return [type]     [description]
     */
    public function actionCreateView()
    {
        $this->layout = false;
        return $this->render('_form');
    }
    /**
     * 企业微信搜索
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @return    [type]     [description]
     */
    public function actionSearchPointList()
    {
        $this->returnHeader();
        $promptMessage['error_code'] = 1;
        $promptMessage['msg']        = '当前用户未登陆.';
        if (!$this->userinfo['userid']) {
            return Json::encode($promptMessage);
        }
        $params         = file_get_contents("php://input");
        $searchParams   = !$params ? [] : Json::decode($params);
        $pointApplicant = $this->userinfo['userid'];
        $userInfo       = BuildingRecord::getUserRoleAndOrg($pointApplicant);
        if ($userInfo['role'] == 'BD') {
            $searchParams['point_applicant'] = $pointApplicant;
        }
        $searchParams['org_id'] = $userInfo['branch'];
        return PointEvaluation::searchPoint($searchParams);
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
     * Updates an existing PointEvaluation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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

}
