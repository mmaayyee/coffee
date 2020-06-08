<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipSymptom */

$this->title = '添加故障现象';
$this->params['breadcrumbs'][] = ['label' => '故障现象', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-symptom-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
