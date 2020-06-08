<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ClearEquip */

$this->title = '设备类型: ' . ' ' . $model->equipment_name.'     '."清洗类型:".' '.$model->clear_code_name;
$this->params['breadcrumbs'][] = ['label' => '清洗设备类型列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="clear-equip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
