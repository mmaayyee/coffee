<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionUser */

$this->title = '更新运维人员: ' . ' ' . $model->user->name;
$this->params['breadcrumbs'][] = ['label' => '运维人员列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user->name, 'url' => ['view', 'id' => $model->userid]];
$this->params['breadcrumbs'][] = '更新运维人员';
?>
<div class="distribution-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
