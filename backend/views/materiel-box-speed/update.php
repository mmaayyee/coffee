<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielBoxSpeed */

$this->title = '设备类型: ' . ' ' . $model->equipment_name.'     '."料盒类型:".' '.$model->material_type_name;
$this->params['breadcrumbs'][] = ['label' => '料盒速度列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="materiel-box-speed-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
