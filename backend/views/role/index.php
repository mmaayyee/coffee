<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->user->can('添加角色')): ?>
    <p>
        <?=Html::a('添加角色', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?php endif;?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'value'     => function ($model) {
                return $model['name'];
            },
        ],
        [
            'attribute' => 'description',
            'value'     => function ($model) {
                return $model['description'];
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑角色') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delete' => function ($url, $model, $key) {
                    $options = [
                        'onclick' => 'if(confirm("确定删除吗？")){$.get(\'' . $url . '\','
                        . 'function(data){'
                        . 'if(data == 1){location.reload()}'
                        . 'else{alert(\'删除失败，请检查是否存在管理员\')}})'
                        . '};'
                        . 'return false;',
                    ];
                    return !\Yii::$app->user->can('删除角色') || $model['name'] === \backend\models\AuthItem::SUPER_MASTER ? '' : Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                },
            ],
        ],
    ],
]);?>

</div>
