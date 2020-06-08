<?php
namespace backend\controllers;

use backend\models\DiscountBuildingAssoc;
use backend\models\DiscountBuildingAssocSearch;
use backend\models\DiscountHolicy;
use backend\models\ManagerLog;
use backend\models\PayTypeApi;
use common\models\Api;
use Yii;
use yii\web\Controller;

class DiscountBuildingAssocController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 优惠列表首页
     * @author  tuqiang
     * @version 2017-09-07
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('优惠楼宇查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel          = new DiscountBuildingAssocSearch();
        $params               = Yii::$app->request->queryParams;
        $dataProvider         = $searchModel->search($params);
        $model                = new Discountholicy();
        $payTypeList          = $model->getPaymentList();
        $buildPayTypeNameList = PayTypeApi::getBuildPayTypeNameList()['data'];
        return $this->render('index', [
            'searchModel'          => $searchModel,
            'dataProvider'         => $dataProvider,
            'buildTypeList'        => Api::getBuildTypeList(),
            'equipTypeList'        => Api::getEquipTypeList(),
            'payTypeList'          => $payTypeList,
            'buildPayTypeNameList' => $buildPayTypeNameList,
        ]);
    }

    /**
     * 添加
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加优惠楼宇')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        if ($params && DiscountBuildingAssoc::disBuildingAssocCreate($params)) {
            ManagerLog::saveLog(Yii::$app->user->id, "楼宇支付方式", ManagerLog::CREATE, '添加楼宇支付方式');
            return $this->redirect(['/build-pay-type/index']);
        } else {
            $model               = new Discountholicy();
            $model->payment_list = $model->getPaymentList();
            $payTypeHolic        = PayTypeApi::getPayTypeHolicy()['data'];
            $disInfo             = PayTypeApi::getDefaultOpenPayType()['data'];
            return $this->render('create', [
                'model'        => $model,
                'payTypeHolic' => $payTypeHolic,
                'disInfo'      => $disInfo,
            ]);
        }
    }

    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUploadCreate()
    {
        if (!Yii::$app->user->can('添加优惠楼宇')) {
            return $this->redirect(['site/login']);
        }
        $csv_filename   = 'upload.txt';
        $dir            = "./tmp-discount-building-excel/";
        $info           = move_uploaded_file($_FILES["upload_file"]["tmp_name"], $dir . $csv_filename);
        $fileConent     = file_get_contents($dir . $csv_filename);
        $content        = mb_convert_encoding($fileConent, "UTF-8", "gb2312,UTF-8");
        $content        = \backend\models\BlackAndWhiteList::clearBom($content);
        $fileConent     = array_filter(preg_split('/[;\r\n]+/s', $content));
        $buildingIDList = DiscountBuildingAssoc::getBuilidingIDList($fileConent);
        $params         = Yii::$app->request->post();
        if ($buildingIDList['id'] && $params) {
            $params['buildingIdArr'] = $buildingIDList['id'];
            if (DiscountBuildingAssoc::disBuildingAssocCreate($params)) {
                ManagerLog::saveLog(Yii::$app->user->id, "楼宇支付方式", ManagerLog::CREATE, '添加楼宇支付方式');
                return $this->redirect(['/build-pay-type/index']);
            } else {
                $model               = new Discountholicy();
                $model->payment_list = $model->getPaymentList();
                return $this->render('create', ['model' => $model]);
            }
        } else {
            $model               = new Discountholicy();
            $model->payment_list = $model->getPaymentList();
            return $this->render('create', ['model' => $model]);
        }
    }
    /**
     * 验证上传文件
     */
    public function actionUploadFileVerify()
    {
        $csv_filename = 'upload.txt';
        $dir          = "./tmp-discount-building-excel/";
        $info         = move_uploaded_file($_FILES["upload_file"]["tmp_name"], $dir . $csv_filename);
        $fileConent   = file_get_contents($dir . $csv_filename);
        $content      = mb_convert_encoding($fileConent, "UTF-8", "gb2312,UTF-8");
        $content      = \backend\models\BlackAndWhiteList::clearBom($content);
        $fileConent   = array_filter(preg_split('/[;\r\n]+/s', $content));
        if (!$fileConent) {
            return json_encode(array('code' => 1, 'msg' => '文件内容不能为空'));
        }
        $buildingIDList = DiscountBuildingAssoc::getBuilidingIDList($fileConent);
        $diffNameArr    = array_unique((array_diff($fileConent, $buildingIDList['name'])));
        if ($diffNameArr) {
            return json_encode(array('code' => 2, 'data' => implode('<br/>', $diffNameArr), 'msg' => '该楼宇不在符合规定之内'));
        }
        return json_encode(array('code' => 0));
    }
    /**
     * 修改
     */
    public function actionUpdate($buildPayTypeId)
    {
        if (!Yii::$app->user->can('优惠楼宇修改')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        if ($params) {
            $params['buildPayTypeId'] = $buildPayTypeId;
            if (DiscountBuildingAssoc::disBuildingAssocCreate($params)) {
                ManagerLog::saveLog(Yii::$app->user->id, "楼宇支付方式", ManagerLog::UPDATE, '编辑楼宇支付方式');
                return $this->redirect(['/build-pay-type/index']);
            }
        } else {
            $model            = new DiscountHolicy();
            $payTypeList      = $model->getPaymentList();
            $buildingList     = $model->getDisBuildingList($buildPayTypeId);
            $disInfo          = PayTypeApi::getBuildPayTypeHolicy($buildPayTypeId);
            $buildPayTypeName = '';
            foreach ($disInfo as $buildPayType) {
                $buildPayTypeName = $buildPayType['build_pay_type_name'];
            }
            $payTypeHolic = PayTypeApi::getPayTypeHolicy()['data'];
            return $this->render('update', [
                'model'            => $model,
                'disInfo'          => $disInfo,
                'buildingList'     => $buildingList,
                'payTypeList'      => $payTypeList,
                'buildPayTypeId'   => $buildPayTypeId,
                'payTypeHolic'     => $payTypeHolic,
                'buildPayTypeName' => $buildPayTypeName,
            ]);
        }
    }
    /**
     * 优惠策略楼宇删除
     * @author  tuqiang
     * @version 2017-09-07
     * @param   integer     策略id
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('优惠楼宇删除')) {
            return $this->redirect(['site/login']);
        }
        Api::DiscountBuildingAssocDelete(array('holicy_id' => $id));
        return $this->redirect(['index']);
    }
    /**
     * json获取传输的数据，去APi搜索符合条件楼宇
     * @author  tuqiang
     * @version 2017-09-08
     * @return  [type]     [description]
     */
    public function actionGetSearchBuild()
    {
        $param    = Yii::$app->request->post();
        $page     = Yii::$app->request->post('page', 1);
        $pageSize = Yii::$app->request->post("pageSize", 20);

        unset($param['page']);
        unset($param['pageSize']);
        $ret = json_decode(Api::getDiscountBuildingListByWhere($param, $page, $pageSize), true);
        echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 根据支付方式id获取对应的优惠列表
     * @author  tuqiang
     * @version 2017-09-08
     * @return  array    [description]
     */
    public function actionGetDisHolicyPaymentList()
    {
        $params['paymentId'] = Yii::$app->request->get('paymentId');
        $discountHolicyList  = Api::getDisHolicyPaymentList($params);
        if ($discountHolicyList) {
            $returnParams['code'] = 0;
            $returnParams['data'] = $discountHolicyList;
        } else {
            $returnParams['code'] = 1;
        }
        echo json_encode($discountHolicyList, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 根据优惠id获取对应的楼宇列表
     * @author  tuqiang
     * @version 2017-09-08
     * @return  array    [description]
     */
    public function actionSelectBuildingList()
    {
        $params['build_pay_type_id'] = Yii::$app->request->get('build_pay_type_id', '');
        $params['buildName']         = Yii::$app->request->get('buildName', '');
        $params['equipType']         = Yii::$app->request->get('equipType', '');
        $params['buildType']         = Yii::$app->request->get('buildType', '');
        $buildingList                = array();
        if ($params) {
            $buildingList['list'] = Api::getDisBuildingEquipTypeList($params);
        }
        if ($buildingList['list']) {
            $buildingList['build_type'] = Api::getBuildTypeList();
            $buildingList['equip_type'] = Api::getEquipTypeList();
            foreach ($buildingList['list'] as $key => $value) {
                $buildingList['list'][$key]['build_type_name'] = isset($buildingList['build_type'][$value['build_type']]) ? $buildingList['build_type'][$value['build_type']] : '';
                $buildingList['list'][$key]['equip_type_name'] = isset($buildingList['equip_type'][$value['equip_type_id']]) ? $buildingList['equip_type'][$value['equip_type_id']] : '';
            }
            $buildingList['code'] = 0;
        } else {
            $buildingList['code'] = 1;
        }
        echo json_encode($buildingList);die;
    }

    /**
     * 根据优惠id获取优惠详情
     * @author  tuqiang
     * @version 2017-09-08
     * @return  array    [description]
     */
    public function actionSelectDiscountDetails()
    {
        $params  = Yii::$app->request->get();
        $disInfo = array();
        if ($params) {
            $model           = new DiscountHolicy();
            $disInfo['list'] = $model->getHolicyInfo($params);
        }
        if ($disInfo['list']) {
            $model       = new DiscountHolicy();
            $payTypeList = $model->getPaymentList();

            $disInfo['list']['holicy_type_name']    = $model->holicy_type_list[$disInfo['list']['holicy_type']];
            $disInfo['list']['holicy_payment_name'] = $payTypeList[$disInfo['list']['holicy_payment']];
            if ($disInfo['list']['holicy_type'] == 1) {
                $disInfo['list']['public_field'] = $disInfo['list']['holicy_price'];
            } elseif ($disInfo['list']['holicy_type'] == 2) {
                $disInfo['list']['public_field'] = intval($disInfo['list']['holicy_price']);
            } else {
                $disInfo['list']['public_field'] = $disInfo['list']['holicy_price'];
            }
            $disInfo['code'] = 0;
        } else {
            $disInfo['code'] = 1;
        }
        echo json_encode($disInfo);die;
    }
}
