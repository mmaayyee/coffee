<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipWarn */

$this->title = '添加异常报警设置';
$this->params['breadcrumbs'][] = ['label' => '异常报警设置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-warn-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
