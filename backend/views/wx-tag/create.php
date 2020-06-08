<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WxTag */

$this->title = '新建标签';
$this->params['breadcrumbs'][] = ['label' => '标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-tag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
