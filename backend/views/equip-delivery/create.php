<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDelivery */

$this->title = '添加销售投放';
$this->params['breadcrumbs'][] = ['label' => '销售投放管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
