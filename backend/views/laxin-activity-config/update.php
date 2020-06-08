<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LaxinActivityConfig */

$this->title                   = '更新拉新活动设置';
$this->params['breadcrumbs'][] = ['label' => '拉新活动设置', 'url' => ['view']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="laxin-activity-config-update">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model'                 => $model,
    'couponGroupIdNameList' => $couponGroupIdNameList,
])?>

</div>
