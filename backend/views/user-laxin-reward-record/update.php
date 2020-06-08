<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLaxinRewardRecord */

$this->title = 'Update User Laxin Reward Record: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Laxin Reward Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->laxin_reward_record_id, 'url' => ['view', 'id' => $model->laxin_reward_record_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-laxin-reward-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
