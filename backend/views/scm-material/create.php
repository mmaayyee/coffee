<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Scmmaterial */

$this->title                   = '添加物料';
$this->params['breadcrumbs'][] = ['label' => '物料信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-material-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
