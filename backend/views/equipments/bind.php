<?php

use common\models\Building;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
// 设置场景用于表单验证
$equipModel->scenario = 'bind';
?>
<div class="modal fade" id="bind" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">绑定</h4>
            </div>
            <div class="modal-body">
            <?php $form = ActiveForm::begin(['action' => '/equipments/bind', 'method' => 'post']);?>

            <?=$form->field($equipModel, "build_id")->dropDownList(Building::getNoDeliveryBuildIdNameArr($equipModel->org_id))?>

            <?=$form->field($equipModel, "pro_group_id")->dropDownList($equipModel::getProGroupArr($equipModel->equip_type_id))?>

            <?=$form->field($equipModel, "operation_status")->dropDownList($equipModel->operationStatusByConditionsArray(2))?>

            <?=$form->field($equipModel, "id")->hiddenInput()->label(false);?>

            <div class="form-group">
                <?=Html::submitButton("提交", ["class" => "btn btn-success"])?>
            </div>
             <?php ActiveForm::end();?>
            </div>
        </div>
    </div>
</div>