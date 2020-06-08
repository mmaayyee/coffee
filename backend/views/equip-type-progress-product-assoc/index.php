<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipTypeProgressProductAssocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '进度条管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-type-progress-product-assoc-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('添加进度条', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '产品名称',
            'format' => 'html',
            'value'  => function ($model) {return $model->product_name;},
        ],
        [
            'label'  => '进度条可选属性',
            'format' => 'html',
            'value'  => function ($model) {return $model->getProgressAttributes($model->progress_bar_attributes);},
        ],
        [
            'label'  => '设置的设备类型',
            'format' => 'html',
            'value'  => function ($model) {return $model->getEquipTypeName($model->equip_type_name_list);},
        ],
        // 'process_name',
        // 'equip_type_name',
        // 'enter_time',
        // 'enter_sort',

        // ['class' => 'yii\grid\ActionColumn'],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {delete} {copy}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看进度条') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                },
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑进度条') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delete' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('删除进度条') ? "" : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url);
                },
                'copy'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑进度条') ? '' : Html::a('<span class="glyphicon glyphicon-copyright-mark"></span>', '/equip-type-progress-product-assoc/update?id=' . $model->product_id . '&isCopy=1', ['title' => '复制进度条']);
                },
            ],
        ],
    ],
]);?>

</div>
