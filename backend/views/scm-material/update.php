<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Scmmaterial */

$this->title                   = '物料信息修改: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '物料信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scm-material-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
