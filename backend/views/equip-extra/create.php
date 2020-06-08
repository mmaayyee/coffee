<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EquipExtra */

$this->title = '添加设备附件';
$this->params['breadcrumbs'][] = ['label' => '设备附件', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-extra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
