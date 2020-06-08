<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use backend\models\EquipDelivery;
/* @var $this yii\web\View */
/* @var $model backend\models\AppVersionManagementSearch */
/* @var $form yii\widgets\ActiveForm */
$equipDeliveryModel = new EquipDelivery();
?>

<div class="app-version-management-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择设备类型</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
                'model' => $model,
                'attribute' => 'equip_type_id',
                'data' => $equipDeliveryModel->getEquipTypeModelArray(),
                'options' => ['multiple' => false, 'placeholder' => '请选择设备类型'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            </div>
        </div>
        <?= $form->field($model, 'big_screen_version') ?>

        <?= $form->field($model, 'small_screen_version') ?>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
