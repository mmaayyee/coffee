<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Organization;
use backend\models\Manager;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-warehouse-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-line form-inline">
        <?= $form->field($model, 'name') ?>

        <?= $form->field($model, 'use')->dropDownList($model->wareHouseUse()) ?>
        
        <?php if (Manager::getManagerBranchID() == 1) {?>
            <?= $form->field($model, 'organization_id')->dropDownList(Organization::getOrgIdNameArr(['>','org_id',1])) ?>
        <?php } ?>
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
