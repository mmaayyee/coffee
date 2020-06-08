<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EquipProductGroupApi;
use kartik\select2\Select2;
use common\models\Api;
use yii\jui\AutoComplete;
use backend\models\EquipTypeProgressProductAssoc;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProgressProductAssocSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-type-progress-product-assoc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="form-group  form-inline">
        <?=$form->field($model, 'product_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' =>EquipTypeProgressProductAssoc::getProcessProductNameList() ], 'options' => ['class' => 'form-control']])?>
        <?=$form->field($model, 'process_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => EquipProductGroupApi::getProgressNameList()], 'options' => ['class' => 'form-control']])?>
        <div class="form-group form-inline">
            <div class="form-group form-inline"><label>设备类型</label></div>
            <div class="form-group form-inline" style="width: 180px;">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'equip_type_id',
                    'data' => Api::getEquipTypeList(),
                    'options' => [
                        'placeholder' => '设备类型',
                        // "multiple"  => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            ?>
            </div>
        </div>
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>



    <?php ActiveForm::end(); ?>

</div>
