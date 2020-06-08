<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Organization */

$this->title = '更新机构: ' . $model->org_id;
$this->params['breadcrumbs'][] = ['label' => '机构列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->org_id, 'url' => ['view', 'id' => $model->org_id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="organization-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
