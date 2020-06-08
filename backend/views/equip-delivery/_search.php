<?php

use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDeliverySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-delivery-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">

         <?=$form->field($model, 'build_id')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['>', 'build_status', Building::PRE_DELIVERY])], 'options' => ['class' => 'form-control']])?>
         <div class="form-group">
            <label>所属分公司</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'orgId',
    'data'          => \backend\models\Organization::getBranchArray(),
    'options'       => ['placeholder' => '分公司'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'orgType')->dropDownList(\common\models\Equipments::$orgType)?>

        <?php echo $form->field($model, 'sales_person') ?>

        <?php echo $form->field($model, 'delivery_status')->dropDownList($model->equipDeliveryStatusArray()) ?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
