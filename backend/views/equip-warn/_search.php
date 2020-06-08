<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipWarnSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-warn-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择报警内容</label>
            <div class="select2-search">
            <?php echo \kartik\select2\Select2::widget([
                'model' => $model,
                'attribute' => 'warn_content',
                'data' => \backend\models\EquipWarn::$warnContent,
                'options' => ['placeholder' => '请选择报警内容'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
