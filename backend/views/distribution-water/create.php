<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWater */

$this->title = '创建水单';
$this->params['breadcrumbs'][] = ['label' => '水单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-water-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
