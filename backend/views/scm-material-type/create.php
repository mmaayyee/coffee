<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmMaterialType */

$this->title                   = '添加物料分类';
$this->params['breadcrumbs'][] = ['label' => '物料分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-material-type-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
