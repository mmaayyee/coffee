<?php
namespace backend\controllers;

use backend\models\SaleBuildingAssoc;
use backend\models\SaleBuildingAssocSearch;
use common\models\Api;
use dosamigos\qrcode\QrCode;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SaleRegisterCodeController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 二维码列表
     * @author  tuqiang
     * @version 2017-09-12
     * @return  [type]     [description]
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看零售活动人员二维码')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new SaleBuildingAssocSearch();
        $params       = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('生成零售活动人员二维码')) {
            return $this->redirect(['site/login']);
        }
        $model  = new SaleBuildingAssoc();
        $params = Yii::$app->request->post();
        if ($params) {
            if (SaleBuildingAssocSearch::saleBuildingAssocCreate($params)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "零售活动人员二维码管理", \backend\models\ManagerLog::CREATE, "生成零售活动人员二维码");
                return $this->redirect(['index']);
            } else {
                return $this->render('index', [
                    'model' => $model,
                ]);
            }
        } else {
            $model->isNewRecord = 1;
            return $this->render('create', ['model' => $model]);
        }

    }

    /**
     * 二维码生成
     * @author  tuqiang
     * @version 2017-09-06
     * @return  array       图片信息
     */
    public function actionQrcode()
    {
        $buildId = yii::$app->request->get('buildId');
        $saleId  = yii::$app->request->get('saleId');
        if (empty($buildId)) {
            $arr['msg']  = "楼宇不存在";
            $arr['code'] = 1;
            echo json_encode($arr);die;
        }
        if (empty($saleId)) {
            $arr['msg']  = "销售人员不存在";
            $arr['code'] = 1;
            echo json_encode($arr);die;
        }
        $verifyInfo = array('buildId' => $buildId, 'saleId' => $saleId);
        $result     = Api::createSaleBuildingInfoVerify($verifyInfo);
        if ($result) {
            $arr['code'] = 0;
            $arr['src']  = Url::to('@web/uploads/sale-register-qrcode/' . $result['qrcode_img'], true);
            $arr['status']  = 0;
        } else {
            $url                      = yii::$app->params['fcoffeeUrl'] . 'site/login.html?buildID=' . $buildId . '&saleID=' . $saleId;
            $name                     = time() . '-' . $buildId . '-' . $saleId . '.png';
            $outfile                  = './uploads/sale-register-qrcode/';
            $arr['src']               = Url::to('@web/uploads/sale-register-qrcode/' . $name, true);
            $arr['code']              = 0;
            $verifyInfo['qrcode_img'] = $name;
            Api::saleBuildingAssocCreate($verifyInfo);
            QrCode::png($url, $outfile . $name, 0, 10); //调用二维码生成方法
            $arr['status']  = 1;
        }
        echo json_encode($arr);die;

    }
    /**
     * 二维码信息删除
     * @author  tuqiang
     * @version 2017-09-07
     * @param   integer     二维码表的主键id
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除零售活动人员二维码')) {
            return $this->redirect(['site/login']);
        }
        $qrCodeImg = yii::$app->request->get('qrcode_img');
        Api::saleBuildingAssocDelete(array('id' => yii::$app->request->get('id')));
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "零售活动人员二维码管理", \backend\models\ManagerLog::DELETE, "删除零售活动人员二维码");
        @unlink('./images/sale-register-qrcode/' . $qrCodeImg);
        return $this->redirect(['index']);
    }
    /**
     * 列表页二维码图片下载
     * @author  tuqiang
     * @version 2017-09-07
     * @param   string      $filename 图片路径
     */
    public function actionUpload()
    {
        $filename  = yii::$app->request->get('src');
        $file_name = pathinfo($filename)['basename'];
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        $img = file_get_contents($filename);
        echo $img;
    }

    /**
     * Finds the Building model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Building the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Building::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
