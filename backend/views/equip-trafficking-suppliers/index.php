<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\EquipTraffickingSuppliersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '投放商列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-trafficking-suppliers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->user->can('添加投放商') ? Html::a('添加投放商', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'userid',
                'value' => function($model) {
                    return $model->user ? $model->user->name : '';
                }
            ],
            'mobile',
            'email:email',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}', 
                'buttons' => [
                    'update' => function($url) {
                        return Yii::$app->user->can('编辑投放商') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                    }
                ]
            ],
        ],
    ]); ?>
</div>
