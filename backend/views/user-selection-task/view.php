<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\UserSelectionTask;
/* @var $this yii\web\View */
/* @var $model backend\models\ActiveBuy */

$this->title = '用户筛选任务详情';
$this->params['breadcrumbs'][] = ['label' => '用户筛选任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="">
    <table class="table table-responsive">
        <tr>
            <td><label for="">任务名称</label></td>
            <td><?php echo $taskInfo['selection_task_name']; ?></td>
        </tr>
        <tr>
            <td><label for="">参考任务</label></td>
            <td>
                <?php echo $taskInfo['reference_task_name']; ?>
            </td>
        </tr>
        <tr>
            <td><label for="">验证手机号</label></td>
            <td>
                <?php echo $taskInfo['validate_mobile']; ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">号码总数量</label></td>
            <td><?php echo $taskInfo['mobile_num'] ?></td>
        </tr>
        
        <tr>
            <td><label for="">任务状态</label></td>
            <td>
                <?php echo UserSelectionTask::getTaskStatusList()[$taskInfo['selection_task_status']]; ?>
            </td>
        </tr>
            
        <tr>
            <td><label for="">原因</label></td>
            <td>
                <?php echo $taskInfo['failure_reason']; ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">筛选结果</label></td>
            <td>
                <?php echo $taskInfo['selection_task_result'] ? UserSelectionTask::getTaskResultList()[$taskInfo['selection_task_result']] : 0  ?>
            </td>
        </tr>
        
        <tr>
            <td><label for="">创建时间</label></td>
            <td>
                <?php echo $taskInfo['create_time'] ?>
            </td>
        </tr>
        
    </table>
</div>
