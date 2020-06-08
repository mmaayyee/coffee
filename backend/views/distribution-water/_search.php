<?php

use backend\models\DistributionWater;
use backend\models\Organization;
use backend\models\ScmSupplier;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-water-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
    <?=$model->managerOrgId == 1 ? $form->field($model, 'orgId')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1]))->label('请选择分公司') : ''?>

        <label>请选择楼宇名称</label>
        <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => DistributionWater::getDistributionWaterBuildList(1, $model->orgId),
    'options'       => ['placeholder' => '请选择楼宇名称', 'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
        </div>

        <?=$form->field($model, 'supplier_id')->dropDownList(ScmSupplier::getOrgWaterList())?>
        <?=$form->field($model, 'completion_status')->dropDownList(array_slice(DistributionWater::$completionStatus,0,3,true))?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
