<?php

namespace backend\controllers;

use backend\models\EquipmentProductGroup;
use backend\models\EquipmentProductGroupSearch;
use backend\models\ManagerLog;
use common\models\Api;
use common\models\CoffeeBackApi;
use common\models\EquipProductGroupApi;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProductGroupController implements the CRUD actions for ProductGroup model.
 */
class EquipmentProductGroupController extends Controller
{
    /**
     * Lists all ProductGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('产品组管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipmentProductGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        list($grouStoIdNameList, $grouStoIdEtypeNameList) = EquipmentProductGroup::proGroupStockList();
        return $this->render('index', [
            'searchModel'            => $searchModel,
            'dataProvider'           => $dataProvider,
            'grouStoIdNameList'      => $grouStoIdNameList,
            'grouStoIdEtypeNameList' => $grouStoIdEtypeNameList,
        ]);
    }

    /**
     * 通过搜索进行查询楼宇
     * @author  zmy
     * @version 2017-09-25
     * @return  [string]     [Json数据]
     */
    public function actionSearchBuild()
    {
        $data      = Yii::$app->request->post();
        $buildList = EquipProductGroupApi::getSpecialSchedulBuildList($data);
        return !$buildList ? [] : Json::encode($buildList);
    }

    /**
     * 产品组和特价排期查询楼宇共用方法
     * 格式："name":buildingName, "build_type":buildingType, "org_id":branch,'orgRange':orgRange, "equipmentType":equipmentType,
     * @author  zmy
     * @version 2017-10-20
     * @return  [type]     [description]
     */
    public function actionGetAllBuildingInProduct()
    {
        $searchParam = Yii::$app->request->post();
        return EquipProductGroupApi::getAllBuildingInProduct($searchParam);
    }

    /**
     * 通过产品组料仓信息ID，
     * @author  zmy
     * @version 2017-09-25
     * @return  [type]     [description]
     */
    public function actionSearchProduct()
    {
        $proGroupId      = Yii::$app->request->post('proGroupId', ''); // 产品组id，产品组料仓信息ID
        $proGroupStockId = Yii::$app->request->post('proGroupStockId', '');
        $ret             = EquipProductGroupApi::getProGroupStockById($proGroupStockId, $proGroupId);
        echo Json::encode($ret);
    }

    /**
     * Displays a single ProductGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看产品组')) {
            return $this->redirect(['site/login']);
        }
        $ret = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProductGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加产品组')) {
            return $this->redirect(['site/login']);
        }
        $model                     = new EquipmentProductGroup();
        $param                     = Yii::$app->request->post();
        $ret                       = EquipProductGroupApi::getEquipGroupTemplate('');
        $equipLabelList            = $ret['equipLabelList'];
        $model->is_update_product  = $model::UPDATE_PRODUCT_YES;
        $model->is_update_recipe   = $model::UPDATE_RECIPE_NO;
        $model->is_update_progress = $model::UPDATE_PROGRESS_YES;
        return $this->render('_form', [
            'model'          => $model,
            'equipLabelList' => '{}',
            'buildList'      => '{}',
        ]);
    }

    /**
     * Updates an existing ProductGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $isCopy = 0)
    {
        if (!Yii::$app->user->can('编辑产品组')) {
            return $this->redirect(['site/login']);
        }
        $model                          = new EquipmentProductGroup();
        $templateRet                    = EquipProductGroupApi::getEquipGroupTemplate($id);
        $model->product_group_id        = $id;
        $model->group_name              = $templateRet['EquipmentProductGroup']['group_name'];
        $model->group_desc              = $templateRet['EquipmentProductGroup']['group_desc'];
        $model->is_update_recipe        = $templateRet['EquipmentProductGroup']['is_update_recipe'];
        $model->is_update_product       = $templateRet['EquipmentProductGroup']['is_update_product'];
        $model->setup_no_coffee_msg     = $templateRet['EquipmentProductGroup']['setup_no_coffee_msg'];
        $model->setup_get_coffee        = $templateRet['EquipmentProductGroup']['setup_get_coffee'];
        $model->is_update_progress      = $templateRet['EquipmentProductGroup']['is_update_progress'];
        $model->pro_group_stock_info_id = $templateRet['EquipmentProductGroup']['pro_group_stock_info_id'];
        $model->build_upload_url        = $templateRet['EquipmentProductGroup']['build_upload_url'];
        $model->build_type_upload       = $templateRet['EquipmentProductGroup']['build_type_upload'];
        $data                           = ['group_id' => $id, 'build_status' => 3, 'pro_group_stock_info_id' => $model->pro_group_stock_info_id];
        $buildList                      = CoffeeBackApi::getBuildList($data);
        return $this->render('_form', [
            'model'          => $model,
            'equipLabelList' => Json::encode($templateRet['equipLabelList']),
            'buildList'      => Json::encode(isset($buildList['buildArr']) ? $buildList['buildArr'] : ""),
            'isCopy'         => $isCopy,
        ]);
    }

    /**
     * Deletes an existing ProductGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除产品组')) {
            return $this->redirect(['site/login']);
        }
        $id        = Yii::$app->request->get('id');
        $groupName = Yii::$app->request->get('groupName');
        if (EquipProductGroupApi::getBuildingByGroup($id)) {
            return false;
        } else {
            if (EquipProductGroupApi::delEquipProductGroupInfo($id)) {
                ManagerLog::saveLog(Yii::$app->user->id, "产品组管理", ManagerLog::DELETE, $groupName);
                return $this->redirect(['index']);
            } else {
                return false;
            }
        }
    }

    /**
     * Finds the ProductGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentProductGroup::getEquipProductGroupById($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 更新发布版本
     * @author  zmy
     * @version 2017-10-19
     * @param   int $id 产品组id
     * @return  [type]         [description]
     */
    public function actionVersion($id)
    {
        if (!Yii::$app->user->can('产品组发布')) {
            return $this->redirect(['site/login']);
        }
        EquipProductGroupApi::updateReleaseVersion($id);
        return $this->redirect(['index']);
    }

    /**
     * 是否可以发布
     * @author  zmy
     * @version 2017-10-19
     * @param   int $id 产品组id
     * @return  [type]         [description]
     */
    public function actionIsPublic($id)
    {
        if (!Yii::$app->user->can('产品组发布')) {
            return $this->redirect(['site/login']);
        }
        $msg = EquipProductGroupApi::productIsProgress($id);
        if ($msg != 1) {
            return Json::encode(preg_split('/\s+/', $msg));
        }
        return $msg;
    }

    /**
     * 根据ID，查询出所有的特价排期单品数组
     * @author      tuqiang
     * @version     2017-10-18
     * @param       $id
     */
    public function actionProduct($id)
    {
        if (!Yii::$app->user->can('查看产品组单品')) {
            return $this->redirect(['site/login']);
        }
        $productList = Api::getProducts($id, 3);
        $productList = !$productList ? [] : Json::decode($productList);
        return $this->render('product', [
            'productList' => $productList,
        ]);
    }

    /**
     * 根据ID，查询出所有的特价排期单品数组
     * @author      tuqiang
     * @version     2017-10-18
     * @param       $id
     */
    public function actionBuilding($id)
    {
        if (!Yii::$app->user->can('查看产品组楼宇')) {
            return $this->redirect(['site/login']);
        }
        $buildList = EquipProductGroupApi::getBuildingByGroup($id);
        return $this->render('building', [
            'building' => $buildList,
        ]);
    }

    /**
     * 添加日志
     * @author   tuqiang
     * @version  2017-11-14
     */
    public function actionSaveLog()
    {
        $type      = Yii::$app->request->get('type');
        $groupName = Yii::$app->request->get('groupName');
        if ($type == 0) {
            $type = ManagerLog::CREATE;
        } else {
            $type = ManagerLog::UPDATE;
        }
        ManagerLog::saveLog(Yii::$app->user->id, "产品组管理",
            $type, $groupName);
    }

    /**
     * 批量对产品组进行发布
     * @author sulingling
     * @version 2018-06-23
     * @param $ids string
     * @return boolean | array()
     */
    public function actionIsPublicAll()
    {
        $ids = Yii::$app->request->post("ids", '');
        $msg = EquipProductGroupApi::productIsProgressAll($ids);
        return $msg;
    }
}
