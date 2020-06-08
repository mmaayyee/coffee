<?php

namespace backend\controllers;

use backend\models\CouponSendTask;
use backend\models\CouponSendTaskSearch;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use common\models\TaskApi;
use PHPExcel;
use common\helpers\multiRequest\MutiRequestManager;

/**
 * CouponSendTasksController implements the CRUD actions for CouponSendTask model.
 */
class CouponSendTaskTotalStatisticsController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Lists all CouponSendTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('发券任务统计')) {
            return $this->redirect(['site/login']);
        }
        $param = Yii::$app->request->queryParams;
        $param['CouponSendTaskSearch']['check_status'] =	3;
        $searchModel  = new CouponSendTaskSearch();
        $dataProvider = $searchModel->search($param);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 统计查询条件的优惠券
     * @return unknown
     */
    public function actionStatistics()
    {
        if (!Yii::$app->user->can('发券任务统计')) {
            return $this->redirect(['site/login']);
        }
        
        $param = Yii::$app->request->queryParams;
        $param['CouponSendTaskSearch']['check_status'] =	3;
        
        $statistics  = TaskApi::couponSendTaskStatistics($param);
        
        return $this->asJson($statistics);
    }
   
    /**
     * 通过接口进行获取数据，进行展示。
     * @author  zmy
     * @version 2018-05-24
     * @param   [int]     $id [任务id]
     * @return  []         [展示页面]
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('发券任务统计查看')) {
            return $this->redirect(['site/login']);
        }
        $model = new CouponSendTask();
        $couponSendTaskInfo   = TaskApi::getCouponSendTaskView($id);
        // 获取优惠券统计信息。
        $couponStatisticsInfo = TaskApi::getCouponStatisticsInfo($id);
        // 获取发券单拼统计信息。
        $productStatisticsInfo= TaskApi::getProductStatisticsInfo($id);

        return $this->render('view', [
            'couponSendTaskInfo'    => $couponSendTaskInfo,
            'couponStatisticsInfo'  => $couponStatisticsInfo,
            'productStatisticsInfo' => $productStatisticsInfo,
            'model'                 => $model,
        ]);
    }

    /**
     * 导出Excel expord
     * @author  zmy
     * @version 2018-05-22
     * @return  [type]     [导出的对象类型]
     */
    public function actionExpord($id) 
    {
        if (!Yii::$app->user->can('发券任务统计导出')) {
            return $this->redirect(['site/login']);
        }
        
        $couponSendTaskListMRHandle         = TaskApi::getCouponSendTaskMQHandle($id);
        $couponListMRHandle                 = TaskApi::getCouponStatisticsInfoMQHandle($id);
        $couponSendTaskProductListMRHandle  = TaskApi::getProductStatisticsInfoMQHandle($id);
        
        
        //并行发送请求
        (new MutiRequestManager())
            ->addRequest($couponSendTaskListMRHandle)
            ->addRequest($couponListMRHandle)
            ->addRequest($couponSendTaskProductListMRHandle)
            ->run();
        
        
        if($couponSendTaskListMRHandle->isSuccess()) {
           $couponSendTaskList  = Json::decode($couponSendTaskListMRHandle->getContents());
           $couponSendTaskList['coupon_group_name'] = $couponSendTaskList['coupon_group_name'] ? $couponSendTaskList['coupon_group_name'] : '非套餐券';
        } else {
            $couponSendTaskList = [];
        }
        
        if($couponListMRHandle->isSuccess()) {
            $couponList = Json::decode($couponListMRHandle->getContents());
        } else {
            $couponList = [];
        }
        
        if(!$couponList){
            exit("<script>alert('数据内容为空！');history.go(-1);</script>");
        }
        
        if($couponSendTaskProductListMRHandle->isSuccess()) {
            $couponSendTaskProductList = Json::decode($couponSendTaskProductListMRHandle->getContents());
        } else {
            $couponSendTaskProductList = [];
        }
        
        $couponSendTaskList = TaskApi::getCouponSendTask($id);
        $couponSendTaskList['coupon_group_name'] = $couponSendTaskList['coupon_group_name'] ? $couponSendTaskList['coupon_group_name'] : '非套餐券';
       
        $couponSendTaskCouponList = [
            'coupon_group_name' => $couponSendTaskList['coupon_task_name'],
            'couponList'        => $couponList ? $couponList : [],
        ];

        $objPHPExcel    = new PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setTitle("发券任务统计信息")
            ->setSubject("发券任务统计信息")
            ->setDescription("发券任务统计信息")
            ->setKeywords("发券任务统计信息")
            ->setCategory("发券任务统计信息");

        // 表头
        $objPHPExcel = CouponSendTask::getCouponSendTaskList($objPHPExcel, $couponSendTaskList);
        // 优惠券数据组合统计
        $objPHPExcel = CouponSendTask::getCombinationCouponList($objPHPExcel, $couponSendTaskCouponList);
        // 单品信息数据组装
        $objPHPExcel = CouponSendTask::getCombinationProductList($objPHPExcel, $couponSendTaskProductList, $couponSendTaskList['coupon_task_name']);

        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-发券任务统计表-" . date("Y-m-d") . ".xlsx";
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
