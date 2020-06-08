<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskEquipSetting */

$this->title = '更新日常任务设置: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '日常任务设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'flag' => $flag]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="distribution-task-equip-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'flag' => $flag
    ]) ?>

</div>
