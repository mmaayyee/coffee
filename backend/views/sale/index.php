<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '零售活动人员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="discount-holicy-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>
    <p><?=Yii::$app->user->can('零售活动人员添加') ? Html::a('添加', ['create'], ['class' => 'btn btn-success']) : '';?></p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => '姓名',
            'value'     => function ($model) {return $model->sale_name;},
        ],
        [
            'label'     => '邮箱',
            'value'     => function ($model) {return $model->sale_email;},
        ],
        [
            'label'     => '手机号',
            'value'     => function ($model) {return $model->sale_phone;},
        ],
        [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{delete} {update}',
            'buttons'   => [
                'update' => function ($url, $model) {
                    return   Yii::$app->user->can('零售活动人员修改') ? Html::a('', '/sale/update?sale_id='.$model->sale_id,['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                },
                'delete' => function ($url, $model) {
                    $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get(\'/sale/delete?sale_id='.$model->sale_id.'\','
                            . 'function(data){'
                            . 'if(data == 1){location.reload()}'
                            . 'else{alert(\'删除失败，请检查该人员是否存在业绩\')}})'
                            . '};'
                            . 'return false;'
                        ]; 
                    return Yii::$app->user->can('零售活动人员删除') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', '/sale/delete?sale_id='.$model->sale_id,$options) : '';
                }
            ],
        ],
    ],
]);?>

</div>
