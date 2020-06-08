<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\UserSelectionTask;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use backend\models\CouponSendTask;
/* @var $this yii\web\View */
/* @var $model backend\models\ActiveBuy */

$this->title = '发券任务详情';
$this->params['breadcrumbs'][] = ['label' => '发券任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="">
    <table class="table table-responsive">
        <tr>
            <td><label for="">任务名称</label></td>
            <td><?php echo $couponSendTaskInfo['task_name']; ?></td>
        </tr>
        <tr>
            <td><label for="">任务状态</label></td>
            <td>
                <?php echo CouponSendTask::getCheckStatus($couponSendTaskInfo['check_status']); ?>
            </td>
        </tr>
        <?php if(!empty($couponSendTaskInfo['coupon_group_name'])):?>
        <tr>
            <td><label for="">优惠券套餐名称</label></td>
            <td>
                <?php echo $couponSendTaskInfo['coupon_group_name']; ?>
            </td>
        </tr>
        <?php endif;?>
        <tr>
            <td><label for="">
            		<?php echo !empty($couponSendTaskInfo['coupon_group_name']) ? '套餐详情' : '优惠券列表';?>
            	</label>
            </td>
            <td>
                <table class="table table-striped">
                    <tr>
                        <td><label for="">优惠券名称</label></td>
                        <td><label for="">优惠券数量</label></td>
                    </tr>
                    <?php if(!empty($couponSendTaskInfo['coupon_group_detail'])):?>
                        <?php foreach ($couponSendTaskInfo['coupon_group_detail'] as $key => $couponGroupList):?>
                        <tr>
                            <td><?php echo $couponGroupList['name'] ?></td>
                            <td><?php echo $couponGroupList['number'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else:?>
                    <?php foreach ($couponSendTaskInfo['coupon_list'] as $key => $coupon):?>
                        <tr>
                            <td><?php echo $coupon['coupon_name'] ?></td>
                            <td><?php echo $coupon['number'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif;?>
                </table>
            </td>
        </tr>
        
        <tr>
            <td><label for="">已发送手机号数量</label></td>
            <td>
                <?php echo $couponSendTaskInfo['user_num'] ?>
            </td>
        </tr>
        <tr>
            <td><label for="">待发送手机号数量</label></td>
            <td><?php echo $couponSendTaskInfo['mobileNumber'], ' '.Html::a('下载', "/coupon-send-task/export?id=". $couponSendTaskInfo['id'], ['title' => 'excel导出']);?></td>
        </tr>
        <tr>
            <td><label for="">发布时间</label></td>
            <td>
                <?php echo $couponSendTaskInfo['send_time'] ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">用户筛选任务</label></td>
            <td>
                <?php echo $couponSendTaskInfo['selection_task_name'] ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">审核时间</label></td>
            <td>
                <?php echo $couponSendTaskInfo['examine_time'] ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">审核意见</label></td>
            <td>
                <?php echo $couponSendTaskInfo['examine_opinion'] ?>
            </td>
        </tr>
        <tr>
            <td><label for="">添加时间</label></td>
            <td>
                <?php echo $couponSendTaskInfo['create_time'] ?>
            </td>
        </tr>
    </table>
    
    <?php if(Yii::$app->user->can('审核发券任务') && $couponSendTaskInfo['check_status'] == 0 || $couponSendTaskInfo['check_status'] == 1){ ?>
        <!-- form表单     -->
        <?php $form = ActiveForm::begin(['action' => ['coupon-send-task/audit-coupon-send-task-error'], 'method'=>'post',]); ?>

        <?= $form->field($model, 'examine_opinion')->textarea(['rows' => 6, 'maxlength' => 100,'placeholder'=>'最多输入100字符!'])->label('审核意见') ?>

        <?= $form->field($model, 'id')->hiddenInput(['value'=>$couponSendTaskInfo['id']])->label(false) ?>
        <div id = "prompt" style="color:red;"></div>
        <div class="form-group">

        <?= Html::submitButton( '拒绝' , ['class' => 'btn btn-success btn-refuse-click' ]) ?>
        <?php ActiveForm::end(); ?>
        <?php if($couponSendTaskInfo['check_status'] == 0){ ?>
        <a href="<?php echo Url::to(['coupon-send-task/audit-coupon-send-task-success','id'=>$couponSendTaskInfo['id']],['class'=> 'color']); ?>">
            <button type="button" class="btn btn-success change-color" >
                通过
            </button>
        </a>
        <?php
        }
    } ?>

</div>

