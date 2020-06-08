<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipDelivery;
use common\models\EquipTask;
use common\models\Building;
use common\models\Equipments;
use backend\models\EquipDeliverySearch;
use backend\models\EquipAcceptanceSearch;
use backend\models\EquipAcceptance;
use backend\models\EquipDebug;
use backend\models\EquipLightBoxDebug;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\EquipTaskSearch;
// EquipCheckDeliveryController

/**
 * EquipDeliveryController implements the CRUD actions for EquipDelivery model.
 */
class EquipCheckDeliveryController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipDelivery models.
     * @return mixed
     */
    public function actionIndex($equip_id)
    {
        $searchModel    =   new EquipTaskSearch();
        $dataProvider   =   $searchModel->searchCheckDelivery(['EquipTaskSearch' => ['equip_id' =>$equip_id, 'task_type' =>2]]);

        return $this->render('index',[
            'searchModel'   =>  $searchModel,
            'dataProvider'  =>  $dataProvider,
        ]);

    }

    // /**
    //  * Displays a single EquipDelivery model.
    //  * @param string $id
    //  * @return mixed
    //  */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

  
    /**
     * Finds the EquipDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipDelivery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *  ajax获取设备验收详情
     */
    public function actionAjaxEquipAcceptance(){
        $deliveryId =   $_GET['delivery_id'];
        $detail     =   $_GET['detail'];
        $acceptEptanceArr   =   array();
        $acceptEptanceArray =   EquipAcceptance::find()->where(['delivery_id'=>$deliveryId])->asArray()->one();
        $valueArr       =   array();
        if($detail=='equip_detail'){
            $debug_result   =   json_decode($acceptEptanceArray['debug_result']);
            if($debug_result){
                foreach ($debug_result as $resultKey => $resultValue) {
                    $valueArr   =   EquipDebug::find()->where(['Id'=>$resultKey])->asArray()->one();
                    $valueArr['ret_result'] = $resultValue;
                    $acceptEptanceArr[] = $valueArr;
                }
            }
        }else{
            $light_box_result   =   json_decode($acceptEptanceArray['light_box_result']);
            if($light_box_result){
                foreach ($light_box_result as $resultKey => $resultValue) {
                    $valueArr   =   EquipLightBoxDebug::find()->where(['Id'=>$resultKey])->asArray()->one();
                    $valueArr['ret_result'] = $resultValue;
                    $acceptEptanceArr[] = $valueArr;
                }
            }
        }
        echo json_encode($acceptEptanceArr);
    }
}
