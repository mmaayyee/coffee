<?php

namespace frontend\controllers;

use Yii;
use backend\models\DistributionWater;
use backend\models\DistributionWaterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\helpers\WXApi\WxMessage;
use common\models\Building;
use common\models\WxMember;
use backend\models\ScmSupplier;

/**
 * DistributionWaterController implements the CRUD actions for DistributionWater model.
 */
class DistributionWaterController extends BaseController
{
    /**
     * Lists all DistributionWater models.
     * @return mixed
     */
    public function actionIndex()
    {
        //当前用户 $this->userinfo['userid']
        $supplierId = WxMember::getFiled('supplier_id', ['userid'=>$this->userinfo['userid']]);
        $distributeWaterArr = [];
        $distributionWaterCount = 0;
        if ($supplierId) {
            $distributeWaterArr = DistributionWater::find()->where(['and','completion_status='.DistributionWater::WAIT_SEND, 'supplier_id='.$supplierId, ['!=', 'order_time','0']])->asArray()->all();
            $distributionWaterCount =   DistributionWater::find()->where(['and','completion_status='.DistributionWater::WAIT_SEND, 'supplier_id='.$supplierId, ['not', ['order_time'=>'0']]])->count();
        }
        return $this->render('index', [
            'distributeWaterArr' =>   $distributeWaterArr,
            'distributionWaterCount'    =>  $distributionWaterCount,
        ]);
    }

    /**
     *  水单详情
     *  @param $id
     **/
    public function actionDetail($id){
        $distributeWaterArr = DistributionWater::find()->where(['id'=>$id])->one();
        
        return $this->render('detail', [
            'distributeWaterArr'    =>  $distributeWaterArr,
        ]);
    }

    /**
     *  任务详情 配送完成
     *  
     **/
    public function actionDeliveryCompleteDetail(){
        $id = Yii::$app->request->get()['id'];
        $waterModel = DistributionWater::findOne($id);
        $waterModel->completion_status  =   DistributionWater::ALREADY_SEND;
        $waterModel->upload_time    =   time();
        $waterModel->completion_date=   date("Y-m-d");
        if($waterModel->save()){
         return $this->redirect(['index']);
        }else{
            echo "添加失败！";exit();
        }
    }


    /**
     *  供水统计主页
     *
     **/
    public function actionWaterStatisticsIndex(){
        $model = new DistributionWater();
        return $this->render('water-statistics-index',[
            'model' => $model,
        ]);
    }

    /**
     *  楼宇送水量统计（ajax）
     *
     **/
    public function actionWaterStatisticsSearch(){
        $params =   Yii::$app->request->get();
        $query = DistributionWater::find();
        // 当前用户 $this->userinfo['userid']
        $supplierId = WxMember::find()->where(['userid'=>$this->userinfo['userid']])->asArray()->one()['supplier_id'];
        //日期查询
        if (!empty($params["startTime"]) && !empty($params["endTime"])) {
            $startTime       = strtotime($params["startTime"]);
            $endTime         = strtotime($params["endTime"]);
            $query->andFilterWhere(['>=', 'distribution_water.upload_time', $startTime]);
            $query->andFilterWhere(['<=', 'distribution_water.upload_time', $endTime]);

        }
        $query->andFilterWhere(['distribution_water.completion_status'=> DistributionWater::ALREADY_SEND, 'supplier_id'=>$supplierId]);

        $distributeWaterArr = $query->asArray()->all();

        $tr     =   '';
        $count  =   '0';
        foreach ($distributeWaterArr as $key => $value) {
            $tr .= "
            <tr>
                <td>
                    ".\common\models\Building::getBuildingDetail('name', ['id'=> $value['build_id']])['name']."
                </td>
                <td>
                    ".floatval($value['need_water'])."桶
                </td>
            </tr>";
            $count  +=  $value['need_water'];
        }

        $table = "
            <table class='table table-bordered'>
                <tr>
                    <td>
                        <b>总送水量</b>
                    </td>
                    <td>
                        <b>".$count."</b>桶
                    </td>
                </tr>
                ".$tr."
            </table>";
        echo $table;
    }

    /**
     *  供水商的配送记录
     *
     **/
    public function actionDeliveryRecord(){
        $supplierId = WxMember::getFiled('supplier_id', ['userid'=>$this->userinfo['userid']]);
        $distributeWaterArr = [];
        $waterCount = 0;
        if ($supplierId) {
            $distributeWaterArr   =   DistributionWater::find()->where(['and','completion_status='.DistributionWater::ALREADY_SEND, 'supplier_id='.$supplierId, ['not', ['upload_time'=>'0']]])->asArray()->all();
        
            $waterCount   =   DistributionWater::find()->where(['and','completion_status='.DistributionWater::ALREADY_SEND, 'supplier_id='.$supplierId, ['not', ['order_time'=>'0']]])->count();
        }
        return $this->render("delivery-record",[
            'distributeWaterArr'  =>  $distributeWaterArr,
            'waterCount'    =>  $waterCount,
        ]);

    }




}
