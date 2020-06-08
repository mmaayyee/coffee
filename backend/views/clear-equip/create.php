<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ClearEquip */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '清洗设备类型列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clear-equip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
