<?php

namespace backend\controllers;

use backend\models\OrderGoods;
use backend\models\OrderGoodsSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderGoodsController implements the CRUD actions for OrderGoods model.
 */
class OrderGoodsController extends Controller
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
     * Lists all OrderGoods models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('订单商品查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel            = new OrderGoodsSearch();
        $orderGoodsListAndCount = $searchModel->search(Yii::$app->request->queryParams);
        // 列表总数
        $dataProvider      = $orderGoodsListAndCount['orderGoodsList'];
        $totalNumber       = $orderGoodsListAndCount['orderGoodsDataSummary']['totalNumber'];
        $totalFee          = $orderGoodsListAndCount['orderGoodsDataSummary']['totalFee'];
        $sourceID          = $orderGoodsListAndCount['orderGoodsDataSummary']['sourceID'];
        $sourceType        = $orderGoodsListAndCount['orderGoodsDataSummary']['sourceType'];
        $productList       = $orderGoodsListAndCount['orderGoodsDataSummary']['productList'];
        $groupList         = $orderGoodsListAndCount['orderGoodsDataSummary']['groupList'];
        $productActiveList = $orderGoodsListAndCount['orderGoodsDataSummary']['productActiveList'];
        $groupActiveList   = $orderGoodsListAndCount['orderGoodsDataSummary']['groupActiveList'];
        return $this->render('index', [
            'searchModel'       => $searchModel,
            'dataProvider'      => $dataProvider,
            'totalNumber'       => $totalNumber,
            'totalFee'          => $totalFee,
            'sourceID'          => $sourceID,
            'sourceType'        => $sourceType,
            'productList'       => $productList,
            'groupList'         => $groupList,
            'productActiveList' => $productActiveList,
            'groupActiveList'   => $groupActiveList,
        ]);
    }

    /**
     * Displays a single OrderGoods model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OrderGoods model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderGoods();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "订单商品管理", \backend\models\ManagerLog::CREATE, "添加订单商品");
            return $this->redirect(['view', 'id' => $model->goods_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing OrderGoods model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "订单商品管理", \backend\models\ManagerLog::UPDATE, "编辑订单商品");
            return $this->redirect(['view', 'id' => $model->goods_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderGoods model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "订单商品管理", \backend\models\ManagerLog::DELETE, "删除订单商品");
        return $this->redirect(['index']);
    }

    /**
     * Finds the OrderGoods model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderGoods the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderGoods::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     *  订单数据导出
     * @Author   GaoYongli
     * @DateTime 2018-06-07
     * @param    [param]
     * @return   [type]     [description] execl
     */
    public function actionExport()
    {
        if (!Yii::$app->user->can('订单商品导出')) {
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
            ->setCellValue('A1', '名称')
            ->setCellValue('B1', '类型')
            ->setCellValue('C1', '销售单价')
            ->setCellValue('D1', '购买数量')
            ->setCellValue('E1', '优惠价格')
            ->setCellValue('F1', '支付金额')
            ->setCellValue('G1', '支付方式')
            ->setCellValue('H1', '销售日期')
            ->setCellValue('I1', '销售时间')
            ->setCellValue('J1', '付款状态');

        $searchModel                       = new OrderGoodsSearch();
        $arr                               = Yii::$app->request->queryParams;
        $arr['OrderGoodsSearch']['export'] = 1;
        $goodsList                         = OrderGoods::getExportOrderGoodsList($arr);
        $i                                 = 2;
        foreach ($goodsList as $goods) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $goods['source_name'])
                ->setCellValue('B' . $i, $goods['source_type'])
                ->setCellValue('C' . $i, $goods['source_price'] * $goods['source_number'])
                ->setCellValue('D' . $i, $goods['source_number'])
                ->setCellValue('E' . $i, $goods['source_price_discount'])
                ->setCellValue('F' . $i, $goods['actual_pay'])
                ->setCellValue('G' . $i, $goods['pay_type'])
                ->setCellValue('H' . $i, $goods['created_at'])
                ->setCellValue('I' . $i, $goods['created_at'])
                ->setCellValue('J' . $i, $goods['pay_static']);
            $i++;
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "OrderProduct-" . date("Y-m-d") . ".xlsx";
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
