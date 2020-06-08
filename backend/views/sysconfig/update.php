<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sysconfig */

$this->title = '编辑系统设置: ' . ' ' . $model->config_desc;
$this->params['breadcrumbs'][] = ['label' => '系统设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="sysconfig-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
