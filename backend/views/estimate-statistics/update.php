<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatistics */

$this->title                   = '修改运维预估单';
$this->params['breadcrumbs'][] = ['label' => '运维预估单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="estimate-statistics-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'                  => $model,
    'estimateShowData'       => $estimateShowData,
    'estimateMaterialDetail' => $estimateMaterialDetail,
])?>

</div>
