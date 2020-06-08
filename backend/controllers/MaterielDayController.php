<?php

namespace backend\controllers;

use backend\models\Manager;
use backend\models\MaterielDay;
use backend\models\MaterielDaySearch;
use common\models\Api;
use common\models\Building;
use PHPExcel;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * MaterielDayController implements the CRUD actions for MaterielDay model.
 */
class MaterielDayController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MaterielDay models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('物料分类消耗统计')) {
            return $this->redirect(['site/login']);
        }

        $searchModel = new MaterielDaySearch();
        $params      = Yii::$app->request->queryParams;
        if ($params) {
            $params['MaterielDaySearch']['orgId'] = !empty($params['MaterielDaySearch']['orgId']) ? $params['MaterielDaySearch']['orgId'] : Manager::getManagerBranchID();
        } else {
            $params['MaterielDaySearch']['orgId'] = Manager::getManagerBranchID();
        }
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'param'        => Yii::$app->request->queryParams,
        ]);
    }

    /**
     * Lists all MaterielDay models.
     * @return mixed
     */
    public function actionIndexBuild()
    {
        if (!Yii::$app->user->can('物料楼宇消耗统计')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new MaterielDaySearch();
        $params      = Yii::$app->request->queryParams;
        if ($params) {
            $params['MaterielDaySearch']['orgId'] = !empty($params['MaterielDaySearch']['orgId']) ? $params['MaterielDaySearch']['orgId'] : Manager::getManagerBranchID();
        } else {
            $params['MaterielDaySearch']['orgId'] = Manager::getManagerBranchID();
        }
        $dataProvider = $searchModel->searchBuild($params);
        return $this->render('index_build', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'param'        => $params,
        ]);
    }
    /**
     * Displays a single MaterielDay model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
        $params      = Yii::$app->request->queryParams;
        $searchModel = new MaterielDay();

        if (isset($params['param']['MaterielDaySearch'])) {
            $searchModel->load(['MaterielDay' => $params['param']['MaterielDaySearch']]);
        }
        if (isset($params['create_at'])) {
            $searchModel->load(['MaterielDay' => array('create_at' => $params['create_at'])]);
        }
        if (isset($params['material_type_id'])) {
            $searchModel->load(['MaterielDay' => array('material_type_id' => $params['material_type_id'])]);
        }
        if (isset($params['MaterielDay'])) {
            $searchModel->load($params);
            $params = $params['MaterielDay'];
        }
        $allArray     = [];
        $parentId     = Manager::getManagerBranchID();
        $allArray     = Api::getOrgIdNameArray(array('parent_path' => $parentId));
        $allOrgIdList = array_keys($allArray);
        if (count($allOrgIdList) == 1) {
            $params['param']['MaterielDaySearch']['orgId'] = $allOrgIdList[0];
        } elseif (count($allOrgIdList) > 1 && isset($params['param']['MaterielDaySearch']['orgId'])) {
            if (!in_array($params['param']['MaterielDaySearch']['orgId'], $allOrgIdList)) {
                exit('没有权限查看！');
            }
        }
        $list = MaterielDay::getMaterielDayInfoByDate($params);
        return $this->render('view', [
            'list'        => $list,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 导出excel
     * @return [type] [description]
     */
    public function actionExcelExport()
    {

        $type        = Yii::$app->request->get('type');
        $searchModel = new MaterielDay();
        $param       = Yii::$app->request->get();
        $param       = isset(Yii::$app->request->queryParams['param']) ? Yii::$app->request->queryParams['param'] : [];
        if ((isset($param['MaterielDaySearch']['startTime']) && !empty($param['MaterielDaySearch']['startTime'])) || (isset($param['MaterielDaySearch']['endTime']) && !empty($param['MaterielDaySearch']['endTime']))) {
            $startTime = strtotime(date('Y-m', strtotime($param['MaterielDaySearch']['startTime'])));
            $endTime   = strtotime(date('Y-m', strtotime($param['MaterielDaySearch']['endTime'])));
            if ($startTime != $endTime) {
                $param['MaterielDaySearch']['startTime'] = 0;
                $param['MaterielDaySearch']['endTime']   = 0;
            } else {
                $param['MaterielDaySearch']['startTime'] = strtotime($param['MaterielDaySearch']['startTime']);
                $param['MaterielDaySearch']['endTime']   = strtotime($param['MaterielDaySearch']['endTime']);
            }
        }
        $param['type'] = $type;
        $allArray      = [];
        $parentId      = Manager::getManagerBranchID();
        $allArray      = Api::getOrgIdNameArray(array('parent_path' => $parentId));
        $allOrgIdList  = array_keys($allArray);
        if (count($allOrgIdList) == 1) {
            $param['MaterielDaySearch']['orgId'] = $allOrgIdList[0];
        } elseif (count($allOrgIdList) > 1 && isset($param['MaterielDaySearch']['orgId'])) {
            if (!in_array($param['MaterielDaySearch']['orgId'], $allOrgIdList)) {
                exit('没有权限导出！');
            }
        }
        $materielDayList = MaterielDay::getMaintainExcelMaterielDay($param);
        $objPHPExcel     = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("coffee")
            ->setTitle("物料消耗物料分类")
            ->setSubject("物料消耗物料分类")
            ->setDescription("物料消耗物料分类")
            ->setKeywords("物料消耗物料分类")
            ->setCategory("物料消耗物料分类");

        if (!empty($materielDayList['materielDayList'])) {

            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '分公司')
                ->setCellValue('B1', '运营模式');

            if ($type == 1) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C1', '付费状态')
                    ->setCellValue('D1', '日期')
                    ->setCellValue('E1', '杯数(个)')
                    ->setCellValue('F1', '付款(元)')
                    ->setCellValue('G1', '咖豆(个)');
                $letter = 72;
            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C1', '日期');
                $letter = 68;
            }
            $countNumber = count($materielDayList['materialType']);
            for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                if ($i < 91) {
                    $col = strtoupper(chr($i));
                } else {
                    $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col . '1', array_shift($materielDayList['materialType'])); //输出大写字母
            }
            $startOption = 1; //横向表头第三个;
            foreach ($materielDayList['materielDayList'] as $key => $value) {
                $startOption += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $startOption, $value['orgName'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("B" . $startOption, $value['onlineName'])->calculateColumnWidths(true);
                if ($type == 1) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("C" . $startOption, $value['paymentState'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("D" . $startOption, $value['time'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("E" . $startOption, $value['number'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("F" . $startOption, $value['actual_fee_all'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("G" . $startOption, $value['beans_num_all'])->calculateColumnWidths(true);
                } else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("C" . $startOption, $value['time'])->calculateColumnWidths(true);
                }
                for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                    if ($i < 91) {
                        $col = strtoupper(chr($i));
                    } else {
                        $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($col . $startOption, array_shift($materielDayList['materielDayList'][$key]['info'])); //输出大写字母
                }
            }
            //定义合计表头
            $startOption += 2;
            $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:B{$startOption}"); // 指定第1行 相邻的列合并
            //设置合计杯数，金额，咖豆
            if ($type == 1) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("E" . $startOption, $materielDayList['finance']['number'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("F" . $startOption, $materielDayList['finance']['actual_fee_all'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("G" . $startOption, $materielDayList['finance']['beans_num_all'])->calculateColumnWidths(true);
            }
            //定义合计物料总值
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$startOption}", '合计');
            for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                if ($i < 91) {
                    $col = strtoupper(chr($i));
                } else {
                    $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col . $startOption, array_shift($materielDayList['materialTypeCount'])); //输出大写字母
            }
            if ($materielDayList['difference']) {
                //定义合计表头
                $startOption += 2;
                if ($type == 1) {
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:E{$startOption}");
                } else {
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:B{$startOption}");
                }
                //定义合计物料总值
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$startOption}", '差异值');
                for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                    if ($i < 91) {
                        $col = strtoupper(chr($i));
                    } else {
                        $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($col . $startOption, array_shift($materielDayList['difference'])); //输出大写字母
                }
            }

        } else {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '没有符合条件的数据')->calculateColumnWidths(true);
        }

        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-物料消耗物料分类统计值-" . date("Y-m-d") . ".xlsx";

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
    /**
     * 导出excel
     * @return [type] [description]
     */
    public function actionExcelExportBuild()
    {
        $type          = Yii::$app->request->get('type');
        $searchModel   = new MaterielDay();
        $param         = isset(Yii::$app->request->queryParams['param']) ? Yii::$app->request->queryParams['param'] : '';
        $param['type'] = $type;
        if ((isset($param['MaterielDaySearch']['startTime']) && !empty($param['MaterielDaySearch']['startTime'])) || (isset($param['MaterielDaySearch']['endTime']) && !empty($param['MaterielDaySearch']['endTime']))) {
            $startTime = strtotime(date('Y-m', strtotime($param['MaterielDaySearch']['startTime'])));
            $endTime   = strtotime(date('Y-m', strtotime($param['MaterielDaySearch']['endTime'])));
            if ($startTime != $endTime) {
                $param['MaterielDaySearch']['startTime'] = 0;
                $param['MaterielDaySearch']['endTime']   = 0;
            } else {
                $param['MaterielDaySearch']['startTime'] = strtotime($param['MaterielDaySearch']['startTime']);
                $param['MaterielDaySearch']['endTime']   = strtotime($param['MaterielDaySearch']['endTime']);
            }
        }
        if (isset($param['MaterielDaySearch']['userId']) && $param['MaterielDaySearch']['userId'] != '') {
            if ($param['MaterielDaySearch']['orgId'] == 1 || $param['MaterielDaySearch']['orgId'] == '') {
                $where = ['distribution_userid' => $param['MaterielDaySearch']['userId']];
            } else {
                $where = ['org_id' => $param['MaterielDaySearch']['orgId'], 'distribution_userid' => $param['MaterielDaySearch']['userId']];
            }
            $param['MaterielDaySearch']['buildList'] = Building::find()->where($where)->select('build_number')->column();
        }
        $allArray     = [];
        $parentId     = Manager::getManagerBranchID();
        $allArray     = Api::getOrgIdNameArray(array('parent_path' => $parentId));
        $allOrgIdList = array_keys($allArray);
        if (count($allOrgIdList) == 1) {
            $param['MaterielDaySearch']['orgId'] = $allOrgIdList[0];
        } elseif (count($allOrgIdList) > 1 && isset($param['MaterielDaySearch']['orgId'])) {
            if (!in_array($param['MaterielDaySearch']['orgId'], $allOrgIdList)) {
                exit('没有权限导出！');
            }
        }
        $materielDayList = MaterielDay::getBuildExcelMaterielDay($param);
        $objPHPExcel     = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("coffee")
            ->setTitle("物料消耗物料分类")
            ->setSubject("物料消耗物料分类")
            ->setDescription("物料消耗物料分类")
            ->setKeywords("物料消耗物料分类")
            ->setCategory("物料消耗物料分类");
        if (!empty($materielDayList['materielDayList'])) {

            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '楼宇名称')
                ->setCellValue('B1', '运营模式')
                ->setCellValue('C1', '日期');
            if ($type == 0) {
                $letter = 69;
            } else {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '杯数(个)')
                    ->setCellValue('E1', '付款(元)')
                    ->setCellValue('F1', '咖豆(个)');
                $letter = 71;
            }
            $countNumber = count($materielDayList['materialType']);
            for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                if ($i < 91) {
                    $col = strtoupper(chr($i));
                } else {
                    $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col . '1', array_shift($materielDayList['materialType'])); //输出大写字母
            }
            $startOption = 1; //横向表头第三个;
            foreach ($materielDayList['materielDayList'] as $key => $value) {
                $startOption += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $startOption, $value['buildName'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("B" . $startOption, $value['onlineName'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("C" . $startOption, $value['time'])->calculateColumnWidths(true);
                if ($type == 1) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("D" . $startOption, $value['number'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("E" . $startOption, $value['actual_fee_all'])->calculateColumnWidths(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("F" . $startOption, $value['beans_num_all'])->calculateColumnWidths(true);
                }
                for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                    if ($i < 91) {
                        $col = strtoupper(chr($i));
                    } else {
                        $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($col . $startOption, array_shift($materielDayList['materielDayList'][$key]['info'])); //输出大写字母
                }

            }
            //定义合计表头
            $startOption += 2;
            $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:B{$startOption}"); // 指定第1行 相邻的列合并
            //设置合计杯数，金额，咖豆
            if ($type == 1) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("D" . $startOption, $materielDayList['finance']['number'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("E" . $startOption, $materielDayList['finance']['actual_fee_all'])->calculateColumnWidths(true);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("F" . $startOption, $materielDayList['finance']['beans_num_all'])->calculateColumnWidths(true);
            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("D" . $startOption, $materielDayList['finance']['number'])->calculateColumnWidths(true);
            }
            //定义合计物料总值
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$startOption}", '合计');
            for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                if ($i < 91) {
                    $col = strtoupper(chr($i));
                } else {
                    $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($col . $startOption, array_shift($materielDayList['materialTypeCount'])); //输出大写字母
            }
            if ($materielDayList['difference']) {
                //定义合计表头
                $startOption += 2;
                if ($type == 1) {
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:E{$startOption}");
                } else {
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$startOption}:B{$startOption}");
                }
                //定义合计物料总值
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$startOption}", '差异值');
                for ($i = $letter; $i < ($letter + $countNumber); $i++) {
                    if ($i < 91) {
                        $col = strtoupper(chr($i));
                    } else {
                        $col = strtoupper(chr(65)) . strtoupper(chr($i - 26));
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($col . $startOption, array_shift($materielDayList['difference'])); //输出大写字母
                }
            }
        } else {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '没有符合条件的数据')->calculateColumnWidths(true);
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-物料消耗物料分类统计值-" . date("Y-m-d") . ".xlsx";

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
    public function actionGetDistributionUserIdNameList()
    {
        $orgId = Yii::$app->request->get('orgId');
        echo json_encode(MaterielDay::getDistributionUserName($orgId));die;
    }
}
