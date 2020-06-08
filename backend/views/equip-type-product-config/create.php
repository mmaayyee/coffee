<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProductConfig */

$this->title = 'Create Equip Type Product Config';
$this->params['breadcrumbs'][] = ['label' => 'Equip Type Product Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-type-product-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
