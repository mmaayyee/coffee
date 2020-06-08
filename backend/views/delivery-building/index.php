<?php

use backend\models\DeliveryBuilding;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DeliveryBuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '外卖点位';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-building-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=!\Yii::$app->user->can('新增配送点位') ? '' : Html::a('新增配送点位', ['create'], ['class' => 'btn btn-success'])?>
    </p>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '点位名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->building_name;
            },
        ],
        [
            'label'  => '点位配送员',
            'format' => 'raw',
            'value'  => function ($model) {
                return implode("<br>", array_keys($model->person_info));
            },
        ],
        [
            'label'  => '配送员电话',
            'format' => 'raw',
            'value'  => function ($model) {
                return implode("<br>", $model->person_info);
            },
        ],
        [
            'label'  => '营业时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->business_time;
            },
        ],
        [
            'label'  => '营业状态',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->business_status == DeliveryBuilding::STATUS_VALID ? '正常营业' : '暂停营业';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{change} {update} {del}',
            'buttons'  => [
                'change' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '更改营业状态',
                        'onclick' => 'if(confirm("确定更改营业状态吗？")){$.get(\'/delivery-building/change?id=' . $model->delivery_building_id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'操作失败!\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('点位营业状态编辑') ? '' : Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, $options);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑配送点位') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑'], []);
                },
                'del'    => function ($url, $model, $key) {
                    $options = [
                        'title'   => '删除',
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/delivery-building/delete?id=' . $model->delivery_building_id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'删除失败!\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('删除配送点位') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>
</div>
