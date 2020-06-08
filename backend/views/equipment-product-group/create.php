<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentProductGroup */

$this->title = '创建产品组';
$this->params['breadcrumbs'][] = ['label' => '产品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-product-group-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
