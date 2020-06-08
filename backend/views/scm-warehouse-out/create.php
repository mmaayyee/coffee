<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseOut */

$this->title = '添加出库单';
$this->params['breadcrumbs'][] = ['label' => '出库单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-out-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
