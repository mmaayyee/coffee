<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipAcceptanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备验收记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-acceptance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

<!--     <p>
        <? //= Html::a('Create Equip Acceptance', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'build_id',
                'format' => 'text',
                'value' => function ($model){
                    if ($model->buildName) {
                        return $model->buildName->name;
                    }
                },
            ],

            'reason',
             [
                'attribute' => 'accept_time',
                'format'=>'text',
                'value' => function ($model){ 
                    if (empty($model->accept_time)) {
                        return '暂无';
                    }else{
                        return date("Y-m-d m:i:s", $model->accept_time);
                    }
                },
            ],
            'accept_renson',
            [
                'attribute' => 'accept_result',
                'format' => 'text',
                'value' => function ($model){
                    if(!$model->accept_result){
                        return '暂无';
                    }else{
                        return $model->getAcceptResultArr($model->accept_result)[$model->accept_result];
                    }
                    
                },
            ],
            [
                'attribute' => 'accept_lightbox_details',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a('查看', Url::to(["/", 'id'=>$model->Id]));
                },
            ],
            [
                'attribute' => 'equip_lightbox_details',
                'format' => 'raw',
                'value' => function ($model){
                    return Html::a('查看', Url::to(["/", 'id'=>$model->Id]));
                },
            ],
        ],
    ]); ?>

</div>
