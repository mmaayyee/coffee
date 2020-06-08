<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Building;
use backend\models\ProductOfflineRecord;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductOfflineRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '产品上下架操作记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-offline-record-index">
    
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'build_id',
                'value' => function($model){
                    if( isset($model->equip->build_id) && !empty($model->equip->build_id)){
                        return Building::getField("name", ['id'=>$model->equip->build_id]);
                    }
                }
            ],
            'equip_code',
            
            [
                'attribute' => 'product_name',
                'value' => function($model){
                    return $model->product_name;
                }
            ],
            [
                'attribute' => 'type',
                'value' => function($model){
                    return ProductOfflineRecord::$shelvesType[$model->type];
                }
            ],

            [
                'attribute' => 'operator',
                'value' => function($model){
                    return isset($model->manager->realname) ? $model->manager->realname : "";
                }
            ],

            [
                'attribute' => 'create_time',
                'value' => function($model){
                    if(!empty($model->create_time)){
                        return date('Y-m-d H:i',$model->create_time);
                    }
                }
            ],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
