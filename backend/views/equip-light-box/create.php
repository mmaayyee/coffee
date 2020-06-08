<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBox */

$this->title = '添加灯箱';
$this->params['breadcrumbs'][] = ['label' => '灯箱列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-light-box-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
