<?php

namespace backend\controllers;

use Yii;
use backend\models\LightBeltProductGroup;
use backend\models\LightBeltProductGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Api;
use yii\data\Pagination;

/**
 * LightBeltProductGroupController implements the CRUD actions for LightBeltProductGroup model.
 */
class LightBeltProductGroupController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'use-progroup-view'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**开始制作： start_make 结束制作：make_product 待机：standby
     * Lists all LightBeltProductGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('饮品组管理')) {
            return $this->redirect(['site/login']);
        }

        $param = Yii::$app->request->queryParams;
        $page  = Yii::$app->request->get('page', 1);

        $model = new LightBeltProductGroup();
        $data  = [];
        if(isset($param['LightBeltProductGroup']) && $param['LightBeltProductGroup']){
            $data["product_group_name"] =   $model->product_group_name  =   $param['LightBeltProductGroup']["product_group_name"];
            $model->choose_product      =   $data["choose_product"]     =   $param['LightBeltProductGroup']['choose_product'];
        }
        $pageSize = 20;
        $productGroupList = json_decode(Api::getLightBeltProGroupArr($data, $pageSize, $page), true);
        $pages = new Pagination(['totalCount' =>$productGroupList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('index',[
            'model' =>  $model,
            'page'  =>  $page,
            'pageSize'=> $pageSize,
            'pages' =>  $pages,
            'productGroupList'   =>  $productGroupList['lightBeltProductGroupArr'],
        ]);
    }

    /**
     * Displays a single LightBeltProductGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        // 接口进行查询
        $productGroupList = ['id'=>2, 'product_group_name'=>'222', 'choose_product'=> '拿铁咖啡,摩卡(含糖),香草卡布奇诺(含糖),热巧克力(含糖)---1,'];
        // $productGroupList = json_decode(Api::getLightBeltProductGroupById($id), true);
        return $this->render('view', [
            'productGroupList'  =>  $productGroupList,
        ]);
    }

    /**
     * 查看 那些场景或方案在使用此饮品组
     * @author  zmy
     * @version 2017-07-13
     * @return  [type]     [description]
     */
    public function actionUseProgroupView()
    {
        if (!Yii::$app->user->can('饮品组使用详情')) {
            return $this->redirect(['site/login']);
        }
        $proGroupID = Yii::$app->request->get("id");
        $useScenarioList = json_decode(Api::getUseScenarioByProGroupId($proGroupID), true);
        if(!$useScenarioList){
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        return $this->render("use-progroup-view", [
            'useScenarioList'   =>  $useScenarioList,
        ]);
    }
    
    /**
     * Creates a new LightBeltProductGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加饮品组')) {
            return $this->redirect(['site/login']);
        }
        $model = new LightBeltProductGroup();

        $productArr =   LightBeltProductGroup::getProductArr();
        $param = Yii::$app->request->post();

        if ($param) {
            // 接口保存饮品组数据
            $productGroupName = $param['LightBeltProductGroup']['product_group_name'] ? $param['LightBeltProductGroup']['product_group_name'] : "";
            $chooseProduct    = $param['LightBeltProductGroup']['choose_product'] ? json_encode($param['LightBeltProductGroup']['choose_product']) : "";
            $data             = ['product_group_name' => $productGroupName, 'choose_product' => $chooseProduct];
            $ret              = Api::saveLightBeltProductGroup($data);
            if ($ret) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "饮品组管理", \backend\models\ManagerLog::CREATE, "添加饮品组名称：" . $productGroupName);
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('error', '添加饮品组失败');
                return $this->render('create', [
                    'model' => $model,
                    'productArr'=> $productArr,
                ]);
            }
        } else {
            return $this->render('_form', [
                'model' => $model,
                'productArr'=> $productArr,
            ]);
        }
    }

    /**
     * Updates an existing LightBeltProductGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑饮品组')) {
            return $this->redirect(['site/login']);
        }

        $model = new LightBeltProductGroup();
        $param = Yii::$app->request->post();

        // 接口查询数组
        $productGroupList = json_decode(Api::getLightBeltProductGroupById($id), true);

        $productArr                = LightBeltProductGroup::getProductArr();
        $model->isNewRecord        = false;
        $model->product_group_name = $productGroupList['product_group_name'];
        if ($param) {
            $productGroupName = $param['LightBeltProductGroup']['product_group_name'] ? $param['LightBeltProductGroup']['product_group_name'] : "";
            $chooseProduct    = $param['LightBeltProductGroup']['choose_product'] ? json_encode($param['LightBeltProductGroup']['choose_product']) : "";

            $data = ['id' => $id, 'product_group_name' => $productGroupName, 'choose_product' => $chooseProduct];
            $ret  = Api::saveLightBeltProductGroup($data);
            if ($ret) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "饮品组管理", \backend\models\ManagerLog::UPDATE, "修改饮品组名称：" . $productGroupName);
                return $this->redirect(['index']);
            }
        }
        return $this->render('_form', [
            'model'         => $model,
            'productArr'    => $productArr,
            "chooseProduct" => $productGroupList['choose_product'],
        ]);

    }

    /**
     * Deletes an existing LightBeltProductGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除饮品组')) {
            return $this->redirect(['site/login']);
        }
        $id = Yii::$app->request->post('id');
        // 删除接口
        $ret = Api::getDelLightBelProductGroupById($id);
        if ($ret == 'true') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "饮品组管理", \backend\models\ManagerLog::DELETE, "删除饮品组");
        }
        echo $ret;
    }

    /**
     * Finds the LightBeltProductGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LightBeltProductGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LightBeltProductGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
