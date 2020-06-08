<?php

namespace backend\controllers;

use Yii;
use backend\models\DistributionWater;
use backend\models\DistributionWaterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\helpers\WXApi\WxMessage;
use common\models\Building;
use common\models\WxMember;
use common\models\Equipments;
use backend\models\ScmSupplier;
use yii\data\Pagination;
use PHPExcel;
use backend\models\DistributionTask;
use backend\models\Manager;

/**
 * DistributionWaterController implements the CRUD actions for DistributionWater model.
 */
class DistributionWaterStatisticsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'search', 'update', 'delete', "excel-export"],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionWater models.
     * @return mixed
     */
    public function actionIndex()
    {   
        if (!Yii::$app->user->can('设备月用水量统计')){
            return $this->redirect(['site/login']);
        }
        // 默认显示所有的数据
        $param = array('DistributionWater'=>array('build_id'=>'', 'completion_date'=>date("Y-m", strtotime("-1 month"))));
        $userId = Yii::$app->user->identity->id;
        $managerModel   =   Manager::find()->where(['id'=>$userId])->one();

        //条件查询
        $data = DistributionWater::distributionWaterStatisticsIndex($managerModel);
        
        // 分页
        $pages= new Pagination(['totalCount' =>$data->count(), 'pageSize' => '20']);
        $buildModel = $data->offset($pages->offset)->limit($pages->limit)->all();
        
        $model = new DistributionWater();
        $model->completion_date =   $param["DistributionWater"]['completion_date'];
        
        $titleDate          =  DistributionWater::getTitleDate($param);
        $waterStatisticsArr =  DistributionWater::getWaterStatisticsArr($buildModel, $param);

        return $this->render('index',[
            'model'    =>  $model,
            'waterStatisticsArr'    =>  $waterStatisticsArr,
            'titleDate' =>  $titleDate,
            'pages'     =>  $pages,
            'param'     =>  $param,
        ]);
    }

    /**
     * 按条件进行搜索
     * @return  [type]     [description]
     */
    public function actionSearch()
    {
        if (!Yii::$app->user->can('设备月用水量统计')){
            return $this->redirect(['site/login']);
        }
        $param  = Yii::$app->request->get();

        $userId = Yii::$app->user->identity->id;
        $managerModel   =   Manager::find()->where(['id'=>$userId])->one();

        // 条件查询
        $data   = DistributionWater::distributionWaterStatisticsSearch($managerModel, $param);
        
        // 分页
        $pages      =   new Pagination(['totalCount' =>$data->count(), 'pageSize' => '20']);
        $buildModel =   $data->offset($pages->offset)->limit($pages->limit)->all();
        $titleDate  =   DistributionWater::getTitleDate($param);
        $waterStatisticsArr = DistributionWater::getWaterStatisticsArr($buildModel, $param);
        $model      =   new DistributionWater();
        
        $model->completion_date =   $param["DistributionWater"]['completion_date'];
        $model->build_id        =   $param["DistributionWater"]['build_id'];
        if($managerModel->branch == 1){
            $model->orgId       =   $param["DistributionWater"]['orgId'] ? $param["DistributionWater"]['orgId'] : "";
        }
        
        return $this->render('index',[
            'waterStatisticsArr'=>  $waterStatisticsArr,
            'titleDate'         =>  $titleDate,
            'model'             =>  $model,
            'pages'             =>  $pages,
            'param'             =>  $param,
        ]);
    }

    /**
     *  Excel 导出
     *
     **/
    public function actionExcelExport(){
        $objPHPExcel = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle("设备月用水量统计")
            ->setSubject("设备月用水量统计")
            ->setDescription("设备月用水量统计")
            ->setKeywords("设备月用水量统计")
            ->setCategory("设备月用水量统计");

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','楼宇名称');
        $i = 1;
        foreach (DistributionWater::getTitleDate($_GET['param']) as $typeKey => $typeVal) {
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(DistributionTask::getExcelConversionLetter($i).'1',$typeVal);
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(DistributionTask::getExcelConversionLetter($i+1).'1','小计');
            $i++;
        }

        //表数据
        if(!empty($_GET['param'])){
            $num = 1;
            $waterStatisticsArr = DistributionWater::getWaterStatisticsExcelArr($_GET["param"]);
            
            foreach ($waterStatisticsArr as $waterStatisticsKey => $waterStaticsVal) {
                $num    +=  count($waterStatisticsKey);
                $totalnum = '';
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter(0).$num,
                        $waterStaticsVal['build_name']);
                $startNum = 0;
                foreach (DistributionWater::getTitleDate($_GET["param"]) as $typeVal) { 
                    $startNum +=    1;
                    $totalnum +=    isset($waterStaticsVal[$typeVal]) ? $waterStaticsVal[$typeVal] : 0;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue(DistributionTask::getExcelConversionLetter($startNum).$num,$waterStaticsVal[$typeVal]);
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue(DistributionTask::getExcelConversionLetter($startNum+1).$num,$totalnum);
                }
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime = microtime(true);
        $outputFileName = "咖啡零点吧-设备月用水量统计-".date("Y-m-d").".xls"; 
        ob_end_clean();
        header("Content-Type: application/force-download"); 
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outputFileName.'"'); 
        header("Content-Transfer-Encoding: binary"); 
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Pragma: no-cache"); 
        $objWriter->save('php://output');
    }

    /**
     * Finds the DistributionWater model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DistributionWater the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionWater::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
