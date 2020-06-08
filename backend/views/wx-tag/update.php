<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WxTag */

$this->title                   = '更新标签: ' . ' ' . $model->tagname;
$this->params['breadcrumbs'][] = ['label' => '标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新标签';
?>
<div class="wx-tag-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
