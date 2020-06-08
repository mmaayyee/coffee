<?php

namespace backend\controllers;

use backend\models\DistributionFiller;
use backend\models\DistributionTask;
use backend\models\Manager;
use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use common\models\WxMember;
use PHPExcel;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionMaterialComparisonController extends Controller {
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
        if (!Yii::$app->user->can('物料对比统计')) {
            return $this->redirect(['site/login']);
        }
        $distributionUserList = $pages = '';

        $param = Yii::$app->request->get("DistributionFiller");

        $pages = new Pagination(['totalCount' => 0, 'defaultPageSize' => 20]);

        $orgId = isset($param['orgId']) && $param['orgId'] ? $param : Manager::getManagerBranchID();

        // 添加分页搜索
        if (!isset($param['add_material_author']) || !$param['add_material_author']) {
            // 获取配送员列表
            // 获取管理员所在分公司
            $query = WxMember::find();
            // 获取所有配送人员
            $query->andFilterWhere(['or', ['position' => WxMember::DISTRIBUTION_RESPONSIBLE], ['position' => WxMember::DISTRIBUTION_MEMBER], ['position' => WxMember::DISTRIBUTION_MANAGER]]);

            if ($orgId > 1) {
                $query->andFilterWhere(['org_id' => $orgId]);
            }

            $pages = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);

            $distributionUserList = $query->offset($pages->offset)->limit($pages->limit)->all();
        }
        // 获取数据
        $materialComparisonArr = DistributionFiller::getMaterialComparisonArr($param, $distributionUserList);

        $model = new DistributionFiller();

        $model->add_material_author = isset($param['add_material_author']) ? $param['add_material_author'] : '';
        $model->start_time          = isset($param['start_time']) ? $param['start_time'] : date('Y-m') . '-01';
        $model->end_time            = isset($param['end_time']) ? $param['end_time'] : date('Y-m-d');

        $model->orgId = $orgId;

        // 获取标题
        $materialSpecificationArr = DistributionFiller::getMaterialTypeSpecificationArr();

        return $this->render('index', [
            'model'                    => $model,
            'pages'                    => $pages,
            'materialComparisonArr'    => $materialComparisonArr,
            'materialSpecificationArr' => $materialSpecificationArr,
            'param'                    => $param,
        ]);
    }

    /**
     *  Excel 导出
     *
     **/
    public function actionExcelExpord() {
        $materialSpecificationArr = DistributionFiller::getMaterialTypeSpecificationArr();
        $objPHPExcel              = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle("物料对比表")
            ->setSubject("物料对比表")
            ->setDescription("物料对比表")
            ->setKeywords("物料对比表")
            ->setCategory("物料对比表");

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A1:A4')->setCellValue('A1', '姓名')
            ->mergeCells('B1:B4')->setCellValue('B1', '项目');

        $num       = 1; //（字母初始值）
        $letterNum = 1; //起始位置
        foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {
            $startNum = $letterNum + 1;
            $letterNum += count($materialSpecificationV);
            if ($letterNum > $startNum) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->mergeCells(DistributionTask::getExcelConversionLetter($startNum) . '1:' . DistributionTask::getExcelConversionLetter($letterNum) . '1')
                    ->setCellValue(DistributionTask::getExcelConversionLetter($startNum) . '1', ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $materialSpecificationK])['material_type_name']);
            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($startNum) . '1', ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id' => $materialSpecificationK])['material_type_name']);
            }

            foreach ($materialSpecificationV as $key => $value) {
                $num += count($key);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($num) . '2', ScmSupplier::getSurplierDetail('name', ['id' => $value['supplier_id']])['name'] . '-' . $value['weight']);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($num) . '3', $value['unit']);
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($num) . '4', '数量');
            }

        }
        // 获取导出的查询条件
        $param = Yii::$app->request->get('param');
        // 初始化要导出的人员列表
        $distributionUserList = [];
        // 获取要到导出的人员列表
        if (!isset($param['add_material_author']) || !$param['add_material_author']) {
            // 获取配送员列表
            // 获取管理员所在分公司
            $query = WxMember::find();
            // 获取所有配送人员
            $query->andFilterWhere(['or', ['position' => WxMember::DISTRIBUTION_RESPONSIBLE], ['position' => WxMember::DISTRIBUTION_MEMBER], ['position' => WxMember::DISTRIBUTION_MANAGER]]);

            $orgId = isset($param['orgId']) && $param['orgId'] ? $param : Manager::getManagerBranchID();

            if ($orgId > 1) {
                $query->andFilterWhere(['org_id' => $orgId]);
            }
            $distributionUserList = $query->all();
        }
        // 获取要导出的数据
        $materialComparisonArr = DistributionFiller::getMaterialComparisonArr($param, $distributionUserList, 2);

        // 查询内容 开始A5 B5
        $endCospan   = 4; //结束的初始值
        $numCospan   = 4;
        $initNum     = 1;
        $numRolespan = 1;
        foreach ($materialComparisonArr as $userId => $materialKindArr) {
            $startCospan = $endCospan + 1;
            $endCospan += count($materialKindArr);
            $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A' . $startCospan . ':A' . $endCospan)
                ->setCellValue('A' . $startCospan, WxMember::getWxMemberNameList("*", array('userid' => $userId))['name']);
            //B5  2列
            foreach ($materialKindArr as $materialKind => $materialTypeArr) {
                $numRolespan = 1;
                $numCospan += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($initNum) . $numCospan, $materialKind);

                foreach ($materialSpecificationArr as $specificationKey => $specificationVal) {
                    if (isset($materialTypeArr[$specificationKey])) {
                        foreach ($specificationVal as $key => $value) {
                            $numRolespan += 1;
                            if (isset($materialTypeArr[$specificationKey][$key])) {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, $materialTypeArr[$specificationKey][$key]);
                            } else {
                                $objPHPExcel->setActiveSheetIndex(0)
                                    ->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, 0);
                            }
                        }
                    } else {
                        foreach ($specificationVal as $key => $value) {
                            $numRolespan += 1;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, 0);
                        }
                    }
                }
            }
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-物料对比表-" . date("Y-m-d") . ".xls";
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
