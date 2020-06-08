<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLaxinRewardRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-laxin-reward-record-search">

    <?php $form = ActiveForm::begin([
    'action' => ['share-reward'],
    'method' => 'get',
]);?>
    <div class="form-inline">
    <?=$form->field($model, 'share_userid')->label('分享者')?>

    <?=$form->field($model, 'laxin_userid')->label('绑定者')?>

    <?=$form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->label('开始日期')?>

    <?=$form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->label('结束日期')?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
</div>
    <?php ActiveForm::end();?>

</div>

