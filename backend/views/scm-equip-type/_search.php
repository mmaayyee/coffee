<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\ScmEquipType;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-equip-type-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择供应商</label>
            <div class="select2-search">
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'supplier_id',
                'data' => $model->getSupplierArray(),
                'options' => ['multiple' => false, 'placeholder' => '请选择供应商'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            </div>
        </div>
        <?= $form->field($model, 'model')->widget('yii\jui\AutoComplete',[
            'options'=>['class'=>'form-control','placeholder'=>'请输入设备类型名称'],
            'clientOptions'=>[
                 'source'=> ScmEquipType::getEquipTypeNameArr()
            ]
        ])->label('设备类型名称') ?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
