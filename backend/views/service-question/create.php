<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ServiceQuestion */

$this->title = '添加问题';
$this->params['breadcrumbs'][] = ['label' => '问题', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-question-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'category' => $category,
    ]) ?>

</div>
