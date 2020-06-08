<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Building;
use common\models\WxDepartment;
/* @var $this yii\web\View */
/* @var $model backend\models\WxDepartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wx-department-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <?= $form->field($model, 'name') ?>

        <div class="form-group form-inline">
            <div class="form-group">
            <label>部门标识</label>
            <div class="select2-search">
                <?php echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'headquarter',
                    'data' => WxDepartment::$headquarter,
                    'options' => ['placeholder' => '请选择部门标识'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
        </div>
        <?=$form->field($model, 'org_id')->dropDownList(\backend\models\Organization::getBranchArray())?>

        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
