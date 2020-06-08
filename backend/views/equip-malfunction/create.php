<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipMalfunction */

$this->title = '添加故障原因';
$this->params['breadcrumbs'][] = ['label' => '故障原因管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-malfunction-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
