<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\WxMember;
use common\models\Building;
use backend\models\Manager;
use backend\models\Organization;

$organization = Organization::getBranchArray();
unset($organization[1]);
$org_id = Manager::getManagerBranchID();

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->registerJs('
    $(".btn-primary").click(function(){
        var start_time  =   $("#distributionfiller-start_time").val();
        var end_time    =   $("#distributionfiller-end_time").val(); 
        var build_id    =   $("#distributionfiller-build_id").val();
        console.log("build_id："+build_id);      
        console.log("start_time："+start_time);  
        console.log("end_time："+end_time);
        if(!start_time && !end_time && !build_id){
            $("#error").show();
            return false;
        }
        if(!build_id && !start_time){
            $("#time_error").show();
            return false;
        }
        $("#error").hide();
    });

'); ?>
<style type="text/css">
    #time_error,#error{
        display: none;
    }
</style>
<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['search'],
        'method' => 'get',
    ]); ?>
    
    <div class="form-inline form-group">
        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search">
            <?php 
                $buildList = Building::getPreDeliveryBuildList();
                unset($buildList['']);
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'build_id',
                    'data' => $buildList,
                    'options' => [
                        'placeholder' => '请选择楼宇',
                        "multiple"  => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); 
            ?>
            </div>
        </div>

        <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>

        <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
        <?php if($org_id == 1){ ?>
            <?= $form->field($model, 'orgId')->dropDownList($organization); ?>
        <?php } ?>
        
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="form-group" id="error" style="color:red;">检索楼宇或时间不可全部为空</div>
        <div class="form-group" id="time_error" style="color:red;">检索开始时间不可为空</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
