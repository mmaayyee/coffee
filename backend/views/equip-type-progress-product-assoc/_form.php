<?php

use common\models\EquipProductGroupApi;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProgressProductAssoc */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '进度条管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
	var equipTypeProcess = <?php echo $equipTypeProcess; ?>;
</script>
<div class="equip-type-progress-product-assoc-form">

    <?php $form = ActiveForm::begin();?>

    <div class="form-inline form-group building-product_id">
        <label>产品名称</label>
        <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'product_id',
    'data'          => EquipProductGroupApi::getProductList(),
    'options'       => [
        'placeholder' => '请选择产品',
        // "multiple"  => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
        <div class="help-block"></div>
    </div>
    <input type="hidden" class="is_new_record" value="<?php echo $model->isNewRecord ?>">
    <input type="hidden" class="isCreateArray" value="<?php echo $isCreateArray ?>">
    <?=$this->render('_procedure_install')?>
    <div class="form-group">
        <?=Html::Button('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

<script type="text/javascript">
function checkClick(obj){
    var parent = $(obj).parent().parent();
    if($(obj).is(":checked") == true){
        one = parent.next().find("input").attr("check-type")
        parent.next().find("input").attr("checkTypeInfo",one)
        parent.next().find("input").attr("check-type",one+" number number5")

        two = parent.next().next().find("input").attr("check-type")
        parent.next().next().find("input").attr("checkTypeInfo",two)
        parent.next().next().find("input").attr("check-type",two +" number number5")
    }else{
        parent.next().find("input").removeAttr("check-type")
        one = parent.next().find("input").attr("checkTypeInfo")
        parent.next().find("input").attr("check-type",one)

        parent.next().next().find("input").removeAttr("check-type")
        two = parent.next().next().find("input").attr("checkTypeInfo")
        parent.next().next().find("input").attr("check-type",two)
    }
}
</script>
