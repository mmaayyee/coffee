<?php
use backend\models\ScmMaterialType;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmMaterialTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '物料分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-material-type-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加物料分类') ? Html::a('添加物料分类', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'material_type_name',
        'unit',
        'weight_unit',
        'spec_unit',
        'new_spec_unit',
        [
            'attribute' => 'type',
            'value'     => function ($model) {
                return ScmMaterialType::$type[$model->type];
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('编辑物料分类') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
            ],
        ],
    ],
]);?>
</div>
