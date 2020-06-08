<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = '修改点位: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '点位列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改点位';
?>
<div class="building-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'             => $model,
    'buildLevelArr'     => $buildLevelArr,
    'bdMaintenanceUser' => $bdMaintenanceUser,
    'orgIdNameList'     => $orgIdNameList,
    'submitAction'      => $submitAction,
    'couponGroupList'   => $couponGroupList,
])?>

</div>
