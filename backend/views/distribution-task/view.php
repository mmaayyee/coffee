<?php

use backend\models\DistributionTask;
use common\models\Building;
use common\models\EquipTask;
use common\models\WxMember;
use frontend\models\DistributionTaskImgurl;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */
$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '运维任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('
	$(".img").on("mouseover",function(){
		var $img=$(this).attr("src");
		$(".large_img").attr("src",$img);
	}).on("mouseout",function(){
		$(".large_img").attr("src","");
	})
');
?>
<style>
	.task_box{
		position:relative;
	}
	.large_img{
		position:absolute;
		top:116%;
		left:29%;
		width:300px;
		height:300px;
	}
</style>
<div class="distribution-task-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'build_id',
            'value'     => !empty($model->build_id) ? Building::getBuildingDetail("name", ['id' => $model->build_id])['name'] : '',
        ],

        [
            'attribute' => '运维员',
            'value'     => !empty($model->assign_userid) ? WxMember::getMemberDetail("name", array('userid' => $model->assign_userid))['name'] : '',
        ],
        [
            'attribute' => 'task_type',
            'value'     => function ($model) {
                $taskTypeArr = explode(',', $model->task_type);
                $taskType    = '';
                foreach ($taskTypeArr as $type) {
                    $taskType .= DistributionTask::$taskType[$type] . ',';
                }
                $taskType = substr($taskType, 0, -1);
                return $taskType;
            },
        ],
        [
            'attribute' => "任务状态",
            'value'     => $model->is_sue == 2 ? "已完成" : "未完成",
        ],
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
        ],
        [
            'attribute' => 'recive_time',
            'value'     => !empty($model->recive_time) ? date('Y-m-d H:i:s', $model->recive_time) : '',
        ],
        [
            'attribute' => 'road_time',
            'value'     => function ($model) {
                $roadTime = '暂无';
                if ($model->start_delivery_time > 0) {
                    $second   = DistributionTask::getRoadTime($model);
                    $roadTime = DistributionTask::getDateHis($second);
                }
                return $roadTime;
            },
        ],
        [
            'attribute' => '打卡时间',
            'value'     => !empty($model->start_delivery_time) ? date('Y-m-d H:i:s', $model->start_delivery_time) : '暂无',
        ],
        [
            'attribute' => '完成时间',
            'value'     => !empty($model->end_delivery_date) ? $model->end_delivery_date : '暂无',
        ],
        [
            'attribute' => 'task_time',
            'value'     => function ($model) {
                $second = !empty($model->end_delivery_date) && $model->start_delivery_time > 0 ? strtotime($model->end_delivery_date) - $model->start_delivery_time : 0;
                return DistributionTask::getDateHis($second);
            },
        ],
        'reason',
        [
            'attribute' => '任务照片',
            'format'    => 'html',
            'value'     => function ($model) {
                //获取任务图片
                $imgurlList = DistributionTaskImgurl::getTaskImgUrlList($model->id);
                if (!empty($imgurlList)) {
                    $imgList = '<div class="task_box"><table><tr>';
                    $count   = count($imgurlList);
                    for ($i = 0; $i < $count; $i++) {
                        $src = Yii::$app->params['frontend'] . $imgurlList[$i];
                        $imgList .= '<td style="padding:2px;"><img width="100" height="100" class="img task_img" src="' . $src . '"></td>';
                    }
                    $imgList .= '</tr></table><img src="" class="large_img"></div>';
                    return $imgList;
                }
                return '暂无';
            },
        ],
        [
            'attribute' => 'equipment_status',
            'value'     => $model->equip->equipment_status == 1 ? '正常' : '故障',
        ],
        [
            'attribute' => 'latest_log',
            'value'     => $model->equip->last_log,
        ],
        [
            'attribute' => 'malfunction_task',
            'format'    => 'html',
            'value'     => function ($model) {
                return !empty($model->malfunction_task) ? EquipTask::getMalfunctionContent($model->malfunction_task, 1) : '暂无';
            },
        ],
        [
            'attribute' => 'result',
            'value'     => function ($model) {
                if ($model->is_sue == 2) {
                    $result = $model->result == 1 ? '已修复' : '未修复';
                } else {
                    $result = '暂无';
                }
                return $result;

            },
        ],
        [
            'attribute' => '蓝牙秤上传数据',
            'format'    => 'html',
            'value'     => $model->bluetoothUpload(),
        ],
    ],
])?>

</div>

