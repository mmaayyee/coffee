<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipSymptomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '故障现象';
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
<div class="equip-symptom-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加故障现象') ? Html::a('添加故障现象', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'symptom',
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons'  => [
                'update' => function ($url) {
                    return Yii::$app->user->can('编辑故障现象') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                },
                'delete' => function ($url) {
                    return Yii::$app->user->can('删除故障现象') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-trash del-confirm', 'title' => '删除']) : '';
                },
            ],
        ],
    ],
]);?>
</div>
<?=$this->render('/layouts/confirm');?>