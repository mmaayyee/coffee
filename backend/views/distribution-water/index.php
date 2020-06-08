<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use backend\models\DistributionWater;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '水单管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-water-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p> 
        <?= !Yii::$app->user->can('添加水单') ? '' : Html::a('添加水单', ['create'], ['class' => 'btn btn-success']) ?>

        <?php if (Yii::$app->user->can('下单操作')) {?>
        <?=Html::a('批量下单', "javascript:void(0);", ['class' => 'btn btn-success gridview'])?>
        <?php }?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options'      => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name'  => 'id',
            ],
            [
                'attribute' => 'build_id',
                'format' => 'text',
                'value' => function ($model){
                    return \common\models\Building::getBuildingDetail('name', ['id'=> $model->build_id])['name'];
                },
            ],
            [
                'attribute' => 'create_time',
                'value' =>  function($model){
                    return !empty($model->create_time) ? date("Y-m-d H:i:s", $model->create_time) : '暂无';
                }
            ],
            [
                'attribute' => 'order_time',
                'value' => function ($model){
                    return !empty($model->order_time) ? date("Y-m-d H:i:s", $model->order_time) : '暂无';
                }
            ],
            [
                'attribute' => 'surplus_water',
                'value' =>  function($model){
                    return $model->surplus_water.'桶';
                }
            ],
            [
                'attribute' => 'need_water',
                'value' =>  function($model){
                    return $model->need_water.'桶';
                }
            ],
            [
                'attribute' => 'supplier_id',
                'format' => 'text',
                'value' => function ($model){
                    return \backend\models\ScmSupplier::getSurplierDetail('name', ['id'=> $model->supplier_id])['name'];
                },
            ],
            [
                'attribute' => 'completion_status',
                'value' => function ($model) {
                    return DistributionWater::$completionStatus[$model->completion_status];
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}{order}',
                'buttons'=>[
                    'update' => function ($url, $model) {
                        return (!Yii::$app->user->can('编辑水单') || $model->completion_status != DistributionWater::NO_WATER_ORDER) ? '' : Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']);
                    },
                    'order' => function ($url, $model) {
                        $options = [
                            'onclick' =>'return confirm("确定下单吗？");'
                        ];                    
                        return (!Yii::$app->user->can('下单操作') || $model->completion_status != DistributionWater::NO_WATER_ORDER) ? '' : Html::a('<span class="glyphicon glyphicon-cloud-upload" title="下单" ></span>', $url, $options);
                    }
                ]
            ],

        ],
    ]); ?>
</div>

<?php
$url = Url::to(["distribution-water/batch-order"]);
$this->registerJs('
        $(".gridview").on("click", function () {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            if (keys.length == 0) {
                alert("请选择想要下单的内容");
            }else{
                console.log(keys);
                $.ajax({
                    type: "POST",
                    url:  "' . $url . '",
                    data: {keys: keys},
                    dataType: "json",
                    success: function(data){
                        if (data == true) {
                            window.location.reload();
                        }else{
                            alert("下单失败");
                        }
                    },
                    error: function(data){
                        alert("下单失败");
                    }
                });
            }

        });
    ');
?>


