<?php
use backend\models\DistributionTask;
$this->title = "任务列表";
?>
<!-- <script src="/js/rem.js"></script> -->
<script src="/js/vconsole.min.js"></script>
<script type="text/javascript">
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
</script>
<link rel="stylesheet" href="/css/common.css">
<link rel="stylesheet" href="/css/distribution-task.css">
<style>

</style>
<ul id="myTab" class="nav nav-tabs nav-justifie">
    <li class="active">
        <a href="#undone" data-toggle="tab">
            今日待办
            <span class="badge">
                <?php echo $taskToBeDoneCount; ?>
            </span>
        </a>
    </li>
    <li>
        <a href="#done" data-toggle="tab">
            历史记录
            <span class="badge">
            <?php echo $taskHistoricalCount; ?>
        </span>
        </a>
    </li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane active" id="undone">
        <div class="row head-line">
            <div class="col-xs-3">
                楼宇次序
                <a href="/distribution-task/index?rand=<?php echo rand(); ?>">
                    <span class="glyphicon glyphicon-repeat"></span>
                </a>
            </div>
            <div class="col-xs-3">任务类型</div>
            <div class="col-xs-2">状态</div>
            <div class="col-xs-4">下发时间</div>
        </div>
        <div class="panel-body">
            <?php
foreach ($taskToBeDone as $task) {
    $taskType = explode(',', $task['task_type']);
    //判断是否已经打卡,如果已经打卡直接跳转到任务完成页面
    if ($task['start_delivery_time'] > 0 && (!in_array(DistributionTask::URGENT, $taskType) || count($taskType) > 1)) {
        echo '<a href="/distribution-task/distribution-index?id=' . $task['id'] . '">';
    } elseif ($task['recive_time'] > 0 && (($task['start_delivery_time'] <= 0) || ($task['start_delivery_time'] > 0 && in_array(DistributionTask::URGENT, $taskType) && count($taskType) == 1))) {
        echo '<a href="/distribution-task/emergency-index?id=' . $task['id'] . '">';
    }
    ?>
            <div class="row" >
                <div class="col-xs-3">
                    <p>
                        <label>
                            <?php echo $buildName[$task['build_id']] ?>
                        </label>
                    </p>
                </div>
                <div class="col-xs-2">
                    <p>
                        <label>
                            <?php echo DistributionTask::getTaskType($task['task_type']) ?>
                        </label>
                    </p>
                </div>
                <div class="col-xs-3" style="padding-left: 36px;">
                    <p>
                        <label>
                            <?php
if ($task['start_delivery_time'] > 0) {
        echo '已打卡';
    } else {
        if ($task['recive_time'] > 0) {
            echo '已接收';
        } else {
            echo '未接收';
        }
    }
    ?>
                        </label>
                    </p>
                </div>
                <div class="col-xs-4">
                    <p>
                        <label>
                            <?php echo date('Y-m-d H:i:s', $task['create_time']) ?>
                        </label>
                    </p>
                </div>

            </div>
        <?php echo '</a>';} ?>

        </div>
        <?php if ($taskUnreceivedCount > 0) {?>
        <div class="inline-btn">
            <a href="/distribution-task/recive?userid=<?php echo $userid; ?>"><button type="button" class="btn btn-primary">接受任务</button></a>
        </div>
        <?php }?>
    </div>
    <div class="tab-pane" id="done">
         <div class="row head-line">
            <div class="col-xs-4">楼宇次序</div>
            <div class="col-xs-4">任务状态</div>
            <div class="col-xs-4">完成时间</div>
        </div>

        <div class="panel-body">
            <?php foreach ($taskHistorical as $task) {
    ?>
            <a href="/distribution-task/task-record-detail?id=<?php echo $task['id']; ?>">
            <div class="row" >
                <div class="col-xs-4">
                    <p>
                        <label>
                            <?php echo $buildName[$task['build_id']] ?>
                        </label>
                    </p>
                </div>
                 <div class="col-xs-4">
                     <p>
                         <label>
                             <?php
if ($task['is_sue'] == 2) {
        echo '已完成';
    } else {
        echo '已作废';
    }
    ?>
                         </label>
                     </p>
                 </div>
                 <div class="col-xs-4">
                     <p>
                         <label>
                             <?php echo $task['end_delivery_date'] ?>
                         </label>
                     </p>
                 </div>
            </div>
            </a>
            <?php }?>
        </div>

    </div>
</div>

<?php
$this->registerJs('
        //寻找 taskType 有紧急任务 其他不可点； 无紧急任务 其他可点
        taskType    =   $(".taskRed").val();
        if(taskType && taskType == "4"){
            $(".panel-body a").not(".4").attr("onclick", "return false");
            $(".panel-body a").not(".4").css("color","#999");
            $(".panel-body a").not(".4").parent().parent().css("color","#999");
        }else{
            $(".panel-body a").not(".4").removeAttr("onclick", "return false");
            $(".panel-body a").not(".4").css("color","#337ab7");
            $(".panel-body a").not(".4").parent().parent().css("color","#333");
        }
')

?>

<script src="/js/operations/layout.js"></script>
<script src="/js/zepto.min.js"></script>
<script>
    if ($(".panel-body .row").length > 0) {
        $(".inline-btn").show();
    } else {
        $(".inline-btn").hide();
    }
    $("#allCheck").on("click", function (argument) {
        var _this = this;
        $(".panel-body input[type='checkbox']").each(function(){
            $(this).prop("checked", $(_this).prop("checked"));
        });
    });
    $(".panel-body input[type='checkbox']").on("click", function(){
        if($(this).prop("checked") == false && $("#allCheck").prop("checked") == true){
            $("#allCheck").prop("checked", false);
        }
        if ($(".panel-body input[type='checkbox']").length > 0 && $(".panel-body input[type='checkbox']").length == $(".panel-body input[type='checkbox']:checked").length && $("#allCheck").prop("checked") == false){
            $("#allCheck").prop("checked", true);
        }
    });
</script>
