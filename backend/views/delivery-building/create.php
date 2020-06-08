<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryBuilding */

$this->title                   = '新增外卖点位';
$this->params['breadcrumbs'][] = ['label' => '外卖点位首页', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delivery-building-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'        => $model,
    'buildingList' => $buildingList,
    'personList'   => $personList,
    'sign'         => 'add',
])?>

</div>
