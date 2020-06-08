<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserConsume */

$this->title = 'Create User Consume';
$this->params['breadcrumbs'][] = ['label' => 'User Consumes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-consume-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
