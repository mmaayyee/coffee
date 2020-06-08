<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipLightBoxDebugSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '灯箱调试项管理';
$this->params['breadcrumbs'][] = ['label' => '灯箱管理', 'url' => ['/equip-light-box/index']];
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
<div class="equip-light-box-debug-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加灯箱调试项') ? Html::a('添加灯箱调试项', ['create?light_box_id=' . $searchModel['light_box_id']], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        'debug_item',
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}',
            'buttons'  => [
                'update' => function ($url) {
                    return !Yii::$app->user->can('编辑灯箱调试项') ? '' : Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']);
                },
                'delete' => function ($url) {
                    return Yii::$app->user->can('删除灯箱调试项') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash del-confirm', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>

</div>
<?=$this->render('/layouts/confirm');?>
