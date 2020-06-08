<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MaterialSafeValue */

$this->title = '添加料仓预警值';
$this->params['breadcrumbs'][] = ['label' => '料仓预警值管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-safe-value-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
