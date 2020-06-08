<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielBoxSpeed */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materiel-box-speed-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<?php if($model->isNewRecord):?>

		<?= $form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList()) ?>

		<?= $form->field($model, 'material_type_id')->dropDownList(Api::getMaterialTypeList()) ?>
    	
    	<?= $form->field($model, 'speed')->textInput(['maxlength' => 20]) ?>

    <?php else: ?>
    	<?=$form->field($model, 'materiel_box_speed_id')->hiddenInput()->label(false);?>
		<?= $form->field($model, 'speed')->textInput(['maxlength' => 20]) ?>
	<?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
