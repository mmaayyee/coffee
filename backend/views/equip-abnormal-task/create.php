<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipAbnormalTask */

$this->title = 'Create Equip Abnormal Task';
$this->params['breadcrumbs'][] = ['label' => 'Equip Abnormal Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-abnormal-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
