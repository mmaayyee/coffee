<?php

use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmWarehouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '库信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Yii::$app->user->can('添加库信息') ? Html::a('添加库信息', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'name',
        'address',
        [
            'attribute' => 'use',
            'value'     => function ($model) {
                return $model->getWarehouseUse();
            },
        ],
        [
            'attribute' => 'organization_id',
            'value'     => function ($model) use($searchModel) {
                return $model->organization_id && isset($searchModel->orgArr[$model->organization_id]) ? $searchModel->orgArr[$model->organization_id] : '';
            },
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Yii::$app->user->can('编辑库信息') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '编辑']) : '';
                }
            ]
        ],
    ],
]);?>

</div>
