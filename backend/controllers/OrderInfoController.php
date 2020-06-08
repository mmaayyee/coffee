<?php

namespace backend\controllers;

use backend\models\OrderInfo;
use backend\models\OrderInfoSearch;
use backend\modules\service\helpers\Api;
use common\helpers\multiRequest\MutiRequestManager;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderInfoController implements the CRUD actions for OrderInfo model.
 */
class OrderInfoController extends Controller
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
     * Lists all OrderInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('订单管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel           = new OrderInfoSearch();
        $orderInfoListAndCount = $searchModel->search(Yii::$app->request->queryParams);
        // 列表总数
        $dataProvider     = $orderInfoListAndCount['orderInfoList'];
        $couponIdNameList = OrderInfo::getCouponIdNameList();
        return $this->render('index', [
            'searchModel'      => $searchModel,
            'dataProvider'     => $dataProvider,
            'realPrice'        => $orderInfoListAndCount['realPrice'],
            'totalCups'        => $orderInfoListAndCount['totalCups'],
            'averageCup'       => $orderInfoListAndCount['averageCup'],
            'count'            => $orderInfoListAndCount['count'],
            'couponIdNameList' => $couponIdNameList,
        ]);
    }

    /**
     * Displays a single OrderInfo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        if (!Yii::$app->user->can('订单管理详情查看')) {
            return $this->redirect(['site/login']);
        }
        $orderList   = Yii::$app->request->get();
        $orderInfoID = empty($orderList['id']) ? 0 : $orderList['id'];
        $orderCode   = empty($orderList['order_code']) ? 0 : $orderList['order_code'];
        if ($orderCode != 0) {
            $order   = Api::getOrderlistByCode($orderCode);
            $manager = new MutiRequestManager();
            $manager->addRequest($order);
            $manager->run();
            $orderId     = $order->parseJsonData()['data']['order_id'];
            $orderInfoID = $orderId['order_id'];
        }
        return $this->render('view', [
            'orderInfoID' => $orderInfoID,
        ]);
    }
    public function actionPaymentinfo()
    {
        if (!Yii::$app->user->can('订单支付信息汇总查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new OrderInfoSearch();
        $paymentinfo = $searchModel->paySearch(Yii::$app->request->queryParams);
        return $this->render('paymentinfo', [
            'paymentinfoList' => $paymentinfo['data'],
            'model'           => $searchModel,
        ]);
    }
    public function actionPaymentinfoExport()
    {
        if (!Yii::$app->user->can('订单支付信息汇总导出')) {
            return $this->redirect(['site/login']);
        }
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("michael")
            ->setTitle("销售报表")
            ->setSubject("销售报表")
            ->setDescription("销售报表.")
            ->setKeywords("销售报表")
            ->setCategory("销售报表");
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单原价总金额')
            ->setCellValue('B1', '订单总价总金额')
            ->setCellValue('C1', '用户优惠总金额')
            ->setCellValue('D1', '公司优惠总金额')
            ->setCellValue('E1', '实际支付总金额（含咖豆、优惠券')
            ->setCellValue('F1', '购买总杯数')
            ->setCellValue('G1', '付款总金额')
            ->setCellValue('H1', '咖豆使用总数')
            ->setCellValue('I1', '咖豆抵用总金额')
            ->setCellValue('J1', '咖豆实际价值')
            ->setCellValue('K1', '退款总额')
            ->setCellValue('L1', '优惠券优惠总额')
            ->setCellValue('M1', '活动优惠总额');

        $i           = 2;
        $searchModel = new OrderInfoSearch();
        $paymentinfo = $searchModel->paySearch(Yii::$app->request->queryParams);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $paymentinfo['data']['sourcePrice'])
            ->setCellValue('B' . $i, $paymentinfo['data']['totalFee'])
            ->setCellValue('C' . $i, $paymentinfo['data']['UserDiscountFee'])
            ->setCellValue('D' . $i, $paymentinfo['data']['sourcePriceDiscount'])
            ->setCellValue('E' . $i, $paymentinfo['data']['realPrice'])
            ->setCellValue('F' . $i, $paymentinfo['data']['totalCups'])
            ->setCellValue('G' . $i, $paymentinfo['data']['actualFee'])
            ->setCellValue('H' . $i, $paymentinfo['data']['beansNum'])
            ->setCellValue('I' . $i, $paymentinfo['data']['beansAmount'])
            ->setCellValue('J' . $i, $paymentinfo['data']['beansRealAmount'])
            ->setCellValue('K' . $i, $paymentinfo['data']['userRefundPrice'])
            ->setCellValue('L' . $i, $paymentinfo['data']['couponRealValue'])
            ->setCellValue('M' . $i, $paymentinfo['data']['orderActivityPrice'])
        ;
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "orderPay-" . date("Y-m-d") . ".xlsx";
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
