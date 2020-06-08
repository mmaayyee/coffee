<?php

use common\models\WxMember;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('@web/js/equipTask.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="equip-task-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'content')->label('任务详情')->textarea(['rows' => 5])?>

    <?=Html::hiddenInput('EquipTask[build_id]', $model->build_id)?>

    <?=Html::hiddenInput('EquipTask[equip_id]', $model->equip_id)?>

    <?=Html::hiddenInput('EquipTask[light_box_repair_id]', $model->light_box_repair_id ? $model->light_box_repair_id : '0')?>

    <?=$form->field($model, 'assign_userid')->label('指定负责人员')->dropDownList(WxMember::equipDisUserArr($model->build->org_id, 3))?>

    <div class="form-group">

        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
