<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltScenario */

$this->params['breadcrumbs'][] = ['label' => '灯带场景管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="light-belt-scenario-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
