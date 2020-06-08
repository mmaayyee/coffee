<?php

use yii\helpers\Html;


/* @var $this yii\web\View */

$this->title = 'Create Equip Light Box Repair';
$this->params['breadcrumbs'][] = ['label' => 'Equip Light Box Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-light-box-repair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
