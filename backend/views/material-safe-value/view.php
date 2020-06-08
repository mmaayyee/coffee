<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Equipments;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterialSafeValue */

$this->title = $model->equipment_id;
$this->params['breadcrumbs'][] = ['label' => '料仓预警值管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-safe-value-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'equipmentId' => $model->equipment_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'equipmentId' => $model->equipment_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除该项吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'build_id',
                'value' => Equipments::findOne($model->equipment_id)->build->name
            ],
            [
                'attribute' => 'equipment_id',
                'label' => '料仓预警值',
                'format' => 'html',
                'value' => \backend\models\MaterialSafeValue::getStockSafeValue($model->equipment_id)
            ],
        ],
    ]) ?>

</div>
