<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentTaskSetting */

$this->title = '添加公司设备类型日常任务';
$this->params['breadcrumbs'][] = ['label' => '公司设备类型日常任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-task-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<style type="text/css">
	.btn-success{
		margin-bottom: 0;
	}
</style>
