<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipTraffickingSuppliers */

$this->title = '添加投放商';
$this->params['breadcrumbs'][] = ['label' => '投放商列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-trafficking-suppliers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
