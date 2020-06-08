<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\EquipSymptom;

/* @var $this yii\web\View */
/* @var $model backend\models\OrganizationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['trouble-list'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">

        <?= $form->field($model, 'content')->dropDownList(EquipSymptom::getSymptomIdNameArr(true))->label('故障原因') ?>

        <?php echo $form->field($model, 'build_id')->textInput()->label('地区名称') ?>

        <?=$form->field($model, 'start_time')->label('开始时间')->widget(\yii\jui\DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput();?>

        <?=$form->field($model, 'end_time')->label('结束时间')->widget(\yii\jui\DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput();?>

        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
