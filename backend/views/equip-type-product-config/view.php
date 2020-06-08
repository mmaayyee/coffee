<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProductConfig */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equip Type Product Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-type-product-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'equip_type_id',
            'product_id',
            'cf_choose_sugar',
            'half_sugar',
            'full_sugar',
            'brew_up',
            'brew_down',
        ],
    ]) ?>

</div>
