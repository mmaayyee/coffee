<?php

use backend\models\DeliveryBuilding;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DeliveryBuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '外送区域';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-building-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=!\Yii::$app->user->can('新增配送区域') ? '' : Html::a('新增配送区域', ['create'], ['class' => 'btn btn-success'])?>
    </p>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '配送区域名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->region_name;
            },
        ],
        [
            'label'  => '点位列表',
            'format' => 'raw',
            'value'  => function ($model) {
                return (implode("<br>", $model->build_list));
            },
        ],
        [
            'label'  => '配送员列表',
            'format' => 'raw',
            'value'  => function ($model) {
                return implode("<br>", $model->person_list);
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
                return $model->business_status == $model::STATUS_VALID ? '正常营业' : '暂停营业';
            },
        ],
        [
            'label'  => '起送价',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->min_consum;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{change} {update} {del}',
            'buttons'  => [
                'change' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '更改营业状态',
                        'onclick' => 'if(confirm("确定更改营业状态吗？")){$.get(\'/delivery-region/delivery-region-change?id=' . $model->delivery_region_id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'操作失败!\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('编辑配送区域') ? '' : Html::a('<span class="glyphicon glyphicon-refresh"></span>', 'javascript:node(0)', $options);
//                    return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, $options);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑配送区域') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑'], []);
//                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑'], []);
                },
//                'del'    => function ($url, $model, $key) {
//                    $options = [
//                        'title'   => '删除',
//                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/delivery-building/delete?id=' . $model->delivery_building_id . '\','
//                        . 'function(data){datas = JSON.parse(data);'
//                        . 'if(datas["status"] == "success"){location.reload()}'
//                        . 'else{alert(\'删除失败!\')}})'
//                        . '};'
//                        . 'return false;',
//                    ];
//                    return !\Yii::$app->user->can('删除配送点位') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
//                },
            ],
        ],
    ],
]);?>
</div>
