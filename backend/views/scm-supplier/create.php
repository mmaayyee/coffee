<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ScmSupplier */

$this->title = '添加供应商';
$this->params['breadcrumbs'][] = ['label' => '供应商管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-supplier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
