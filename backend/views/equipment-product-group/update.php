<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentProductGroup */

$this->title = '修改产品组';
$this->params['breadcrumbs'][] = ['label' => '产品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->product_group_id, 'url' => ['view', 'id' => $model->product_group_id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="equipment-product-group-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
