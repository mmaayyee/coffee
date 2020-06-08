<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EquipmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '楼宇列表';
$this->params['breadcrumbs'][] = ['label' => '预磨豆设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = '楼宇列表';
?>
<div class="equipments-index">

    <h1><?=Html::encode($this->title);?></h1>
    <?php echo $this->render('_search_building', ['model' => $searchModel]); ?>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => '设备编号',
            'value'     => function ($model) {
                return empty($model->equipmentCode) ? '' : $model->equipmentCode;
            },
        ],
        [
            'label'     => '楼宇名称',
            'value'     => function ($model) {
                return empty($model->buildName) ? '' : $model->buildName;
            },
        ],
        [
            'label'     => '分公司',
            'value'     => function ($model) {
                return empty($model->orgName) ? '' : $model->orgName;
            },
        ],
        [
            'class'     => 'yii\grid\ActionColumn',
            'template'  => '{delete}',
            'buttons'   => [
                'delete' => function ($url, $model) {
                    $options = [
                            'onclick' =>'if(confirm("确定删除吗？")){$.get(\'/grind/build-delete?equipmentCode='.$model->equipmentCode.'\','
                            . 'function(data){'
                            . 'if(data == 1){location.reload()}'
                            . 'else{alert(\'删除失败,网络问题\')}})'
                            . '};'
                            . 'return false;'
                        ]; 
                    return Yii::$app->user->can('预磨豆设置删除') ? Html::a('<span class="glyphicon glyphicon-trash"></span>', '/grind/build-delete?equipmentCode='.$model->equipmentCode,$options):'';
                }
            ],
        ],
    ],
]);?>

</div>