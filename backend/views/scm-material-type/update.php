<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmMaterialType */

$this->title                   = '编辑物料分类: ' . $model->material_type_name;
$this->params['breadcrumbs'][] = ['label' => '物料分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑物料分类';
?>
<div class="scm-material-type-update">
    <h1><?=Html::encode($this->title)?></h1>
    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
