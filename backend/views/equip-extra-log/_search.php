<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\EquipExtraLog;
use common\models\EquipExtra;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipExtraLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-extra-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-inline">

    <?= $form->field($model, 'equip_extra_id')->dropDownList(EquipExtra::getEquipExtraSelect()) ?>

    <?= $form->field($model, 'status')->dropDownList(['' => '请选择',EquipExtraLog::USING => '使用中', EquipExtraLog::REPLACED => '被回收']) ?>

    <?php echo $form->field($model, 'create_user') ?>

    <?=Html::hiddenInput('EquipExtraLogSearch[equip_id]', $model->equip_id);?>

    <?php // echo $form->field($model, 'create_time') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
