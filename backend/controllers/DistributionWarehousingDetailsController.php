<?php

namespace backend\controllers;

use backend\models\DistributionFiller;
use backend\models\DistributionTask;
use backend\models\Manager;
use backend\models\ScmMaterialType;
use backend\models\ScmStock;
use backend\models\ScmSupplier;
use backend\models\ScmWarehouseOut;
use PHPExcel;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionWarehousingDetailsController extends Controller
{

    /**
     * Lists all DistributionTask models.
     * @return mixed
     */
    public function actionOutWarehouse()
    {
        $param = Yii::$app->request->get('ScmWarehouseOut');
        $query = ScmWarehouseOut::find()->where(['status' => 3])->select('date, author')->groupBy('date, author')->orderBy('date desc');
        // 添加查询条件
        if (!$param || (!$param['startTime'] && !$param['endTime'])) {
            // 默认按开始日期大于当前月1号
            $query->andFilterWhere(['>=', 'date', date('Y-m') . '-01']);
            $query->andFilterWhere(['<=', 'date', date('Y-m-d')]);
        } else {
            if ($param["startTime"]) {
                $query->andFilterWhere(['>=', 'date', $param["startTime"]]);
            }
            if ($param["endTime"]) {
                $query->andFilterWhere(['<=', 'date', $param["endTime"]]);
            }

        }
        // 管理员所在分公司
        $managerOrgId = Manager::getManagerBranchID();
        // 要查询的分公司
        $orgId = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : $managerOrgId;
        if ($orgId > 1) {
            $query->joinWith('user u')->andFilterWhere([
                'u.org_id' => $orgId,
            ]);
        }

        if (isset($param['author'])) {
            $query->andFilterWhere([
                'author' => $param['author'],
            ]);
        }
        // 根据查询条件首先获取指定数目的（人和日期）的数据
        $pages          = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $warehousOutArr = $query->offset($pages->offset)->limit($pages->limit)
            ->all();
        // 计算每种物料的综合
        $warehousingDetails       = ScmWarehouseOut::getWarehouseOutInfo($warehousOutArr);
        $materialSpecificationArr = DistributionFiller::getMaterialTypeSpecificationArr();
        $model                    = new ScmWarehouseOut();
        $model->author            = isset($param['author']) ? $param['author'] : '';
        $model->startTime         = (isset($param['startTime']) && $param['startTime']) ? $param['startTime'] : date('Y-m') . '-01';
        $model->endTime           = (isset($param['endTime']) && $param['endTime']) ? $param['endTime'] : date('Y-m-d');
        $model->orgId             = $orgId;
        return $this->render('index', [
            'model'                    => $model,
            'warehousingDetails'       => $warehousingDetails,
            'materialSpecificationArr' => $materialSpecificationArr,
            'param'                    => $param,
            'pages'                    => $pages,
            'type'                     => 1,
            'managerOrgId'             => $managerOrgId,

        ]);
    }

    /**
     *    查询后返回index主页显示table
     **/
    public function actionInWarehouse()
    {
        $param = Yii::$app->request->get('ScmWarehouseOut');
        $query = ScmStock::find()->where(['reason' => 2])->select(["FROM_UNIXTIME(ctime,'%Y-%m-%d') as date", "distribution_clerk_id"])->groupBy('date, distribution_clerk_id')->orderBy('date desc');
        // 添加查询条件
        if (!$param || (!$param["startTime"] && !$param["endTime"])) {
            // 默认按开始日期大于当前月1号
            $query->andFilterWhere(['>=', "FROM_UNIXTIME(ctime,'%Y-%m-%d')", date('Y-m') . '-01']);
            $query->andFilterWhere(['<=', "FROM_UNIXTIME(ctime,'%Y-%m-%d')", date('Y-m-d')]);
        } else {
            if ($param["startTime"]) {
                $query->andFilterWhere(['>=', "FROM_UNIXTIME(ctime,'%Y-%m-%d')", $param["startTime"]]);
            }
            if ($param["endTime"]) {
                $query->andFilterWhere(['<=', "FROM_UNIXTIME(ctime,'%Y-%m-%d')", $param["endTime"]]);
            }
        }
        // 管理员所在分公司
        $managerOrgId = Manager::getManagerBranchID();
        // 要查询的分公司
        $orgId = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : $managerOrgId;
        if ($orgId > 1) {
            $query->joinWith('user u')->andFilterWhere([
                'u.org_id' => $orgId,
            ]);
        }
        if (isset($param['author'])) {
            $query->andFilterWhere([
                'distribution_clerk_id' => $param['author'],
            ]);
        }
        // 根据查询条件首先获取指定数目的（人和日期）的数据
        $pages       = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $scmStockArr = $query->offset($pages->offset)->limit($pages->limit)
            ->all();
        // 计算每种物料的综合
        $warehousingDetails = ScmWarehouseOut::getScmStockInfo($scmStockArr);
        // 获取标题数组
        $materialSpecificationArr = DistributionFiller::getMaterialTypeSpecificationArr();
        $model                    = new ScmWarehouseOut();
        $model->author            = isset($param['author']) ? $param['author'] : '';
        $model->startTime         = (isset($param['startTime']) && $param['startTime']) ? $param['startTime'] : date('Y-m') . '-01';
        $model->endTime           = (isset($param['endTime']) && $param['endTime']) ? $param['endTime'] : date('Y-m-d');
        $model->orgId             = $orgId;
        return $this->render('index', [
            'model'                    => $model,
            'warehousingDetails'       => $warehousingDetails,
            'materialSpecificationArr' => $materialSpecificationArr,
            'param'                    => $param,
            'pages'                    => $pages,
            'type'                     => 2,
            'managerOrgId'             => $managerOrgId,

        ]);
    }

    /**
     *  Excel 导出
     *
     **/
    public function actionExcelExpord()
    {
        $type                     = Yii::$app->request->get('type');
        $title                    = $type == 1 ? '出库明细表' : '入库明细表';
        $materialSpecificationArr = DistributionFiller::getMaterialTypeSpecificationArr();
        $objPHPExcel              = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords($title)
            ->setCategory($title);

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A1:C1')->setCellValue('A1', '物料名称')
            ->mergeCells('A2:C2')->setCellValue('A2', '单位')
            ->mergeCells('A3:C3')->setCellValue('A3', '规格')
            ->setCellValue('A4', '日期')
            ->setCellValue('B4', '项目')
            ->setCellValue('C4', '经手人');

        $num       = 2; //（字母初始值）
        $letterNum = 2; //起始位置
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

        $param              = Yii::$app->request->get('param');
        $warehousingDetails = ScmWarehouseOut::getWarehousingDetails($param, $type);
        $startDataNum       = 4;
        $numCospan          = 4;
        if ($warehousingDetails) {
            foreach ($warehousingDetails as $date => $authorArr) {
                foreach ($authorArr as $author => $projectArr) {
                    foreach ($projectArr as $project => $detailsVal) {
                        $startDataNum += 1;
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue("A" . $startDataNum, $date)
                            ->setCellValue("B" . $startDataNum, $project)
                            ->setCellValue("C" . $startDataNum, $author);
                        $numRolespan = 2;
                        $numCospan += 1;
                        foreach ($materialSpecificationArr as $specificationKey => $specificationVal) {
                            if (isset($detailsVal[$specificationKey])) {
                                foreach ($specificationVal as $key => $value) {
                                    $numRolespan += 1;
                                    if (isset($detailsVal[$specificationKey][$key])) {
                                        $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, $detailsVal[$specificationKey][$key]);
                                    } else {
                                        $objPHPExcel->setActiveSheetIndex(0)
                                            ->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, 0);
                                    }
                                }
                            } else {
                                foreach ($specificationVal as $key => $value) {
                                    $numRolespan += 1;
                                    $objPHPExcel->setActiveSheetIndex(0)
                                        ->setCellValue(DistributionTask::getExcelConversionLetter($numRolespan) . $numCospan, 0);
                                }
                            }
                        }
                    }
                }
            }
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-" . $title . "-" . date("Y-m-d") . ".xls";
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
