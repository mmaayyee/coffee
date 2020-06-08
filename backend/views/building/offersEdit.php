<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = '修改点位优惠策略';
$this->params['breadcrumbs'][] = ['label' => '点位列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-update">

    <h1><?=Html::encode($this->title)?></h1>

<div class="building-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'first_free_strategy')->dropDownList($couponGroupList)?>

    <?=$form->field($model, 'strategy_change_date')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput();?>

    <?=$form->field($model, 'first_backup_strategy')->dropDownList($couponGroupList)?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

</div>
