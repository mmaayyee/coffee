<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/5/15
 * Time: 下午3:55
 */

use common\models\Building;
use common\models\EquipTask;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EquipExtra;

$this->registerJsFile('@web/js/equipTask.js?v=1.3', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style type="text/css">
    #equiptask-content label{
        width:20%;
    }
</style>
<div class="equip-task-form">

    <?php $form = ActiveForm::begin();?>
    <?php if ($model->relevant_id) {?>
        <?=$form->field($model, 'build_id')->dropDownList(Building::getPreDeliveryBuildList(), ['data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build', 'disabled' => 'disabled'])?>
    <?php } else {
        ?>
        <?=$form->field($model, 'build_id')->widget(\kartik\select2\Select2::classname(), [
            'data'          => Building::getBusinessBuildByOrgId(),
            'options'       => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])?>
    <?php }?>
    <p>所选楼宇：<span id="build_name"></span></p>
    <p>设备编号：<span id="equip_code"></span></p>
    <p>设备类型：<span id="equip_model"></span></p>

    <?=$form->field($model, 'content')->checkboxList(EquipExtra::getEquipExtra())->label('设备附件')?>
    <?=$form->field($model, 'remark')->label('备注')->textarea(['rows' => 5])?>


    <?=Html::hiddenInput('EquipTask[equip_id]', '', ['id' => 'equip_id'])?>

    <?=Html::hiddenInput('userid', $model->assign_userid, ['id' => 'userid'])?>

    <?=$form->field($model, 'assign_userid')->label('指定负责人')->dropDownList(['' => '请选择'])?>

    <div class="form-group">

        <?=Html::button($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
