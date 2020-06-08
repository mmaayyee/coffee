<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 16:00
 */
namespace backend\controllers;

use backend\models\TemporaryAuthorization;
use backend\models\TemporaryAuthorizationSearch;
use backend\models\WxMemberSearch;
use common\models\SendNotice;
use yii;
use yii\web\Controller;

class TemporaryAuthorizationController extends Controller
{
    /**
     * 申请临时开门记录列表
     * @author sulingling
     * @dateTime 2018-07-26
     * @version  [version]
     * @return   [type]     [description]
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('门禁卡管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new TemporaryAuthorizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 申请临时开门记录导出
     * @author wangxiwen
     * @version 2018-12-25
     * @return
     */
    public function actionExport()
    {
        if (!Yii::$app->user->can('导出门禁卡临时开门记录')) {
            return $this->redirect(['site/login']);
        }
        $searchModel    = new TemporaryAuthorizationSearch();
        $temporaryArray = $searchModel->exportSearch(Yii::$app->request->queryParams);
        //导出数据
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setTitle("申请临时开门记录");
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20); //所有单元格（列）默认宽度
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '点位名称')
            ->setCellValue('C1', '用户名')
            ->setCellValue('D1', '申请时间')
            ->setCellValue('E1', '审核时间')
            ->setCellValue('F1', '状态');
        foreach ($temporaryArray as $key => $temporary) {
            $applicationDate = $temporary['application_time'] ? date('Y-m-d H:i:s', $temporary['application_time']) : '';
            $auditDate       = $temporary['audit_time'] ? date('Y-m-d H:i:s', $temporary['audit_time']) : '';
            $state           = TemporaryAuthorization::getStatusNameByState($temporary['state'], $temporary['application_time']);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($key + 2), $key + 1)
                ->setCellValue('B' . ($key + 2), $temporary['build_name'])
                ->setCellValue('C' . ($key + 2), $temporary['wx_member_name'])
                ->setCellValue('D' . ($key + 2), $applicationDate)
                ->setCellValue('E' . ($key + 2), $auditDate)
                ->setCellValue('F' . ($key + 2), $state);
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $callStartTime  = microtime(true);
        $outputFileName = "申请临时开门记录" . date("Y-m-d") . ".xlsx";
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="' . $outputFileName . '"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Displays a single TemporaryAuthorization model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('门禁卡管理')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TemporaryAuthorization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('门禁卡管理')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        if ($model->load(yii::$app->request->post())) {
            $data                   = yii::$app->request->post('TemporaryAuthorization');
            $temporaryAuthorization = TemporaryAuthorization::findOne($id);
            if ($temporaryAuthorization->state == 0) {
                $temporaryAuthorization->state      = $data['state'];
                $temporaryAuthorization->audit_time = time();
                $temporaryAuthorization->save();
                $content = $temporaryAuthorization->wx_member_name . '(' . date('Y-m-d H:i:s', time()) . ')(';
                $content .= $temporaryAuthorization->build_name . ")临时开门申请[";
                $content .= $data['state'] == 1 ? "审核通过]" : (($data['state']) == 2 ? "审核未通过" : "请耐心等待") . "]";
                $userUrl = WxMemberSearch::getOne(['name' => $model->wx_member_name]);
                SendNotice::sendWxNotice($userUrl->userid, '', $content, '');
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "门禁卡管理", \backend\models\ManagerLog::UPDATE, "审核临时开门申请");
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the TemporaryAuthorization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return TemporaryAuthorization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TemporaryAuthorization::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
