<?php

use backend\models\EquipSymptom;
use backend\models\EquipWarn;
use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/distributionTask.js?v=5.2', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<style>
    @media screen and (min-width: 992px){
		.fc {
			display: inline-block;
			width: 50%;
			margin-right:2% ;
		}
		.f2 {
			display: inline-block;
			width: 10%;
			margin-right:0.5% ;
		}
		.ys{
			display:inline-block;width:17%;text-align:right;margin-right:1% ;
		}
    }
    @media screen and (max-width: 991px){
    	.fc{
    		width:97%;
    		margin-bottom: 2%;
    	}
    	.check-info{
    		display: inline-block;
    		width:90%;
    		margin-right:1% ;
    	}
    }
    #allmap{
    	margin:1% 0;
    	width:300px;
    	height:200px;
    }
    #distributiontask-malfunction_task label{
        width:20%;
    }
	#tip{
		color:red;
		display: none;
	}
</style>
<div class="distribution-task-form">
    <?php $form = ActiveForm::begin();?>
	<div id='tip'>楼宇存在未完成任务,添加后将合并到已有任务中</div>
     <?=$form->field($model, 'build_id')->widget(Select2::className(), [
    'data'          => \common\models\Building::getOperationBuildList(),
    'options'       => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])?>
	<p>所选楼宇：<span class="build" id="build_name"></span></p>
	<p>设备编号：<span class="buildCode" id="equip_code"></span></p>
	<p>设备类型：<span class="equipCode" id="equip_model"></span></p>
	<div id="allmap"></div>
	<?=$form->field($model, 'malfunction_task')->checkBoxList(EquipSymptom::getSymptomIdNameArr())?>
    <div class="distribution_task_abnormal">
        <div class="delivery_anormal">
            <?php if ($model->abnormal) {
    ?>
            <label>异常报警：</label>
            <div class="create_distribution_task_annormal">
                    <?php $abnormal_id = Json::decode($model->abnormal);
    $abnormals                             = '';
    if (!empty($abnormal_id)) {
        foreach ($abnormal_id as $abnormal) {
            $abnormals .= EquipWarn::$warnContent[$abnormal] . "<br/>";
        }
    }
    echo $abnormals;
    ?>
            </div>
            <?php }?>
        </div>
    </div>
    <?=$form->field($model, 'assign_userid')->widget(Select2::classname(), [
    'data'          => ['' => '请选择'],
    'options'       => ['placeholder' => '请选择负责人', 'onchange' => 'return changUser()'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])?>
    <div class="distribution_task_surplus_material">
        <div class="surplus_material">
            <label>运维人员手中剩余物料：</label>
            <div id="user_surplus_material"></div>
        </div>
    </div>
    <div class="distribution_task_content">
        <div class="delivery_content">
            <label>选择配送内容：</label>
            <div class="create_distribution_task_content"></div>
        </div>
    </div>

	<?=$form->field($model, 'remark')->textarea(['maxlength' => 255, 'rows' => 6], ['format' => 'html'])?>

    <input type="hidden" class="hide_assign_userid" value="<?php echo $model->assign_userid ?>">
    <input type="hidden" class="hide_assign_userName" value="<?php echo isset($model->assignUser->name) ? $model->assignUser->name : '' ?>">
	<input type="hidden" id="hide_id" value="<?php echo $model->id ?>">
    <input type="hidden" class="address" value="">

    <div class="form-group">
        <?=Html::button($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>

</div>
<script type="text/javascript">
    function changUser(){
        var userId = $("#distributiontask-assign_userid  option:selected").val();
        $.ajax({
            url: 'get-user-surplus-material',
            type: 'get',
            data: {userId: userId},
            success: function(res){
                $('#user_surplus_material').html(res);
            }
        })
    }
</script>