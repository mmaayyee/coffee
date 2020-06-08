<?php
namespace backend\controllers;
use Yii;
use common\models\Api;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\Sale;
use backend\models\SaleSearch;
use yii\helpers\Url;

class SaleController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 销售信息列表
     * @author  tuqiang
     * @version 2017-09-11
     */
    public function actionIndex()
    {   
        if (!Yii::$app->user->can('零售活动人员管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new SaleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {   
        if (!Yii::$app->user->can('零售活动人员添加')) {
            return $this->redirect(['site/login']);
        }
        $model = new Sale();
        $model->setScenario('create');
        $params = Yii::$app->request->post();
        if($params){
            if ($model->load($params) && $model->validate() && $model->saleCreate($params)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "零售活动人员管理", \backend\models\ManagerLog::CREATE, "添加零售活动人员");
                return $this->redirect(['index']);
            } else {
                $model->isNewRecord = 1;
                return $this->render('create', ['model' => $model]);    
            }        
        }else{
            $model->isNewRecord = 1;
            return $this->render('create', ['model' => $model]);    
        }
    }
    /**
     * Creates a new Building model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdate($sale_id)
    {   
        if (!Yii::$app->user->can('零售活动人员修改')) {
            return $this->redirect(['site/login']);
        }
        $model = new Sale();
        $model->setScenario('update');
        $params = Yii::$app->request->post();

        if (!$params) {
            $model = $model->getSaleInfo(array("sale_id" => $sale_id));
            return $this->render('update', ['model' => $model]);
        }

        if ($model->load($params) && $model->validate() && $model->saleUpdate($params)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "零售活动人员管理", \backend\models\ManagerLog::UPDATE, "编辑零售活动人员");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }
    /**
     * 零售人员删除
     * @author  tuqiang 
     * @version 2017-09-07 
     * @param   integer     二维码表的主键id 
     */
    public function actionDelete($sale_id){
        if (!Yii::$app->user->can('零售活动人员删除')) {
            return $this->redirect(['site/login']);
        }
        if(Api::verifySaleDelete(array('sale_id' => $sale_id))){
            return false;
        }else{
            $sale = new Sale();
            $sale->saleDelete(array('sale_id' => $sale_id));
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "零售活动人员管理", \backend\models\ManagerLog::DELETE, "删除零售活动人员");
            return true;
        }
    }
   
}
