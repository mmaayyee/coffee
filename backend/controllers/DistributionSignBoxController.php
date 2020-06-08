<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\Manager;
use backend\models\ScmMaterialType;
use PHPExcel;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionSignBoxController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     *  分页处理
     *  @param param
     *  @return array
     **/
    public static function getPage($param)
    {
        $query = DistributionTask::find()->select('assign_userid')->groupBy('assign_userid');

        //  条件筛选
        $query->andFilterWhere([
            'is_sue'            => 2,
            'task_type'         => [1, 3],
            'build_id'          => $param['build_id'],
            'end_delivery_date' => $param['end_delivery_date'],
            'assign_userid'     => $param['assign_userid'],
        ]);
        $managerOrgId = Manager::getManagerBranchID();

        $orgId = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : $managerOrgId;
        if ($orgId > 1) {
            $query->joinWith('assignUser u')->andFilterWhere(['u.org_id' => $orgId]);
        }

        $pages          = new Pagination(['totalCount' => $query->count(), 'pageSize' => '20']);
        $assignUserList = $query->offset($pages->offset)->limit($pages->limit)->all();
        $assignUserArr  = ArrayHelper::getColumn($assignUserList, 'assign_userid');

        $distributionTaskModel = DistributionTask::find()->where(['assign_userid' => $assignUserArr, 'end_delivery_date' => $param['end_delivery_date']])->all();

        return array($pages, $distributionTaskModel, $managerOrgId);
    }

    /**
     * Lists all DistributionTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('开箱签到记录')) {
            return $this->redirect(['site/login']);
        }

        $param = array('build_id' => '', 'assign_userid' => '', 'end_delivery_date' => date("Y-m-d", strtotime('-1 day')));
        // 分页
        $pageArr = self::getPage($param);
        $taskArr = DistributionTask::getSignBoxArr($pageArr[1]);

        $model                    = new DistributionTask();
        $model->end_delivery_date = $param['end_delivery_date'];
        $count                    = ScmMaterialType::find()->select(['id', 'material_type_name'])->count();
        return $this->render('index', [
            'model'        => $model,
            'taskArr'      => $taskArr,
            'count'        => $count,
            'param'        => $param,
            'pages'        => $pageArr[0],
            'managerOrgId' => $pageArr[2],
        ]);
    }

    /**
     *    查询后返回index主页显示table
     **/
    public function actionSearch()
    {
        $param = Yii::$app->request->get("DistributionTask");

        $pageArr = self::getPage($param);
        $taskArr = DistributionTask::getSignBoxArr($pageArr[1]);
        $count   = ScmMaterialType::find()->select(['id', 'material_type_name'])->asArray()->count();
        $model   = new DistributionTask();

        $model->end_delivery_date = $param['end_delivery_date'];
        $model->build_id          = $param['build_id'];
        $model->assign_userid     = $param['assign_userid'];
        $model->orgId             = isset($param['orgId']) && $param['orgId'] ? $param['orgId'] : '';

        return $this->render('index', [
            'model'        => $model,
            'taskArr'      => $taskArr,
            'count'        => $count,
            'param'        => $param,
            'pages'        => $pageArr[0],
            'managerOrgId' => $pageArr[2],
        ]);
    }

    /**
     *  Excel 导出
     *
     **/
    public function actionExcelExpord()
    {
        $objPHPExcel = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle("开箱签到表")
            ->setSubject("开箱签到表")
            ->setDescription("开箱签到表")
            ->setKeywords("开箱签到表")
            ->setCategory("开箱签到表");

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A1:A2')->setCellValue('A1', '姓名')
            ->mergeCells('B1:B2')->setCellValue('B1', '楼宇')
            ->mergeCells('C1:C2')->setCellValue('C1', '开箱时间')
            ->mergeCells('D1:D2')->setCellValue('D1', '关箱时间');

        $count       = count(ScmMaterialType::getMaterialTypeArray());
        $startOption = 3; //横向表头第三个;
        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells(DistributionTask::getExcelConversionLetter($startOption + 1) . '1:' . DistributionTask::getExcelConversionLetter($startOption + $count) . '1')
            ->setCellValue(DistributionTask::getExcelConversionLetter($startOption + 1) . '1', '物料添加');
        foreach (ScmMaterialType::getMaterialTypeArray('', 'pieces') as $key => $value) {
            $startOption += 1;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(DistributionTask::getExcelConversionLetter($startOption) . '2', $value);
        }

        if (!empty($_GET['param'])) {
            $taskArr      = DistributionTask::getSignBoxExcelArr($_GET['param']);
            $startDataNum = 2;
            foreach ($taskArr as $taskKey => $taskVal) {
                $startDataNum += 1;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $startDataNum, $taskVal['assign_userid'])
                    ->setCellValue("B" . $startDataNum, $taskVal['build_id'])
                    ->setCellValue("C" . $startDataNum, date("Y-m-d H:i", $taskVal['start_delivery_time']))
                    ->setCellValue("D" . $startDataNum, date("Y-m-d H:i", $taskVal['end_delivery_time']));
                $startDataOption = 3;
                foreach (ScmMaterialType::getMaterialTypeArray() as $typeKey => $typeVal) {
                    $startDataOption += 1;
                    if (isset($taskVal['filler'][$typeKey])) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(DistributionTask::getExcelConversionLetter($startDataOption) . $startDataNum, $taskVal['filler'][$typeKey]);
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue(DistributionTask::getExcelConversionLetter($startDataOption) . $startDataNum, '0');
                    }
                }
            }
        }

        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-开箱签到表-" . date("Y-m-d") . ".xls";
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
