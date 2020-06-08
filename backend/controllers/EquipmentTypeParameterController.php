<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipmentTypeParameter;
use common\models\EquipmentTypeParameterApi;
use yii\web\Controller;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EquipmentTypeParameterController implements the CRUD actions for EquipmentTypeParameter model.
 */
class EquipmentTypeParameterController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipmentTypeParameter models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('设备类型参数管理')) {
            return $this->redirect(['site/login']);
        }
        //获取设备类型列表
        $equipmentTypeList = EquipmentTypeParameterApi::getEquipmentTypeList();
        return $this->render('index', [
            'equipmentTypeList' => Json::encode($equipmentTypeList),
        ]);
        
    }
    /**
     * 根据设备类型id值获取参数值列表    equipment-type-parameter/get-parameter-list
     * @method post
     * @param  [int]   equipment_type_id  设备类型参数id       （必填）
     * @return [json]
     */
    public function actionGetParameterList()
    {
        if (!Yii::$app->user->can('查看类型参数')) {
            return $this->redirect(['site/login']);
        }
        $equipmentTypeId = Yii::$app->request->post('equipment_type_id');
        if(!$equipmentTypeId){
            return json::encode([]);
        }
        //查找数据
        $where['equipment_type_id'] = $equipmentTypeId;
        $equipmentTypeParameterList = EquipmentTypeParameter::getEquipmentTypeParameterList($where);
        return Json::encode($equipmentTypeParameterList);
    }
    /**
     * 修改/新增 设备参数类型值  equipment-type-parameter/update
     * @method post
     * @param  [int]    equipment_type_id   设备类型               （必填）
     * @param  [str]    parameter_name      设备类型参数名          （必填）
     * @param  [int]    max_parameter       设备类型参数可设定最大值 （必填）
     * @param  [int]    min_parameter       设备类型参数可设定最小值 （必填）
     * @param  [int]    id                  参数id                 （修改时必填）
     * @return [json]
     */
    public function actionUpdate()
    {
        if (!Yii::$app->user->can('编辑类型参数')) {
            return $this->redirect(['site/login']);
        }
        $data = Yii::$app->request->post();
        $result = EquipmentTypeParameterApi::updateEquipmentTypeParameter($data);
        if ($result['status'] == 'success') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备类型参数管理", \backend\models\ManagerLog::UPDATE, "编辑设备类型参数");
        }
        return Json::encode($result);
    }

    /**
     * 删除设备参数类型值  equipment-type-parameter/delete
     * @method post
     * @param  [int]   id  设备类型参数id       （必填）
     * @return [json]
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除类型参数')) {
            return $this->redirect(['site/login']);
        }
        $equipmentTypeParameterId = Yii::$app->request->post('id');
        $result                   = EquipmentTypeParameterApi::delEquipmentTypeParameter(['id' => $equipmentTypeParameterId]);
        if ($result['status'] == 'success') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备类型参数管理", \backend\models\ManagerLog::DELETE, "删除设备类型参数");
        }
        return Json::encode($result);
    }
}
