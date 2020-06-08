<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */

$this->title = '修改运维任务' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '运维任务', 'url' => ['distribution-task/index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="distribution-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
