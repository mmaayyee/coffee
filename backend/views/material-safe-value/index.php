<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Equipments;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\MaterialSafeValueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '料仓预警值管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-safe-value-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加料仓预警值', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'equipment_id',
                'label'     => '设备编号',
                'value'     => function($model){
                    return $model->equipment_id ? Equipments::getField('equip_code',['id' => $model->equipment_id]) : '';
                }
            ],
            [
                'attribute' => 'build_id',
                'value' => function($model){
                    return $model->equipment_id ? Equipments::findOne($model->equipment_id)->build->name : '';
                }
            ],
            [
                'attribute' => 'equipment_id',
                'label' => '预警值',
                'format' => 'html',
                'value' => function($model){
                    return $model->equipment_id ? \backend\models\MaterialSafeValue::getStockSafeValue($model->equipment_id) : '';
                }
            ],

            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons'  => [
                    'view'   => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('查看料仓预警值') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/material-safe-value/view','equipmentId'=> $model->equipment_id]));
                    },
                    'update' => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('编辑料仓预警值') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/material-safe-value/update','equipmentId'=> $model->equipment_id]));
                    },
                    'delete' =>function ($url, $model) {
                        $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get("'.Url::to(['/material-safe-value/delete','equipmentId'=> $model->equipment_id]).'",'
                                . 'function(data){'
                                . 'if(data == 1){location.reload()}'
                                . 'else{alert("删除失败，请检查是否存在管理员")}})'
                                . '};'
                                . 'return false;'
                        ];
                        return \Yii::$app->user->can('删除料仓预警值') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/material-safe-value/delete','equipmentId'=> $model->equipment_id]),$options) : '';
                    },
                ],
            ],
        ],
    ]); ?>
</div>
