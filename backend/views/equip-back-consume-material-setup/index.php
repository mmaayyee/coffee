<?php

use common\helpers\Tools;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '工厂模式物料消耗设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-label-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'equipTypeIdNameList' => $equipTypeIdNameList]); ?>
    <p>
        <?=Yii::$app->user->can('添加工厂模式物料消耗设置') ? Html::a('添加工厂模式物料消耗设置', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'label' => '设备类型',
            'value' => function ($model) use ($equipTypeIdNameList) {
                return $equipTypeIdNameList[$model->equip_type_id] ?? '';
            },
        ],
        [
            'label' => '参数名称',
            'value' => function ($model) {
                return $model->config_key;
            },
        ],
        [
            'label' => '参数值',
            'value' => function ($model) {
                return $model->config_value;
            },
        ],
        [
            'label' => '添加时间',
            'value' => function ($model) {
                return Tools::getDateByTime($model->create_time);
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {del}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑工厂模式物料消耗设置') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑']);
                },
                'del'    => function ($url, $model, $key) {
                    $options = [
                        'title'   => '删除',
                        'onclick' => 'return confirm("确定删除吗？");',
                    ];
                    return !\Yii::$app->user->can('删除工厂模式物料消耗设置') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>
</div>
