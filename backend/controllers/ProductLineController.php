<?php

namespace backend\controllers;

use backend\models\Manager;
use backend\models\ProductOfflineRecord;
use backend\models\ScmStock;
use common\models\Api;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * ProductShelvesController implements the CRUD actions for ScmStock model.
 */
class ProductLineController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'product-shelves', 'get-product'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 产品上架接口处理
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('产品下架列表管理')) {
            return $this->redirect(['site/login']);
        }
        $data   = [];
        $params = Yii::$app->request->get();

        $page              = Yii::$app->request->get('page', 1);
        $model             = new ProductOfflineRecord();
        $retProductOffline = '';
        $orgID             = Manager::getManagerBranchID();
        if ($params) {
            $model->load($params);
            $equipCode = '';
            if (isset($params['equip_code_radio']) && $params['equip_code_radio'] == 1) {
                $equipCode       = $params['ProductOfflineRecord']['build_id'];
                $model->build_id = $equipCode;
            } else if (isset($params['equip_code_radio']) && $params['equip_code_radio'] == 2) {
                $equipCode         = $params['ProductOfflineRecord']['equip_code'];
                $model->equip_code = $equipCode;
            }

            $data['equipment_code'] = trim($equipCode);
            $data['lock_from']      = $model->lock_from;
            $data['start_time']     = $model->start_time;
            $data['end_time']       = $model->end_time;
        }
        // Api 获取所有的智能平台上的下架数据
        $pageSize            = 20;
        $productOfflineLists = json_decode(Api::getProductOfflineArr($data, $pageSize, $page, $orgID), true);
        $pages               = new Pagination(['totalCount' => $productOfflineLists['totalCount'], 'pageSize' => $pageSize]);

        return $this->render('index', [
            'model'               => $model,
            'pages'               => $pages,
            'productOfflineLists' => $productOfflineLists['productOfflineArr'],

        ]);
    }

    /**
     * 接收ID， 调用上架接口
     * @author  zmy
     * @version 2017-06-03
     * @param   [type]     $id [description]
     * @return  [type]         [description]
     */
    public function actionProductShelves()
    {
        if (!Yii::$app->user->can('产品下架列表管理')) {
            return $this->redirect(['site/login']);
        }
        $id          = Yii::$app->request->post('id');
        $equipCode   = Yii::$app->request->post('equip_code');
        $productName = Yii::$app->request->post('product_name');
        $productID   = Yii::$app->request->post('product_id');
        // 根据ID 上架产品
        $result = Api::productLineSync($id);
        if (!$result) {
            echo json_encode(false);exit();
        } else {
            // 添加记录
            $userName = yii::$app->user->identity->username;
            ProductOfflineRecord::saveProOffRecord($equipCode, $productID, $userName, ProductOfflineRecord::OFF_SHELEVES, $productName);

        }
        echo json_encode(true);

    }

    /**
     * ajax通过buildId 查询出接口，进行组合返回数据
     * @author  zmy
     * @version 2017-05-17
     * @return  [type]     [description]
     */
    public function actionGetProduct()
    {
        if (!Yii::$app->user->can('产品下架列表管理')) {
            return $this->redirect(['site/login']);
        }
        $buildId = Yii::$app->request->post("buildId", 0);
        $type    = Yii::$app->request->post("type", 0);
        $input   = ProductOfflineRecord::getProductInput($buildId, $type);
        echo json_encode($input);
    }

}
