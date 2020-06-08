<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeProductSetup */

$this->title = 'Update Coffee Product Setup: ' . $model->setup_id;
$this->params['breadcrumbs'][] = ['label' => 'Coffee Product Setups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->setup_id, 'url' => ['view', 'id' => $model->setup_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="coffee-product-setup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
