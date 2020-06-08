<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Grind */

$this->title = '编辑';
$this->params['breadcrumbs'][] = ['label' => '预磨豆设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="grind-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
