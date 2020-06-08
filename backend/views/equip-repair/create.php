<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipRepair */

$this->title = '上报新故障';
$this->params['breadcrumbs'][] = ['label' => '报修记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-repair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
