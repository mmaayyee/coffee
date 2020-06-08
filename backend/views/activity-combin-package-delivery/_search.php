<?php

use yii\helpers\Html;
use common\models\WxMember;
use yii\widgets\ActiveForm;
use backend\models\ActivityCombinPackageDelivery;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivityCombinPackageDeliverySearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="activity-combin-package-delivery-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-inline form-group">
    <?= $form->field($model, 'distribution_type')->dropDownList(ActivityCombinPackageDelivery::getdistributioTypeList()) ?>
    
    <?php echo $form->field($model, 'is_delivery')->dropDownList([''=> '请选择', '0'=> '未发货', '1'=> '已发货']) ?>
    <?php echo $form->field($model, 'activity_id')->hiddenInput()->label('') ?>
    <?php echo $form->field($model, 'distribution_user_id')->dropDownList(WxMember::getDistributionIdToNameList(1)) ?>
    
    <?php echo $form->field($model, 'user_mobile') ?>
    <?php echo $form->field($model, 'receiver') ?>
        
    <?=$form->field($model, 'createFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm',
        ],
    ]); ?>
    <?=$form->field($model, 'createTo')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm',
            'hour'       => 23,
            'minute'     => 59,
        ],
    ]); ?>

    
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
