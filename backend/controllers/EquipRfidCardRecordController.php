<?php

namespace backend\controllers;

use backend\models\EquipRfidCardRecord;
use backend\models\EquipRfidCardRecordSearch;
use common\models\Building;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipRfidCardRecordController implements the CRUD actions for EquipRfidCardRecord model.
 */
class EquipRfidCardRecordController extends Controller
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
     * Lists all EquipRfidCardRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipRfidCardRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipRfidCardRecord model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        if (!Yii::$app->user->can('导出门禁卡开门记录')) {
            return $this->redirect(['site/login']);
        }
        //获取人员列表
        $memberArray = WxMember::getUserInfo(0);
        //获取楼宇列表
        $buildArray      = Building::getBuildingArray();
        $searchModel     = new EquipRfidCardRecordSearch();
        $cardRecordArray = $searchModel->exportSearch(Yii::$app->request->queryParams);
        //导出数据
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setTitle("门禁卡开门记录");
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20); //所有单元格（列）默认宽度
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '设备编号')
            ->setCellValue('C1', '门禁卡号')
            ->setCellValue('D1', '点位名称')
            ->setCellValue('E1', '开门人员')
            ->setCellValue('F1', '开门类型')
            ->setCellValue('G1', '开门结果')
            ->setCellValue('H1', '开门时间');
        foreach ($cardRecordArray as $key => $record) {
            $buildName  = $record['build_id'] && !empty($buildArray[$record['build_id']]) ? $buildArray[$record['build_id']] : '';
            $username   = $record['open_people'] && !empty($memberArray[$record['open_people']]) ? $memberArray[$record['open_people']] : '';
            $openType   = $record['open_type'] ? EquipRfidCardRecord::$openType[$record['open_type']] : '';
            $openStatus = $record['is_open_success'] ? EquipRfidCardRecord::$isOpenSuccess[$record['is_open_success']] : '';
            $openDate   = $record['create_time'] ? date('Y-m-d H:i:s', $record['create_time']) : '';
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($key + 2), $key + 1)
                ->setCellValue('B' . ($key + 2), $record['equip_code'])
                ->setCellValue('C' . ($key + 2), $record['rfid_card_code'])
                ->setCellValue('D' . ($key + 2), $buildName)
                ->setCellValue('E' . ($key + 2), $username)
                ->setCellValue('F' . ($key + 2), $openType)
                ->setCellValue('G' . ($key + 2), $openStatus)
                ->setCellValue('H' . ($key + 2), $openDate);
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $callStartTime  = microtime(true);
        $outputFileName = "门禁卡开门记录" . date("Y-m-d") . ".xlsx";
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
     * Creates a new EquipRfidCardRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipRfidCardRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "门禁卡开门记录管理", \backend\models\ManagerLog::CREATE, "添加门禁卡开门记录");

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipRfidCardRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "门禁卡开门记录管理", \backend\models\ManagerLog::UPDATE, "编辑门禁卡开门记录");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipRfidCardRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "门禁卡开门记录管理", \backend\models\ManagerLog::DELETE, "删除门禁卡开门记录");

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipRfidCardRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipRfidCardRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipRfidCardRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
