<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterielMonth */

$this->title = '修改楼宇物料消耗: ' . ' ' . $model['build_name'];
$this->params['breadcrumbs'][] = ['label' => '差异值', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="materiel-month-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
