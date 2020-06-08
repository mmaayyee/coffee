<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipDebugSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备调试项管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("
    $('.del-confirm').click(function(){
        var url = $(this).attr('href');
        $('#confirm-content').text('确认要删除吗？');
            cart_id = $(this).next().val();
            $('#confirm').modal();
            $('#confirm-sure').click(function (){
                location.href=url;
            })
        return false;
    })
");
?>
<div class="equip-debug-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加设备调试项') ? Html::a('添加设备调试项', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'debug_item',
            'options'   => ['style' => 'width:60%'],
        ],
        [
            'attribute' => 'equip_type_id',
            'value'     => function ($model) {
                return isset($model->equipType) ? $model->equipType->model : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑设备调试项') ? '' : Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']);
                },
                'delete' => function ($url) {
                    return Yii::$app->user->can('删除设备调试项') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash del-confirm', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
<?=$this->render('/layouts/confirm');?>
