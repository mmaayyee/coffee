<?php

use common\models\Equipments;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal fade" id="unBind" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">解绑</h4>
            </div>
            <div class="modal-body">
            <?php $form = ActiveForm::begin(['action' => '/equipments/un-bind', 'method' => 'post']);?>

            <?=$form->field($scmWarehouse, 'name')->widget(Select2::classname(), ['data' => Equipments::getMaterialWarehouseArr(), 'options' => ['placeholder' => '请选择分库'], 'pluginOptions' => ['allowClear' => true]])?>
            <?=Html::hiddenInput('ScmWarehouse[equip_id]', $equip_id)?>
            <div class="form-group">
                <?=Html::submitButton("提交", ["class" => "btn btn-success"])?>
            </div>
             <?php ActiveForm::end();?>
            </div>
        </div>
    </div>
</div>