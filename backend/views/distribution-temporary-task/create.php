<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */

$this->title = '添加临时任务';
$this->params['breadcrumbs'][] = ['label' => '运维任务管理', 'url' => ['distribution-task/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
