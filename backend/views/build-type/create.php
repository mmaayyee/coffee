<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */

$this->title = '创建楼宇类型';
$this->params['breadcrumbs'][] = ['label' => '楼宇类型', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
