<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Manager;
use backend\models\Organization;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouse */
/* @var $form yii\widgets\ActiveForm */

//获取当前用户的分公司
$userId =   Yii::$app->user->identity->id;
$branch =   Manager::find()->where(['id'=>$userId])->asArray()->one()['branch'];

?>

<div class="scm-warehouse-form">

    <?php $form = ActiveForm::begin(); ?>
	
    <?= $form->field($model, 'name')->textInput(['maxlength' => 20]) ?>
	<?php if ($branch==1) { ?>
    	<?= $form->field($model, 'organization_id')->dropDownList(Organization::getOrgIdNameArr(['>','org_id',1])) ?>
	<?php } ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => 80]) ?>

    <?= $form->field($model, 'use')->dropDownList($model->wareHouseUse()) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
