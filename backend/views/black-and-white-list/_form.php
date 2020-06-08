<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="user-tag-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'market_type')->dropDownList($model->marketType)?>

    <?=$form->field($model, 'user_list_type')->dropDownList($model->userListType)?>

    <?=$form->field($model, 'black_white_list_remarks')->textArea(['maxlenth' => 100])?>
    <?php if ($model->add_type == $model::INPUT_ADD): ?>
        <?=$form->field($model, 'user_content')->textArea()->hint('多个楼宇或者手机号需通过英文竖线“|”分割')?>
    <?php elseif ($model->add_type == $model::IMPORT_ADD): ?>
        <?=$form->field($model, 'user_content')->fileInput()->hint('导入文件必须是TXT格式的，且每个楼宇或者手机号独占一行')?>
    <?php endif?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
