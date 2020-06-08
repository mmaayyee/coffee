<?php

use backend\models\Organization;
use common\models\Building;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->registerJs('
    $(".btn-primary").click(function(){
        var build_id    =   $("#distributiontask-build_id").val();
        var userid      =   $("#distributiontask-assign_userid").val();
        var time        =   $("#distributiontask-end_delivery_date").val();
        if(!time && !build_id && !userid){
            $("#error").show();
            return false;
        }
        $("#error").hide();
    });
');?>
<style type="text/css">
    #error{
        display: none;
    }
</style>

<div class="distribution-task-search">
    <?php $form = ActiveForm::begin([
    'action' => ['search'],
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
        <?=$managerOrgId == 1 ? $form->field($model, 'orgId')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1]))->label('请选择分公司') : ''?>
        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => Building::getPreDeliveryBuildList(1, $model->orgId),
    'options'       => ['placeholder' => '请选择楼宇'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <div class="form-group">
            <label>请选择配送员</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'assign_userid',
    'data'          => WxMember::getDistributionUserArr(3),
    'options'       => ['placeholder' => '请选择配送员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <?=$form->field($model, 'end_delivery_date')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput();?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
        <div class="form-group" id="error" style="color:red;">检索条件不可为空</div>
    </div>

    <?php ActiveForm::end();?>

</div>
