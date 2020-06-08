<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskEquipSetting */

$this->title = '添加日常任务设置';
$this->params['breadcrumbs'][] = ['label' => '日常任务设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-equip-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'flag' => $flag
    ]) ?>

</div>
