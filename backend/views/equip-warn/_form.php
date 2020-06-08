<?php

use backend\models\EquipWarn;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipWarn */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/equip_warn.js?v=1.0', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<script type="text/javascript">
    var hournum = <?php echo json_encode(EquipWarn::$intervalTime); ?>;
    var sendtype = <?php echo json_encode(EquipWarn::$noticeType); ?>;
    var report_setting = '<?php echo $model->report_setting ?>';
    var userid = '<?php echo json_encode($model->userid); ?>';
</script>

<div class="equip-warn-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'warn_content')->widget(\kartik\select2\Select2::classname(), [
    'data'          => EquipWarn::$warnContent,
    'options'       => ['placeholder' => '请选择报警内容'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])?>
    <?=$form->field($model, 'userid')->checkBoxList(EquipWarn::$position)?>

    <?=$form->field($model, 'continuous_number')->dropDownList(EquipWarn::$continuousNumber)?>

    <?=$form->field($model, 'notice_type')->checkBoxList(EquipWarn::$noticeType)?>

    <?=$form->field($model, 'interval_time')->dropDownList(EquipWarn::$intervalTime)?>

    <?=$form->field($model, 'is_report')->dropDownList([2 => '否', 1 => '是'])?>
    <div id="report_setting" style="display:none;">
        <?=$form->field($model, 'report_num')->dropDownList(EquipWarn::$reportNum)?>
        <div id="report_num_set"></div>
    </div>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'wareSave'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
