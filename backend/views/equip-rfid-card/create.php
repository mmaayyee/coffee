<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCard */

$this->title = '添加RFID卡';
$this->params['breadcrumbs'][] = ['label' => 'RFID卡管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
