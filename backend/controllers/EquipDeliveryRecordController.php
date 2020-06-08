<?php

namespace backend\controllers;

use backend\models\EquipDeliveryRecordSearch;
use common\models\EquipDeliveryRecord;
use common\models\Equipments;
use PHPExcel;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * EquipDeliveryRecordController implements the CRUD actions for EquipDeliveryRecord model.
 */
class EquipDeliveryRecordController extends Controller
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
     * Lists all EquipDeliveryRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('楼宇投放记录')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipDeliveryRecordSearch();
        $params       = Yii::$app->request->queryParams;
        $views        = (isset($params['type']) && $params['type'] == 2) ? 'equip_record' : 'index';
        $dataProvider = $searchModel->search($params);
        if (!empty($params['export-btn'])) {
            if ($views == 'index') {
                return $this->exportBuildRecord($dataProvider, $searchModel);
            } else {
                return $this->exportEquipRecord($dataProvider, $searchModel);
            }
        }
        return $this->render($views, [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 导出楼宇投放记录
     * @author wxl
     * @return \yii\web\Response
     * @throws \PHPExcel_Exception
     */
    private function exportBuildRecord($dataProvider, $searchModel)
    {
        $objPHPExcel = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("coffee")
            ->setTitle("楼宇投放记录")
            ->setSubject("楼宇投放记录")
            ->setDescription("楼宇投放记录")
            ->setKeywords("楼宇投放记录")
            ->setCategory("楼宇投放记录");
        if (!empty($dataProvider)) {
            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '楼宇名称')
                ->setCellValue('C1', '楼宇地址')
                ->setCellValue('D1', '分公司')
                ->setCellValue('E1', '设备类型')
                ->setCellValue('F1', '4G流量卡号')
                ->setCellValue('G1', '数量')
                ->setCellValue('H1', '电表')
                ->setCellValue('I1', '外包灯箱')
                ->setCellValue('J1', '定时器型号')
                ->setCellValue('K1', '投放时间')
                ->setCellValue('L1', '撤回时间')
                ->calculateColumnWidths(true);
            $startOption = 1; //横向表头第三个;
            foreach ($dataProvider as $key => $model) {
                if (!isset($model->delivery->is_ammeter)) {
                    continue;
                }
                $startOption += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $startOption, $key + 1)->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("B" . $startOption, empty($model->build) ? '' : $model->build->name)->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("C" . $startOption, $model->build ? $model->build->province . $model->build->city . $model->build->area . $model->build->address : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("D" . $startOption, !empty($model->build) && !empty($searchModel->orgArr[$model->build->org_id]) ? $searchModel->orgArr[$model->build->org_id] : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("E" . $startOption, !empty($model->equip->equipTypeModel) ? $model->equip->equipTypeModel->model : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("F" . $startOption, $model->acceptance ? $model->acceptance->sim_number : '', 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("G" . $startOption, 1, 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("H" . $startOption, $model->delivery->is_ammeter == 1 ? '是' : '否')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("I" . $startOption, isset($model->delivery->lightBox) ? $model->delivery->lightBox->light_box_name : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("J" . $startOption, $model->acceptance ? $model->acceptance->timer_model : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("K" . $startOption, $model->create_time ? date('Y-m-d', $model->create_time) : '', 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("L" . $startOption, $model->un_bind_time ? date('Y-m-d', $model->un_bind_time) : '', 's')->calculateColumnWidths(true);
            }
        } else {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '没有符合条件的数据')->calculateColumnWidths(true);
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-楼宇投放记录-" . date("Y-m-d") . ".xls";
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
    /**
     * 导出设备设备投放记录
     * @author wxl
     * @return \yii\web\Response
     * @throws \PHPExcel_Exception
     */
    private function exportEquipRecord($dataProvider, $searchModel)
    {
        $objPHPExcel = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("coffee")
            ->setTitle("设备投放记录")
            ->setSubject("设备投放记录")
            ->setDescription("设备投放记录")
            ->setKeywords("设备投放记录")
            ->setCategory("设备投放记录");
        if (!empty($dataProvider)) {
            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '设备编号')
                ->setCellValue('C1', '设备类型')
                ->setCellValue('D1', '出厂编号')
                ->setCellValue('E1', '出厂设备型号')
                ->setCellValue('F1', '设备状态')
                ->setCellValue('G1', '供应商')
                ->setCellValue('H1', '分公司')
                ->setCellValue('I1', '楼宇名称')
                ->setCellValue('J1', '数量')
                ->setCellValue('K1', '投放时间')
                ->setCellValue('L1', '撤回时间')
                ->calculateColumnWidths(true);
            $startOption = 1; //横向表头第三个;
            foreach ($dataProvider as $key => $model) {
                if (empty($model->equip) || empty($model->build) || empty($model->delivery)) {
                    continue;
                }
                $startOption += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $startOption, $key + 1)->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("B" . $startOption, $model->equip->equip_code)->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("C" . $startOption, !empty($model->equip->equipTypeModel) ? $model->equip->equipTypeModel->model : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("D" . $startOption, $model->equip->factory_code, 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("E" . $startOption, $model->equip->factory_equip_model, 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("F" . $startOption, $model->equip->equipment_status == Equipments::NORMAL ? '正常' : "故障")->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("G" . $startOption, !empty($model->equip->equipTypeModel->supplier) ? $model->equip->equipTypeModel->supplier->name : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("H" . $startOption, !empty($searchModel->orgArr[$model->build->org_id]) ? $searchModel->orgArr[$model->build->org_id] : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("I" . $startOption, !empty($model->build->name) ? $model->build->name : '')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("J" . $startOption, 1)->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("K" . $startOption, $model->create_time ? date('Y-m-d', $model->create_time) : '', 's')->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit("L" . $startOption, $model->un_bind_time ? date('Y-m-d', $model->un_bind_time) : '', 's')->calculateColumnWidths(true);
            }
        } else {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '没有符合条件的数据')->calculateColumnWidths(true);
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-设备投放记录-" . date("Y-m-d") . ".xls";
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
