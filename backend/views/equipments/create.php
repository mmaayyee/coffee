<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Equipments */

$this->title = '添加设备批次';
$this->params['breadcrumbs'][] = ['label' => '设备信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => 	$model,
        'branch'=>	$branch,
    ]) ?>

</div>
