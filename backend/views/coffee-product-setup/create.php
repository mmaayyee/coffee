<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeProductSetup */

$this->title = 'Create Coffee Product Setup';
$this->params['breadcrumbs'][] = ['label' => 'Coffee Product Setups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-product-setup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
