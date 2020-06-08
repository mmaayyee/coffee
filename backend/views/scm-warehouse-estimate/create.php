<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseEstimate */

$this->title = 'Create Scm Warehouse Estimate';
$this->params['breadcrumbs'][] = ['label' => 'Scm Warehouse Estimates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-estimate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
