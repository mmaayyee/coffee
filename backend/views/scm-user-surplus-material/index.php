<?php

use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\ScmMaterialType;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmUserSurplusMaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '剩余物料管理';
$this->params['breadcrumbs'][] = ['label' => '配送员管理', 'url' => ['/distribution-user/view', 'id' => $searchModel->author]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-user-surplus-material-index">
    <h1><?=Html::encode($this->title)?></h1>
    <p>
        <?=Html::a('返回上一页', '/distribution-user/view?id=' . $searchModel->author, ['class' => 'btn btn-primary'])?>
    </p>
    <h2>剩余整包物料</h2>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '物料名称',
            'value' => function ($model) {
                return isset($model->material) ? $model->material->name : '';
            },
        ],
        [
            'label' => '规格',
            'value' => function ($model) {
                return $model->material->weight > 0 ? $model->material->weight . $model->material->materialType->spec_unit : '';
            },
        ],
        [
            'label' => '物料数量',
            'value' => function ($model) {
                return isset($model->material) ? $model->material_num . $model->material->materialType->unit : '';
            },
        ],
        [
            'label' => '物料拥有人',
            'value' => function ($model) {
                return isset($model->user) ? $model->user->name : '';
            },
        ],
        // ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>
</div>

<div class="scm-user-surplus-material-index">

    <h2>剩余散料</h2>

    <?=GridView::widget([
        'dataProvider' => $dataGramProvider,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '物料分类',
                'value' => function ($model) {
                    return isset($model->material_type_id) ? $model->materialType->material_type_name : '';
                },
            ],
            [
                'label' => '供应商',
                'value' => function ($model) {
                    return isset($model->supplier_id) ? $model->supplier->name : '';
                },
            ],
            [
                'label' => '物料重量',
                'value' => function ($model) {
                    $unit = $model->materialType->type == ScmMaterialType::TYPE_ON ? $model->materialType->weight_unit : $model->materialType->unit;
                    return isset($model->gram) ? $model->gram.$unit : '';
                },
            ],
            [
                'label' => '物料拥有人',
                'value' => function ($model) {
                    return isset($model->author) ? \common\models\WxMember::getNameOne($model->author) : '';
                },
            ],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);?>
</div>