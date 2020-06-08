<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use backend\models\UserSelectionTask;
use backend\models\UserSelectionTaskSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Api;
use common\models\Equipments;
use backend\models\BuildType;
use common\models\TaskApi;

/**
 * UserSelectionTaskController implements the CRUD actions for UserSelectionTask model.
 */
class UserSelectionTaskController extends Controller
{
    public $enableCsrfValidation = false;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserSelectionTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('用户筛选任务管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new UserSelectionTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Lists all UserSelectionTask models.
     * @return mixed
     */
    public function actionExportUserMoblie()
    {
        if (!Yii::$app->user->can('用户筛选号码导出')) {
            return $this->redirect(['site/login']);
        }
        //接收post数据 处理返回
        $urls = Yii::$app->request->post();
        $urlArray = explode(',', $urls['keys']);
        $list = [];
        foreach ($urlArray as $value) {
            $nameMobileUrl = explode('@@',$value);
            if($nameMobileUrl[1]){
                $content = @file_get_contents(Yii::$app->params['fcoffeeUrl'].$nameMobileUrl[1]);
                $content = str_replace(["\r\n","\r","\n"], '', $content);
                if($content){
                   //执行下载
                    $list[] = [
                        'name'=>$nameMobileUrl[0].'.txt',
                        'content'=>$content,
                    ];
                } 
            }
        }
        if($list){
            return json_encode($list);
        }else{
            return json_encode('n');
        }
    }
    /**
     * Displays a single UserSelectionTask model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('用户筛选任务查看')) {
            return $this->redirect(['site/login']);
        }
        $taskInfo = TaskApi::getUserSelectionTaskInfo($id);
        return $this->render('view',[
            'taskInfo' => $taskInfo,
        ]);
    }

    /**
     * Creates a new UserSelectionTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('用户筛选任务添加')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        // 用户筛选任务中的选择项数据
        $taskOptionsList = UserSelectionTask::getConditionsList();

        return $this->render('_form', [
            'taskOptionsList' => Json::encode($taskOptionsList),
        ]);
    }


    /**
     * 跨域文件上传回调
     * @author  zgw
     * @version 2017-09-13
     * @return  json      验证数据和文件路径
     */
    public function actionVerifyFile()
    {
        return Yii::$app->request->get('back');
    }


    /**
     * Updates an existing UserSelectionTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('用户筛选任务编辑')) {
            return $this->redirect(['site/login']);
        }
        // 获取修改选项
        $taskOptionsList = UserSelectionTask::getConditionsList($id);
        return $this->render('_form', [
            'taskOptionsList' => Json::encode($taskOptionsList),
        ]);
    }
    
    // 文件导出
    public function actionExport($id)
    {
        if (!Yii::$app->user->can('用户筛选号码导出')) {
            return $this->redirect(['site/login']);
        }
        // 如果文件没有，则无法下载，会进入一个空页面
        $taskInfo = TaskApi::getUserSelectionTaskInfo($id);
        $fileUrl =  isset($taskInfo['mobile_file_path']) ? $taskInfo['mobile_file_path'] : '';
        $mobileStr = @file_get_contents($fileUrl);
        $mobileStr = str_replace(["\r\n","\r","\n"], '', $mobileStr);
        $mobileArray = explode(',', $mobileStr);
        $xlsName = $taskInfo['selection_task_name'];
        $xlsCell=array(
                array('mobile','手机号码'),
        );
        $list = [];
        for($i=0;$i<count($mobileArray);$i++){
            $list[] = [
                'mobile'=>$mobileArray[$i]
            ];
        }
        if($list!==false && is_array($list) && count($list)>0){
            $this->exportExcel($xlsName,$xlsCell,$list);
        }
    }

   /**
     * 导出Excel表格
     * @author Xushijie
     */
    private function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();

        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        ob_end_clean();
        header('pragma:public');
        //2007
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
        header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"'); 
        header('Cache-Control: max-age=0');
          
        $objWriter = \PHPExcel_IOFactory:: createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Finds the UserSelectionTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserSelectionTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserSelectionTask::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * 检测是否（手机号，楼宇，公司）合法，
     * @author  zmy
     * @version 2018-02-02
     * @return  [type]     [description]
     */
    public function actionCheckLegal()
    {
        $param = $_POST;
        // 检测类型；
        $checkType =  isset($param['check_type']) ? $param['check_type'] : '';
        if($checkType ){ // 检测类型是否为真
            if ($checkType == 1) {        // 手机号
                $mobile = isset($param['mobile']) ? $param['mobile'] : '';
                return TaskApi::getMobileDetect($mobile);
            }else if ($checkType == 2) { // 楼宇
                $build = isset($param['build_name']) ? $param['build_name'] : '';
                return TaskApi::getBuildDetect($build);
            }else if ($checkType == 3) { // 公司
                $company = isset( $param['company_name']) ? $param['company_name'] : '';
                return TaskApi::getCompanyDetect($company);
            }
        }
        return false;
    }

    /**
     * 通过任务ID，获得任务的条件和逻辑关系
     * @author  zmy
     * @version 2018-01-25
     * @return  [string]     [json]
     */
    public function actionGetWhereByTaskId()
    {
        $taskId   = $_POST['task_id'];
        $taskInfo  = TaskApi::getWhereByTaskId($taskId);
        return Json::encode($taskInfo);
    }
    
    /**
     * 楼宇点位获取
     * @author  zmy
     * @version 2018-02-02
     * @return  [type]     [description]
     * 
     *  $conditionList = [
            'city'       => [],
            'build_type' => [],
            'equip_type' => [],
        ];
     */
    public function actionGetBuildLevelBuildList()
    {
        //$conditionList = $_POST;
        $conditionList = json_decode(file_get_contents('php://input'));
        $ret = TaskApi::getBuildLevelBuildList($conditionList);
        if(!$ret){
            echo "{}";die();
        }
        echo Json::encode($ret);die();
    }
}
