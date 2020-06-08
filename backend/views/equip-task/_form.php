<?php

use backend\models\EquipSymptom;
use common\models\Building;
use common\models\EquipTask;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
        'data'          => Building::getOperationBuildList(),
        'options'       => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
        <?php }?>
        <p>所选楼宇：<span id="build_name"></span></p>
        <?php if($model->task_type && $model->task_type !== EquipTask::TRAFFICKING_TASK):?>
        <p>设备编号：<span id="equip_code"></span></p>
        <?php endif;?>
        <p>设备类型：<span id="equip_model"></span></p>
        <?php if (!$model->task_type || $model->task_type == EquipTask::MAINTENANCE_TASK) {?>
            <?=$form->field($model, 'content')->checkboxList(EquipSymptom::getSymptomIdNameArr())?>
            <?=$form->field($model, 'remark')->label('备注')->textarea(['rows' => 5])?>
        <?php } else {?>
            <?php if ($model->relevant_id) {?>
                <label class="control-label">任务内容</label>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo $model->content;?></li>
                </ul>
            <?php } else {?>
            <?=$form->field($model, 'content')->textarea()?>
            <?php }?>
        <?php }?>

        <?=Html::hiddenInput('task_type',$model->task_type,['id' => 'task_type']);?>

        <?=Html::hiddenInput('EquipTask[equip_id]', '', ['id' => 'equip_id'])?>

        <?=Html::hiddenInput('userid', $model->assign_userid, ['id' => 'userid'])?>

    <?=$form->field($model, 'assign_userid')->label('指定负责人')->dropDownList(['' => '请选择'])?>

    <div class="form-group">

        <?=Html::button($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
