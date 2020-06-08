<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WxMember */

$this->title = '更新成员: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->userid]];
$this->params['breadcrumbs'][] = '更新成员';
?>
<div class="wx-member-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
