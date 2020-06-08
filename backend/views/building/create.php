<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = '添加点位';
$this->params['breadcrumbs'][] = ['label' => '点位列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'             => $model,
    'buildLevelArr'     => $buildLevelArr,
    'bdMaintenanceUser' => $bdMaintenanceUser,
    'orgIdNameList'     => $orgIdNameList,
    'submitAction'      => 'create',
    'couponGroupList'   => $couponGroupList,
])?>

</div>
