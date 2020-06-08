<?php
namespace backend\controllers;

use backend\models\BuildingSiteStatistics;
use backend\models\BuildingSiteStatisticsSearch;
use common\models\EquipDeliveryRecord;
use Yii;
use yii\web\Controller;

class BuildingSiteStatisticsController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     *
     */
    public function actionIndex()
    {
        //获取检索条件所需数据
        $searchData = BuildingSiteStatistics::getBuildSiteSearchData();
        $params     = Yii::$app->request->queryParams;
        //开始时间和结束时间必填且时间间隔不大于31天
        if (!empty($params['BuildingSiteStatisticsSearch'])) {
            $beginDate = $params['BuildingSiteStatisticsSearch']['beginDate'];
            $endDate   = $params['BuildingSiteStatisticsSearch']['endDate'];
            if (empty($beginDate) || empty($endDate)) {
                Yii::$app->getSession()->setFlash('error', '统计时间(开始)和统计时间(结束)必填');
                return $this->redirect(['building-site-statistics/index']);
                sleep(3);
            }
            $beginTime = strtotime($beginDate);
            $endTime   = strtotime($endDate);
            if ($endTime < $beginTime || ($endTime - $beginTime + 1) / 60 / 60 / 24 > 31) {
                Yii::$app->getSession()->setFlash('error', '统计时间(开始)需小于统计时间(结束)且时间间隔不大于31天');
                return $this->redirect(['building-site-statistics/index']);
                sleep(3);
            }
            //当天时间不可选
            $currentDate = date('Y-m-d');
            if ($beginDate >= $currentDate || $endDate >= $currentDate) {
                Yii::$app->getSession()->setFlash('error', '统计时间(开始)和统计时间(结束)应小于当前日期');
                return $this->redirect(['building-site-statistics/index']);
                sleep(3);
            }
        }
        $searchModel  = new BuildingSiteStatisticsSearch();
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'searchData'   => $searchData,
            'params'       => $params,
        ]);
    }

    /**
     * 导出数据
     * @return [type] [description]
     */
    public function actionExport()
    {
        $params = Yii::$app->request->queryParams;
        //开始时间和结束时间必填且时间间隔不大于31天
        $beginDate = $params['BuildingSiteStatisticsSearch']['beginDate'];
        $endDate   = $params['BuildingSiteStatisticsSearch']['endDate'];
        if (empty($beginDate) || empty($endDate)) {
            Yii::$app->getSession()->setFlash('error', '开始时间和结束时间必填');
            return $this->redirect(['building-site-statistics/index']);
        }
        $beginTime = strtotime($beginDate);
        $endTime   = strtotime($endDate);
        if ($endTime < $beginTime || ($endTime - $beginTime + 1) / 60 / 60 / 24 > 31) {
            Yii::$app->getSession()->setFlash('error', '开始时间需小于结束时间且时间间隔不大于31天');
            return $this->redirect(['building-site-statistics/index']);
        }
        //当天时间不可选
        $currentDate = date('Y-m-d', time());
        if ($beginDate >= $currentDate || $endDate >= $currentDate) {
            Yii::$app->getSession()->setFlash('error', '时间选择应小于当前日期');
            return $this->redirect(['building-site-statistics/index']);
        }
        $searchModel   = new BuildingSiteStatisticsSearch();
        $buildSiteList = $searchModel->exportSearch($params);
        //获取检索时日期范围数组(导出数据时表头使用)
        $dateList            = BuildingSiteStatistics::getDateList($beginDate, $endDate);
        $exportBuildSiteList = BuildingSiteStatistics::getExportDataFormat($dateList, $buildSiteList);
        //表头(点位统计出基础数据列外，还需特殊数据最多31列,因为检索天数最多不能超过31天)
        $header = ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
        //导出数据
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setTitle("楼宇点位统计");
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20); //所有单元格（列）默认宽度
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '点位编号')
            ->setCellValue('B1', '点位名称')
            ->setCellValue('C1', '运营模式')
            ->setCellValue('D1', '所属公司')
            ->setCellValue('E1', '所属城市')
            ->setCellValue('F1', '运营开始时间')
            ->setCellValue('G1', '运营结束时间')
            ->setCellValue('H1', '现设备编号')
            ->setCellValue('I1', '现设备类型');
        foreach ($dateList as $key => $date) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($header[$key] . '1', $date);
        }
        $i = 2;
        foreach ($exportBuildSiteList as $exportBuildSiteArray) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $exportBuildSiteArray['build_number'])
                ->setCellValue('B' . $i, $exportBuildSiteArray['name'])
                ->setCellValue('C' . $i, $exportBuildSiteArray['organization_type'])
                ->setCellValue('D' . $i, $exportBuildSiteArray['org_name'])
                ->setCellValue('E' . $i, $exportBuildSiteArray['org_city'])
                ->setCellValue('F' . $i, $exportBuildSiteArray['create_time'])
                ->setCellValue('G' . $i, $exportBuildSiteArray['un_bind_time'])
                ->setCellValue('H' . $i, $exportBuildSiteArray['equipment_code'])
                ->setCellValue('I' . $i, $exportBuildSiteArray['equipment_type_name']);
            $statisticsList = $exportBuildSiteArray['exportDateList'];
            foreach ($statisticsList as $key => $statisticsArray) {
                //统计数据
                $statistics = '';
                $sign       = 0;
                if (!empty($statisticsArray)) {
                    $statisticsData = explode('|', $statisticsArray);
                    $statistics     = $statisticsData[0];
                    $sign           = $statisticsData[1];
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($header[$key] . $i, $statistics);
                if ($sign == 1) {
                    //设置背景颜色
                    $objPHPExcel->getActiveSheet()->getStyle($header[$key] . $i)->getFill()->setFillType('solid');

                    $objPHPExcel->getActiveSheet()->getStyle($header[$key] . $i)->getFill()->getStartColor()->setARGB('E57373');

                }
            }
            $i++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $callStartTime  = microtime(true);
        $outputFileName = "楼宇点位统计" . date("Y-m-d") . ".xlsx";
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
     * 同步设备运营时间到智能系统
     * @author wangxiwen
     * @version 2018-11-01
     * @return
     */
    public function actionSyncEquipDeliveryRecord()
    {
        //获取设备第一次投放时间
        $equipDeliveryRecord = EquipDeliveryRecord::getEquipDeliveryRecord();
        //同步到智能系统
        $result = BuildingSiteStatistics::syncEquipDeliveryRecord($equipDeliveryRecord);
        if (!$result) {
            echo '同步失败';die;
        }
        echo '同步成功';die;
    }

    /**
     * 同步楼宇运营时间到智能系统
     * @author wangxiwen
     * @version 2018-11-01
     * @return
     */
    public function actionSyncBuildDeliveryRecord()
    {
        //获取设备第一次投放时间
        $buildDeliveryRecord = EquipDeliveryRecord::getBuildDeliveryRecord();
        //同步到智能系统
        $result = BuildingSiteStatistics::syncBuildDeliveryRecord($buildDeliveryRecord);
        if (!$result) {
            echo '同步失败';die;
        }
        echo '同步成功';die;
    }
}
