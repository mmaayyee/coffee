<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\WxMember;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskSearch */
/* @var $form yii\widgets\ActiveForm */
// var_dump(Yii::$app->request->get());exit();
?>

<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['distribution-task-record'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择配送员</label>
            <div style="display: inline-block;width: 200px;vertical-align: middle;" >
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'assign_userid',
                'data' => WxMember::getDistributionUserArr(3),
                'options' => ['multiple' => false, 'placeholder' => '请选择配送员'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            </div>
        </div>
        <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
        <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
        <?= $form->field($model, 'equip_id')->hiddenInput()->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
