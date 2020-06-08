<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLanguageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-language-search">
      <div class="form-group  form-inline">
    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <?=$form->field($model, 'language_name')?>
    <div class="form-group">
            <label>对应饮品</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'language_product',
    'data'          => $productNameList,
    'options'       => ['multiple' => false, 'placeholder' => '请搜索指定饮品'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            </div>
            <?=$form->field($model, 'language_static')->dropDownList($onlineStaticList)->label('咖语状态')?>
    <?=$form->field($model, 'language_type')->dropDownList($languageTypeList)->label('咖语类型')?>

        </div>
          <?=$form->field($model, 'start_time')->widget(\janisto\timepicker\TimePicker::className(), [
              'mode'          => 'datetime',
              'clientOptions' => [
                  'dateFormat' => 'yy-mm-dd',
                  'timeFormat' => 'HH:mm:ss',
                  'showSecond' => true,
              ],
          ]);
          ?>
          <?=$form->field($model, 'end_time')->widget(\janisto\timepicker\TimePicker::className(), [
              'mode'          => 'datetime',
              'clientOptions' => [
                  'dateFormat' => 'yy-mm-dd',
                  'timeFormat' => 'HH:mm:ss',
                  'hour'       => 23,
                  'minute'     => 59,
                  'second'     => 59,
                  'showSecond' => true,
              ],
          ]);
          ?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>
</div>
</div>
