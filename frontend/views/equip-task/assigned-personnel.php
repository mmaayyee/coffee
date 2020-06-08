<?php

use common\models\WxMember;
use frontend\models\FrontendDistributionTask;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = $title;
// $this->registerJsFile('@web/js/equipTaskAssigned.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style type="text/css">
	p{
		margin: 0;
	}
	.conten{
		border:1px solid #ccc;
		border-radius: 5px;
		padding:1% 2%;
		margin-bottom:2% ;
	}
</style>
<div class="equip-task-form">

    <?php $form = ActiveForm::begin();?>
        <p>
            <label for="">楼宇名称：</label>
            <span id="equiptask-build_id" value = '<?php echo $model->build_id ?>'>
                <?php echo $model->build->name; ?>
            </span>
        </p>
        <p><span id="equip_model"></span></p>
            <label for="">设备类型：</label>
            <span>
                <?php echo $equipType; ?>
            </span>
        </p>
       <?php if(!empty($model->content) && $model->content != '默认内容'):?>
        <p>
            <label for="">任务内容</label>
            <div class="conten"><?php echo $model::getMalfunctionContent($model->content, $model->task_type); ?></div>
        </p>
        <?php endif; ?>
        <?php if($model->remark):?>
            <p><label>备注：</label><span style="word-break:break-word;"><?php echo $model->remark;?></span></p>
        <?php endif;?>

        <?php if (!$model->assign_userid) {?>
            <p>
                <?=$form->field($model, 'assign_userid')->label('指定负责人')->dropDownList(FrontendDistributionTask::getEquipNameArr($model->build_id, 3))?>
            </p>
        <?php } else {?>
            <p>
                <label for="">指定负责人</label>
                <div><?php echo WxMember::getMemberDetail("*", ['userid' => $model->assign_userid])['name']; ?></div>
            </p>
        <?php }?>
    <?php if (!$model->assign_userid) {?>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php }?>
    <?php ActiveForm::end();?>

</div>
