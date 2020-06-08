<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WxDepartment */

$this->title = '新建部门';
$this->params['breadcrumbs'][] = ['label' => '部门管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-department-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
