<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltStrategy */

$this->title = '添加灯带策略';
$this->params['breadcrumbs'][] = ['label' => '灯带策略管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-strategy-create">

    <?= $this->render('_form', [
        'model' => $model,
        'lightBeltList'=>$lightBeltList,
    ]) ?>

</div>
