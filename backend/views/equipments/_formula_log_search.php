<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatisticsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formula-log-search">
    <?php $form = ActiveForm::begin([
    'action' => ['formula-adjustment-log?equip_code=' . $equipCode],
    'method' => 'get',
]);?>

        <div class="form-group  form-inline">
            <?=$form->field($model, 'begin_date')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['readonly' => true]);?>
            <?=$form->field($model, 'end_date')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['readonly' => true]);?>
            <div class="form-group">
                <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
            </div>
        </div>

    <?php ActiveForm::end();?>

</div>
