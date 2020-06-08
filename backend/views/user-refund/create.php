<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserRefund */

$this->title = 'Create User Refund';
$this->params['breadcrumbs'][] = ['label' => 'User Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-refund-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
