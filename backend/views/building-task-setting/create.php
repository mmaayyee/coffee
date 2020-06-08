<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuildingTaskSetting */

$this->title = '新建楼宇日常任务';
$this->params['breadcrumbs'][] = ['label' => '楼宇日常任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-task-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
