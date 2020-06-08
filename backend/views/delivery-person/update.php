<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryPerson */

$this->title                   = '编辑配送人员';
$this->params['breadcrumbs'][] = ['label' => '配送人员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="delivery-person-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
