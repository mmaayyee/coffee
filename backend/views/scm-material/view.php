<?php

use backend\models\ScmMaterialType;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Scmmaterial */

$this->title                   = $model->name;
$this->params['breadcrumbs'][] = ['label' => '物料信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-material-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?php if (Yii::$app->user->can('编辑物料')) {?>
        <?=Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
        <?php }?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'supplier_id',
            'value'     => !empty($model->supplier) ? $model->supplier->name : '',
        ],
        'name',
        'weight',
        [
            'attribute' => 'material_type',
            'value'     => $model->material_type ? ScmMaterialType::getIdNameArr()[$model->material_type] : '',
        ],
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '',
        ],
    ],
])?>

</div>
