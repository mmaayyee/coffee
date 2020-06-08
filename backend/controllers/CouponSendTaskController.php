<?php

namespace backend\controllers;

use backend\models\CouponSendTask;
use backend\models\CouponSendTaskSearch;
use backend\models\QuickSendCoupon;
use common\models\CoffeeBackApi;
use common\models\TaskApi;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * CouponSendTasksController implements the CRUD actions for CouponSendTask model.
 */
class CouponSendTaskController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Lists all CouponSendTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('发券任务')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new CouponSendTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CouponSendTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $type = 1)
    {
        if (!Yii::$app->user->can('查看发券任务') && !Yii::$app->user->can('审核发券任务')) {
            return $this->redirect(['site/login']);
        }

        $model = new CouponSendTask();

        $couponSendTaskInfo = TaskApi::getCouponSendTaskView($id);
        return $this->render('view', [
            'couponSendTaskInfo' => $couponSendTaskInfo,
            'model'              => $model,
        ]);
    }

    /**
     * 添加发券任务
     * @author  zmy
     * @version 2018-01-30
     * @return  [type]     [description]
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加发券任务')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('_form', [
            'list' => "{}",
            'id'   => '',
        ]);
    }

    /**
     * 获取优惠券套餐数组
     * @author  zmy
     * @version 2018-02-28
     * @return  [type]     [description]
     */
    public function actionGetCoupon()
    {
        $ret = QuickSendCoupon::getCouponPackage();
        echo Json::encode($ret);
    }

    /**
     * 获取有效优惠券套餐数组
     * @author  wbq
     * @version 2018-06-5
     * @return  [type]     [description]
     */
    public function actionGetValidCouponGroup()
    {
        $ret = QuickSendCoupon::getCouponValidPackage();
        unset($ret[""]);
        foreach ($ret as $id => $coupon) {
            $ret[$id] = $id . '-' . $coupon;
        }
        echo Json::encode($ret);
    }

    /**
     * 获取有效优惠券
     * @author wlw
     * @version 2018-08-31
     * @return  [type]     [description]
     */
    public function actionGetValidCoupon()
    {
        $coupons = QuickSendCoupon::getQuickSendCouponList(['not_intrest' => 1]);
        unset($coupons[""]);
        foreach ($coupons as $id => $coupon) {
            $coupons[$id] = $id . '-' . $coupon;
        }

        echo Json::encode($coupons);
    }

    /**
     * 编辑任务
     * @param integer   $id             任务id
     * @param integer   $sendCouponType 0-导入添加 1-搜索添加
     * @param integer   $copy           0-修改 1-复制
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑发券任务')) {
            return $this->redirect(['site/login']);
        }
        $list = TaskApi::getCouponSendTaskView($id);
        return $this->render('_form', [
            'list' => Json::encode($list),
            'id'   => $id,
        ]);

    }

    /**
     * 审核失败，
     * @author  zmy
     * @version 2018-02-24
     * @return  [type]     [description]
     */
    public function actionAuditCouponSendTaskError()
    {
        if (!Yii::$app->user->can('审核发券任务')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        $data   = [
            'id'              => $params['CouponSendTask']['id'],
            'examine_type'    => 0, // 0-审核失败，1-审核成功
            'examine_opinion' => $params['CouponSendTask']['examine_opinion'],
            'check_status'    => 2,
        ];
        $ret = TaskApi::auditCouponSendTask($data);
        if ($ret) {
            return $this->redirect(['index']);
        }
        Yii::$app->getSession()->setFlash('error', '审核操作失败，请重试');
        return $this->redirect(['view']);
    }

    /**
     * 审核成功，
     * @author  zmy
     * @version 2018-02-24
     * @return  [type]     [description]
     */
    public function actionAuditCouponSendTaskSuccess()
    {

        if (!Yii::$app->user->can('审核发券任务')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->get();
        $data   = [
            'id'              => $params['id'],
            'examine_type'    => 0, // 0-审核失败，1-审核成功
            'examine_opinion' => '',
            'check_status'    => 1, // 1-审核通过 2-审核失败
        ];
        $ret = TaskApi::auditCouponSendTask($data);
        if ($ret) {
            return $this->redirect(['index']);
        }
        Yii::$app->getSession()->setFlash('error', '审核操作失败，请重试');
        return $this->redirect(['view', 'id' => $data['id']]);
    }

    /**
     * 上传审核内容信息 是否通过
     * @author  zmy
     * @version 2018-02-23
     * @param   [type]     $data [description]
     * @return  [type]           [description]
     */
    public function actionAuditCouponSendTask($data)
    {
        if (!Yii::$app->user->can('审核发券任务')) {
            return $this->redirect(['site/login']);
        }
        // $data = [
        //     'id'           => 1,
        //     'examine_type' => 0, // 0-审核失败，1-审核成功
        //     'examine_opinion'=> '不想你通过',
        //     'check_status' => 1,
        // ];
        $ret = TaskApi::couponSendTask($data);
        if ($ret) {
            return $this->redirect(['index']);
        }
        return $this->redirect(['create']);
    }

    // /**
    //  * 删除任务
    //  * @param integer $id
    //  * @return mixed
    //  */
    // public function actionDelete($id)
    // {
    //     if (!Yii::$app->user->can('删除发券任务')) {
    //         return $this->redirect(['site/login']);
    //     }
    //     CoffeeBackApi::delCouponSendTask($id);
    //     return $this->redirect(['index']);
    // }

    // /**
    //  * 审核任务
    //  * @author  zgw
    //  * @version 2017-08-28
    //  * @param integer $ID     任务id
    //  * @return  mixed
    //  */
    // public function actionCheckStatus()
    // {
    //     if (!Yii::$app->user->can('审核发券任务')) {
    //         return $this->redirect(['site/login']);
    //     }
    //     $data = Yii::$app->request->post('CouponSendTask');
    //     if ($data) {
    //         // echo Json::encode($data);die;
    //         CoffeeBackApi::saveCouponSendTask($data);
    //     }
    //     return $this->redirect(['index']);
    // }

    /**
     * 获取楼宇列表
     * @author  zgw
     * @version 2017-08-29
     * @return  array     楼宇列表
     */
    public function actionGetBuildList()
    {
        if (Yii::$app->request->isAjax) {
            $data      = Yii::$app->request->post();
            $buildList = CoffeeBackApi::getBuildList($data);
            return !$buildList ? [] : Json::encode($buildList);
        }
        return [];
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
     * 根据条件获取所有楼宇
     * @author  zgw
     * @version 2017-09-11
     * @return  json     楼宇列表
     */
    public function actionGetAllBuildingByCondition()
    {
        $searchBuildingCondition = Yii::$app->request->post();
        return CoffeeBackApi::getAllBuildingByCondition($searchBuildingCondition);
    }

    /**
     * 获取任务详情
     * @author  zgw
     * @version 2017-08-28
     * @param   integer     $id 任务id
     * @return  object          任务model
     */
    // protected function findModel($id)
    // {
    //     if (($model = CouponSendTask::getCouponSendTaskInfo($id)) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }

    // /**
    //  * 下载文件
    //  * @author  zgw
    //  * @version 2017-09-28
    //  */
    // public function actionDownload()
    // {
    //     $filename = Yii::$app->params['fcoffeeUrl'] . Yii::$app->request->get('filePath', '');
    //     $fileinfo = pathinfo($filename);
    //     header('Content-type: application/octet-stream');
    //     header('Content-Disposition: attachment; filename=' . $fileinfo['basename']);
    //     readfile($filename);
    // }

    // /**
    //  * 获取搜索的楼宇列表
    //  * @author  zgw
    //  * @version 2017-09-28
    //  * @param   integer     $id 楼宇id
    //  */
    // public function actionAddBuildList($id)
    // {
    //     $buildingList = [];
    //     $name         = Yii::$app->request->get('name');
    //     $model        = $this->findModel($id);
    //     $whereString  = Json::decode($model->where_string);
    //     if ($name) {
    //         $buildingList = CoffeeBackApi::getAllBuildingByCondition(['name' => $name]);
    //     } else if ($whereString['buildingIds']) {
    //         $buildingList = CoffeeBackApi::getAllBuildingByCondition(['id' => $whereString['buildingIds']]);
    //     }
    //     $buildingList = !$buildingList ? [] : json_decode($buildingList);
    //     $dataProvider = new ArrayDataProvider([
    //         'allModels' => $buildingList,
    //     ]);
    //     return $this->render('add_build_list', ['dataProvider' => $dataProvider, 'name' => $name, 'id' => $id]);
    // }

    // 文件导出
    public function actionExport($id)
    {
        // 如果文件没有，则无法下载，会进入一个空页面
        $taskInfo    = TaskApi::getCouponSendTaskView($id);
        $fileUrl     = isset($taskInfo['mobile_file_path']) ? $taskInfo['mobile_file_path'] : '';
        $mobileStr   = @file_get_contents($fileUrl);
        $mobileStr   = str_replace(["\r\n", "\r", "\n"], '', $mobileStr);
        $mobileArray = explode(',', $mobileStr);
        $xlsName     = '发券任务：' . $taskInfo['task_name'];
        $xlsCell     = array(
            array('mobile', '手机号码'),
        );
        $list = [];
        for ($i = 0; $i < count($mobileArray); $i++) {
            $list[] = [
                'mobile' => $mobileArray[$i],
            ];
        }
        if ($list !== false && is_array($list) && count($list) > 0) {
            $this->exportExcel($xlsName, $xlsCell, $list);
        }
    }

    /**
     * 导出Excel表格
     * @author Xushijie
     */
    private function exportExcel($expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); //文件名称
        $fileName = $expTitle . date('_YmdHis'); //or $xlsTitle 文件名称可根据自己情况设定
        $cellNum  = count($expCellName);
        $dataNum  = count($expTableData);
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();

        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); //合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  Export time:' . date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        ob_end_clean();
        header('pragma:public');
        //2007
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
