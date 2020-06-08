<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWater */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '零售活动人员列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
