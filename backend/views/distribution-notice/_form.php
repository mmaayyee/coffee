<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionNotice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-notice-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'receiver')->checkBoxList($wxMemberArr)?>

    <?=$form->field($model, 'content')->textarea(['maxlength' => 200, 'rows' => 6])?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'save'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
