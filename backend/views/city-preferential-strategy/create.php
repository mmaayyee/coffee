<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */

$this->title                   = '添加城市优惠策略';
$this->params['breadcrumbs'][] = ['label' => '城市优惠策略', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-type-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'  => $model,
    'cities' => $cities,
])?>

</div>
