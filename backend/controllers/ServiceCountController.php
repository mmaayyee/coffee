<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\ServiceCategory;
use backend\models\ServiceCount;
use backend\models\ServiceCountSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ServiceCountController implements the CRUD actions for ServiceCount model.
 */
class ServiceCountController extends Controller
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
     * Lists all ServiceCount models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('统计查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new ServiceCountSearch();
        $params      = '';
        $categorys   = ServiceCategory::getCategoryList($params);
        $category    = ['' => '请选择问题分类'];
        foreach ($categorys['categoryList'] as $key => $val) {
            $category[$val['id']] = $val['category'];
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        foreach ($dataProvider as $data) {
            if ($data['categoryList'] != '') {
                $list  = $data['categoryList'];
                $pages = new \yii\data\Pagination(['totalCount' => $data['total'], 'defaultPageSize' => '7']);

            }
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'list'        => $list,
            'category'    => $category,
            'pages'       => $pages,
        ]);
    }
    /**
     * 到处统计数据
     */
    public function actionExcelExport()
    {
        if (!Yii::$app->user->can('Excel导出')) {
            return $this->redirect(['site/login']);
        }
        $objPHPExcel = new \PHPExcel();
        //设置文档基本属性
        $objPHPExcel->getProperties()->setCreator("咖啡零点吧")
            ->setLastModifiedBy("zhangmuyu")
            ->setTitle("数据统计")
            ->setSubject("数据统计")
            ->setDescription("数据统计")
            ->setKeywords("数据统计")
            ->setCategory("数据统计");
        // 表头
        $num = 1;
        foreach ($_GET['list'] as $key => $value) {
            $i = 0;
            foreach ($value as $k => $v) {
                if (!isset($value[$k])) {
                    continue;
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue(DistributionTask::getExcelConversionLetter($i) . $num, $value[$k]);
                $i++;
            }
            $num++;
        }
        $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $callStartTime  = microtime(true);
        $outputFileName = "咖啡零点吧-客服数据统计-" . date("Y-m-d") . ".xls";
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
     * Displays a single ServiceCount model.
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
     * Creates a new ServiceCount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ServiceCount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "统计管理", \backend\models\ManagerLog::CREATE, "添加统计信息");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ServiceCount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "统计管理", \backend\models\ManagerLog::UPDATE, "编辑统计信息");
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ServiceCount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "统计管理", \backend\models\ManagerLog::DELETE, "删除统计信息");
        return $this->redirect(['index']);
    }

    /**
     * Finds the ServiceCount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServiceCount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceCount::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
