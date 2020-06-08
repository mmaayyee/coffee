<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DeliveryPersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '配送人员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-person-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
    <?=!\Yii::$app->user->can('新增配送人员') ? '' : Html::a('新增配送人员', ['create'], ['class' => 'btn btn-success'])?>
    </p>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '配送人员名',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->person_name;
            },
        ],
        [
            'label'  => '负责的区域',
            'format' => 'raw',
            'value'  => function ($model) {
                return implode("<br>", $model->building_info);
            },
        ],
        [
            'label'  => '企业微信号',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->wx_number;
            },
        ],
        [
            'label'  => '电话',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->mobile;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {del}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑配送人员') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => '编辑'], []);
                },
                'del'    => function ($url, $model, $key) {
                    $options = [
                        'title'   => '删除',
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'/delivery-person/delete?id=' . $model->person_id . '\','
                        . 'function(data){datas = JSON.parse(data);'
                        . 'if(datas["status"] == "success"){location.reload()}'
                        . 'else{alert(\'删除失败!\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('删除配送人员') ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>
</div>
