<?php
use backend\models\EquipSymptom;
use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/addRepairTask.js?v=3.4', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style type="text/css">
	.field-equiptask-content label {
	    margin-right: 1.5rem;
	}
	.field-equiptask-content label input[type="checkbox"] {
	    vertical-align: middle;
	    margin: 0 0 .1rem;
	}
</style>
<div>
    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'build_id')->widget(Select2::className(), [
        'data'          => \common\models\Building::getOperationBuildStore(1,$userId),
        'options'       => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/distribution-task/ajax-get-build'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
    <p>所选楼宇：<span class="build" id="build_name"></span></p>
    <p>设备编号：<span class="buildCode" id="equip_code"></span></p>
    <p>设备类型：<span class="equipCode" id="equip_model"></span></p>
    <div id="allmap"></div>
    <?=$form->field($model, 'content')->checkBoxList(EquipSymptom::getSymptomIdNameArr())?>

    <?=$form->field($model, 'remark')->textarea(['maxlength' => 255, 'rows' => 6])?>

    <input type="hidden" class="hide_assign_userid" value="<?php echo $userId ?>">

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>
</div>