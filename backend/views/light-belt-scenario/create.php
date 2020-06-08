<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltScenario */

$this->title = '添加灯带场景名称';
$this->params['breadcrumbs'][] = ['label' => '灯带场景管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-scenario-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
