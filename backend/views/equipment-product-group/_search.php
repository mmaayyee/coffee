<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;
use kartik\select2\Select2;
use common\models\EquipProductGroupApi;
$getEquipTypeList = Api::getEquipTypeList();
?>

<div class="equipment-product-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-group form-inline">
    <?= $form->field($model, 'group_name') ?>

    <?= $form->field($model, 'group_desc') ?>

    <?= $form->field($model, 'setup_get_coffee')->dropDownList([ ''=>'请选择', '0'=>'是', '1'=>'否']) ?>

    <?= $form->field($model, 'setup_no_coffee_msg') ?>

    <?php echo $form->field($model, 'release_status')->dropDownList([''=>'请选择', '0'=>'未发布', '1'=> '已发布']) ?>

    <?php echo $form->field($model, 'equip_type')->dropDownList($getEquipTypeList) ?>

    <div class="form-group">
            <label>请选择产品组料仓信息</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'pro_group_stock_info_id',
                'data' =>  EquipProductGroupApi::getGroupStockIdAndName(),
                'options' => ['multiple' => false, 'placeholder' => '请选择产品组料仓信息'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            </div>
        </div>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>
