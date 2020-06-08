<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Organization */

$this->title = '创建机构';
$this->params['breadcrumbs'][] = ['label' => '机构列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
