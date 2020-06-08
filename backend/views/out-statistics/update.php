<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OutStatistics */

$this->title                   = '运维出库单复审';
$this->params['breadcrumbs'][] = ['label' => '运维出库单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '复审';
?>
<div class="out-statistics-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'                 => $model,
    'examineMaterialDetail' => $examineMaterialDetail,
])?>

</div>
