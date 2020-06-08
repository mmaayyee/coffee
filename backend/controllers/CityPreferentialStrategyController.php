<?php

namespace backend\controllers;

use backend\models\CityPreferentialStrategy;
use common\models\Api;
use Yii;
use yii\web\Controller;

/**
 * BuildTypeController implements the CRUD actions for BuildType model.
 */
class CityPreferentialStrategyController extends Controller
{
    /**
     * 城市优惠策略列表
     * @author  zgw
     * @version 2017-07-05
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看城市优惠策略')) {
            return $this->redirect(['site/login']);
        }
        $cityName     = Yii::$app->request->get('city_name');
        $strategyList = Api::getCityPreferentialStrategy('&city_name=' . $cityName);
        return $this->render('index', ['strategyList' => $strategyList, 'cityName' => $cityName]);
    }

    /**
     * 添加城市优惠策略
     * @author  zgw
     * @version 2017-07-05
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加城市优惠策略')) {
            return $this->redirect(['site/login']);
        }
        $model = new CityPreferentialStrategy();
        $data  = Yii::$app->request->post('CityPreferentialStrategy');
        if ($data) {
            $saveRes = Api::saveCityPreferentialStrategy($data);
            if ($saveRes) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "城市优惠策略管理", \backend\models\ManagerLog::CREATE, "添加城市优惠策略");
                return $this->redirect('index');
            } else {
                $model->load(['CityPreferentialStrategy' => $data]);
                $model->addError('city_name', $model->city_name . '已存在');
                return $this->render('update', ['model' => $model, 'cities' => Api::getCities()]);
            }
        } else {
            return $this->render('create', ['model' => $model, 'cities' => Api::getCities()]);
        }
    }

    /**
     * 编辑城市优惠策略
     * @author  zgw
     * @version 2017-07-05
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑城市优惠策略')) {
            return $this->redirect(['site/login']);
        }
        $model = new CityPreferentialStrategy();
        $data  = Yii::$app->request->post('CityPreferentialStrategy');
        if ($data) {
            $saveRes = Api::saveCityPreferentialStrategy($data);
            if ($saveRes) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "城市优惠策略管理", \backend\models\ManagerLog::UPDATE, "编辑城市优惠策略");
                return $this->redirect('index');
            } else {
                $model->load(['CityPreferentialStrategy' => $data]);
                $model->id = $id;
                $model->addError('city_name', $model->city_name . '已存在');
                return $this->render('update', ['model' => $model, 'cities' => Api::getCities()]);
            }
        } else {
            $strategyList = Api::getCityPreferentialStrategy('&id=' . $id);
            if (isset($strategyList[0])) {
                $strategy = $strategyList[0];
                $model->load(['CityPreferentialStrategy' => $strategy]);
                $model->id = $strategy['id'];
            }
            return $this->render('update', ['model' => $model, 'cities' => Api::getCities()]);
        }
    }
    /**
     * 删除城市优惠策略
     * @author  zgw
     * @version 2017-07-05
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除城市优惠策略')) {
            return $this->redirect(['site/login']);
        }
        Api::delCityPreferentialStrategy($id);
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "城市优惠策略管理", \backend\models\ManagerLog::DELETE, "删除城市优惠策略");
        return $this->redirect('index');
    }
}
