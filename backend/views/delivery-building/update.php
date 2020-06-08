<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryBuilding */

$this->title                   = '编辑配送点位';
$this->params['breadcrumbs'][] = ['label' => '配送点位', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->delivery_building_id, 'url' => ['view', 'id' => $model->delivery_building_id]];
$this->params['breadcrumbs'][] = '编辑配送点位';
?>
<div class="delivery-building-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'        => $model,
    'buildingList' => $buildingList,
    'personList'   => $personList,
    'sign'         => 'edit',
])?>

</div>
