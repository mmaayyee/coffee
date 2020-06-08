<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */

$this->title                   = '编辑城市优惠策略: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '城市优惠策略', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="build-type-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'  => $model,
    'cities' => $cities,
])?>

</div>
