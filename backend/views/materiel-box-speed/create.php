<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MaterielBoxSpeed */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '料盒速度列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-box-speed-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
