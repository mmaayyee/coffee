<?php

use backend\models\Organization;
use common\models\WxDepartment;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\WxMemberSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-member-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group form-inline">
        <?=$form->field($model, 'userid')?>

        <?=$form->field($model, 'name')?>

        <?=$form->field($model, 'mobile')?>

        <div class="form-group">
            <label>部门</label>
            <div class="select2-search">
                <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'department_id',
    'data'          => WxDepartment::getDepartArray(),
    'options'       => ['placeholder' => '请选择部门'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'gender')->dropDownList(array('' => '请选择', 1 => '男', 2 => '女'))?>

        <?=$form->field($model, 'position')->dropDownList(WxMember::$position)?>

        <?=$form->field($model, 'org_id')->dropDownList(Organization::getBranchArray())?>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
