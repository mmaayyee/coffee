<?php

use common\models\Building;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipRepairSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-repair-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-inline">


        <?=$form->field($model, 'build_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['build_status' => Building::SERVED])], 'options' => ['class' => 'form-control']])?>

        <?=$form->field($model, 'is_accept')->dropDownList(['' => '请选择', 1 => '是', 2 => '否']);?>

        <?=$form->field($model, 'author')->textInput()?>

        <?=$form->field($model, 'process_status')->dropDownList(['' => '请选择', 1 => '未处理', 2 => '处理中', 3 => '成功解决', 4 => '解决失败']);?>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
