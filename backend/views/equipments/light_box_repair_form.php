<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\WxMember;
use common\models\EquipTraffickingSuppliers;

?>
<div class="modal fade" id="lightBoxRepair" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">发起灯箱报修</h4>
            </div>
            <div class="modal-body">
            <?php $form = ActiveForm::begin(['action'=>'/equip-light-box-repair/create', 'method'=>'post']);?>

            <?= $form->field($lightBoxRepairModel, "supplier_id")->dropDownList(EquipTraffickingSuppliers::getIdNameArr())?>

            <?= $form->field($lightBoxRepairModel, "remark")->textarea() ?>

            <?= Html::hiddenInput('EquipLightBoxRepair[build_id]',$build_id) ?>
            <?= Html::hiddenInput('EquipLightBoxRepair[equip_id]',$equip_id) ?>

            <div class="form-group">
                <?= Html::submitButton("提交", ["class" => "btn btn-success"]) ?>
            </div>
             <?php ActiveForm::end();?>
            </div>
        </div>
    </div>
</div>