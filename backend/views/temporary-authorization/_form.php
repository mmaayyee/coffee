<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 11:23
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\TemporaryAuthorization;

unset(TemporaryAuthorization::$state[TemporaryAuthorization::FAILED]);
$state = TemporaryAuthorization::$state;
?>
<div class="temporary-authorization-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if(!$model->isNewRecord){ ?>
        <?= $form->field($model, 'state')->dropDownList($state) ?>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ["formId" =>"test",'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
