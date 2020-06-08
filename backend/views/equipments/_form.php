<?php

use backend\models\Organization;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Equipments */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="equipments-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'equip_type_id')->dropDownList($model->getEquipTypeArray())?>
    <!-- 总部 -->
    <?php if ($branch == 1): ?>
        <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => Organization::getOrgIdNameArr(['>', 'org_id', 1], 2),
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择所属机构'],
    'pluginOptions' => ['allowClear' => true]])?>
        <?=$form->field($model, 'warehouse_id')->dropDownList(['' => '请先选择设备类型'])?>
    <?php else: ?>
        <?=$form->field($model, 'warehouse_id')->dropDownList($model->getWarehousArray($branch))?>
    <?php endif?>
    <?=$form->field($model, 'number')->textInput()?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php

$url = Url::to(["equipments/equip-warehouse"]);
$this->registerJs('
    var org_id  =   $("#equipments-org_id").val();
    if (org_id) {
        $.post("' . $url . '",{org_id:org_id},function(data){
            $("#equipments-warehouse_id").html(data);
        });
    }
    //ajax实现改变分公司值时，自动出现该分公司下的分库
    $("#equipments-org_id").change(function(){
        var org_id  =   $("#equipments-org_id").val();
        $.post("' . $url . '",{org_id:org_id},function(data){
            $("#equipments-warehouse_id").html(data);
        });

    });
');

?>

