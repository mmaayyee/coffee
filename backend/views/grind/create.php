<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Grind */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '预磨豆设备列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grind-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
