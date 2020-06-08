<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouse */

$this->title = '添加库信息';
$this->params['breadcrumbs'][] = ['label' => '库信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
