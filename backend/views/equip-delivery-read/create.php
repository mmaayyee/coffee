<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipDeliveryRead */

$this->title = 'Create Equip Delivery Read';
$this->params['breadcrumbs'][] = ['label' => 'Equip Delivery Reads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-delivery-read-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
