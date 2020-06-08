<?php

use backend\models\Organization;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrganizationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline">
    <?=$form->field($model, 'org_id')?>

    <?=$form->field($model, 'org_name')?>

    <?=$form->field($model, 'org_city')?>

    <?=$form->field($model, 'parent_id')->dropDownList(Organization::getBranchArray())?>

    <?=$form->field($model, 'organization_type')->dropDownList(Organization::$organizationType);?>

    <?=$form->field($model, 'is_replace_maintain')->dropDownList($model->instead);?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
