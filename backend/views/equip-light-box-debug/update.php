<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBoxDebug */

$this->title = '修改灯箱调试项: ' . ' ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => '灯箱管理', 'url' => ['/equip-light-box/index']];
$this->params['breadcrumbs'][] = ['label' => '灯箱调试项管理', 'url' => ['index?EquipLightBoxDebugSearch[light_box_id]='.$model->light_box_id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="equip-light-box-debug-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
