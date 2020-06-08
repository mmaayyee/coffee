<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterial */

$this->title                   = 'Create Scm User Surplus Material';
$this->params['breadcrumbs'][] = ['label' => 'Scm User Surplus Materials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-user-surplus-material-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
