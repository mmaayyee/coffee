<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserLaxinRewardRecord */

$this->title = 'Create User Laxin Reward Record';
$this->params['breadcrumbs'][] = ['label' => 'User Laxin Reward Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-laxin-reward-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
