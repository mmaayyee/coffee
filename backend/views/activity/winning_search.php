<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Activity;
/* @var $this yii\web\View */
/* @var $model backend\models\LotteryWinningRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lottery-winning-record-search">
    
    <?php $form = ActiveForm::begin([
        'action' => ['view' ],
        'method' => 'get',

    ]); ?>
    <div class="form-group form-inline">
    
    <?= $form->field($model, 'prizes_name') ?>
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <?= $form->field($model, 'prizes_type')->dropDownList(Activity::prizesTypeList()) ?>
    
    <?php echo $form->field($model, 'user_id') ?>
    
    <?php echo $form->field($model, 'user_phone') ?>

    <?php  echo $form->field($model, 'is_ship')->dropDownList(Activity::shipList())  ?>
        
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
