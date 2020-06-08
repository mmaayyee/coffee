<?php

namespace backend\controllers;

use backend\models\DistributionFiller;
use backend\models\DistributionTask;
use backend\models\Manager;
use backend\models\ScmMaterialType;
use common\models\Building;
use PHPExcel;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionMaterialRecordController extends Controller {
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'search', 'create', 'update', 'delete', 'excel-expord'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionTask models.
     * @return mixed
     */
    public function actionIndex() {
        if (!Yii::$app->user->can('物料记录统计')) {
            return $this->redirect(['site/login']);
        }

        $param = array('DistributionFiller' => array('build_id' => '', 'start_time' => date('Y-m') . '-01', 'end_time' => date('Y-m-d')));

        $userId       = Yii::$app->user->identity->id;
        $managerModel = Manager::find()->where(['id' => $userId])->one();
        if ($managerModel->branch == 1) {
            //总公司
            $data = Building::find()->andWhere(['build_status' => Building::SERVED]);
        } else {
            $data = Building::find()->andWhere(['build_status' => Building::SERVED, 'org_id' => $managerModel->branch]);
        }
        // 分页
        $pages      = new Pagination(['totalCount' => $data->count(), 'pageSize' => '20']);
        $buildModel = $data->offset($pages->offset)->limit($pages->limit)->all();

        $taskFillerArr     = DistributionFiller::getMaterialRecordArr($buildModel, $param["DistributionFiller"]);
        $model             = new DistributionFiller();
        $model->start_time = $param["DistributionFiller"]['start_time'];
        $model->build_id   = $param["DistributionFiller"]['build_id'];
        $model->end_time   = $param["DistributionFiller"]['end_time'];

        return $this->render('index', [
            'model'         => $model,
            'taskFillerArr' => $taskFillerArr,
            'param'         => $param,
            'pages'         => $pages,
        ]);
    }

    /**
     *    查询后返回index主页显示table
     **/
    public function actionSearch() {
        $param = Yii::$app->request->get();

        $userId       = Yii::$app->user->identity->id;
        $managerModel = Manager::find()->where(['id' => $userId])->one();
        if ($managerModel->branch == 1) { //总公司
            $param['DistributionFiller']['orgId']   =   $param['DistributionFiller']['orgId'] ? $param['DistributionFiller']['orgId'] : '';
            if ($param['DistributionFiller']['build_id']) {
                $data = Building::find()->andFilterWhere(['build_status' => Building::SERVED, 'id' => $param['DistributionFiller']['build_id'], 'org_id'=> $param['DistributionFiller']['orgId'] ]);
            } else {
                $data = Building::find()->andFilterWhere(['build_status' => Building::SERVED, 'org_id'=> $param['DistributionFiller']['orgId'] ]);
            }
        } else { // 分公司
            if ($param['DistributionFiller']['build_id']) {
                $data = Building::find()->andWhere(['build_status' => Building::SERVED, 'id' => $param['DistributionFiller']['build_id'], 'org_id' => $managerModel->branch]);
            } else {
                $data = Building::find()->andWhere(['build_status' => Building::SERVED, 'org_id' => $managerModel->branch]);
            }
        }

        // 分页
        $pages             = new Pagination(['totalCount' => $data->count(), 'pageSize' => '20']);
        $buildModel        = $data->offset($pages->offset)->limit($pages->limit)->all();
        $taskFillerArr     = DistributionFiller::getMaterialRecordArr($buildModel, $param["DistributionFiller"]);
        $model             = new DistributionFiller();
        $model->start_time = $param["DistributionFiller"]['start_time'];
        $model->build_id   = $param["DistributionFiller"]['build_id'];
        $model->end_time   = $param["DistributionFiller"]['end_time'];
        if($managerModel->branch == 1){
            $model->orgId  = $param['DistributionFiller']['orgId'] ? $param['DistributionFiller']['orgId'] : '';
        }
        return $this->render('index', [
            'model'         => $model,
            'taskFillerArr' => $taskFillerArr,
            'param'         => $param,
            'pages'         => $pages,

        ]);
    }

    /**
     *  Excel 导出
     *
     **/
    public function actionExcelExpord() {
        $objPHPExcel = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle("物料配送记录表")
            ->setSubject("物料配送记录表")
            ->setDescription("物料配送记录表")
            ->setKeywords("物料配送记录表")
            ->setCategory("物料配送记录表");

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '楼宇名称');
        $i = 1;
        foreach (ScmMaterialType::getMaterialTypeArray("", 'pieces') as $typeKey => $typeVal) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(DistributionTask::getExcelConversionLetter($i) . '1', $typeVal);
            $i++;
        }

        //表数据
        if (!empty($_GET['param'])) {
            $num           = 1;
            $taskFillerArr = DistributionFiller::getMaterialRecordExcelArr($_GET['param']);
            foreach ($taskFillerArr as $taskFillerKey => $taskFillerVal) {
                $num += count($taskFillerKey);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter(0) . $num, $taskFillerKey);
                $startNum = 0;
                foreach (ScmMaterialType::getMaterialTypeArray() as $typeKey => $typeVal) {
                    $startNum += 1;
                    if (isset($taskFillerVal[$typeKey])) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(DistributionTask::getExcelConversionLetter($startNum) . $num, $taskFillerVal[$typeKey]);
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(DistributionTask::getExcelConversionLetter($startNum) . $num, 0);
                    }
                }
            }
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-物料配送记录表-" . date("Y-m-d") . ".xls";
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
    }
}
