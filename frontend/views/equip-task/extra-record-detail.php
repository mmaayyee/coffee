<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/5/16
 * Time: 上午11:01
 */
$this->title = "设备附件详情";
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('@web/js/equiptask.js',['depends'=>['frontend\assets\AppAsset']]);
?>
<style>
    div .table1:nth-child(6){

    }
    .table1{
        margin: 0;
        padding: 0 3px;
        border-left:1px solid #ddd;
        border-top:1px solid #ddd;
        border-right:1px solid #ddd;
        vertical-align: middle;
    }
    .table1 dt{
        padding:5px 0;
    }
    .table1 dd{
        padding-top:5px;
        padding-left:3px;
        border-left: 1px solid #ddd;
    }
</style>
<div id="task_detail">
    <dl class="dl-horizontal">
        <dt>楼宇名称：</dt>
        <dd><?php echo $task_detail['build']['name'];?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>楼宇地址：</dt>
        <dd id="address"><?php echo $task_detail['build']['province'].$task_detail['build']['city'].$task_detail['build']['area'].$task_detail['build']['address']; ?></dd>
    </dl>
    <div id="allmap" style="width:100%;height:200px;"></div>
    <dl class="dl-horizontal">
        <dt>任务创建时间：</dt>
        <dd><?php echo $task_detail['create_time'] ? date('Y年m月d日 H点i分',$task_detail['create_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>设备附件：</dt>
        <dd><?php echo \common\models\EquipExtra::getExtraNameByID($task_detail['content']) ?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd><?php echo $task_detail['remark']; ?></dd>
    </dl>
    <div style="border-bottom:1px solid #ddd;">
        <dl class="dl-horizontal table1">
            <dt>开始配送时间：</dt>
            <dd><?php echo $task_detail['start_repair_time'] ? date('Y年m月d日 H点i分',$task_detail['start_repair_time']) : ''?></dd>
        </dl>
        <dl class="dl-horizontal table1">
            <dt>配送结束时间：</dt>
            <dd><?php echo $task_detail['end_repair_time'] ? date('Y年m月d日 H点i分',$task_detail['end_repair_time']) : ''?></dd>
        </dl>
        <dl class="dl-horizontal table1">
            <dt>处理结果：</dt>
            <dd><?php
                switch($task_detail['process_result']){
                    case 2:
                        echo '配送成功';
                        break;
                    case 4:
                        echo '回收成功';
                        break;
                    default:
                        echo '配送失败';
                        break;
                }
                ?>
            </dd>
        </dl>

    </div>
</div>
<style>
    .dl-horizontal dt{
        float: left;
        width:105px;
        overflow: hidden;
        clear: left;
        text-align: left;
    }
    .dl-horizontal dd{
        margin-left:105px;
    }
    .form-control {
        display: initial;
    }
    dl {
        margin-bottom: 10px;
    }
</style>
