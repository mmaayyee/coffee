<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBox */

$this->title = '编辑灯箱: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '灯箱列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑灯箱';
?>
<div class="equip-light-box-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
