<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipDebug */

$this->title = '添加设备调试项';
$this->params['breadcrumbs'][] = ['label' => '设备调试项管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-debug-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
