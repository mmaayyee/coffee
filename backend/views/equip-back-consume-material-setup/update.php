<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipAbnormalTask */

$this->title                   = '编辑工厂模式物料消耗设置';
$this->params['breadcrumbs'][] = ['label' => '工厂模式物料消耗设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-abnormal-task-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'               => $model,
    'equipTypeIdNameList' => $equipTypeIdNameList])?>

</div>
