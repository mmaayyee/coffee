<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterialSureRecord */

$this->title = 'Create Scm User Surplus Material Sure Record';
$this->params['breadcrumbs'][] = ['label' => 'Scm User Surplus Material Sure Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-user-surplus-material-sure-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
