<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/5/12
 * Time: 下午12:01
 */
use yii\helpers\Url;
use common\models\EquipTask;
$this->title = '待分配任务';
?>
<style>
    .panel-default > .panel-heading {
        text-align: center;
        display: table;
        width:100%;
    }
    .panel-heading h3{
        width: 60%;
    }
    #search_r{
        display: table-cell;
        vertical-align: middle;
        font-size: 1.6rem;
        width: 8%;
    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title" style="text-align:center;margin: 0 auto;">
            待分配任务
            <span class="badge"><?php echo $equipTaskCount; ?></span>
        </h3>
        <div  id="search_r" >
            <a href="/distribution-task/wait-for-task?rand=<?php echo rand(); ?>">
                <span class="glyphicon glyphicon-repeat"></span>
            </a>
        </div>
    </div>
    <div class="panel-body" style="text-align: center">
        <?php if ($equipTaskArr) {foreach ($equipTaskArr as $taskKey => $taskValue) {?>
            <div class="row">
                <div class="col-xs-5"><p style="margin-top:15px;"><?php echo EquipTask::$task_type[$taskValue['task_type']];?></p></div>
                <div class="col-xs-7">
                    <div class="row">
                        <a href="<?php echo Url::to(['equip-task/assigned-personnel', 'id' => $taskValue['id']]) ?>">
                            <div class="col-xs-12">
                                <?php echo \common\models\Building::getBuildingDetail('name', ['id' => $taskValue['build_id']])['name'] ?>
                            </div>
                            <div class="col-xs-12">
                                <?php echo date("Y年m月d日 H时i分", $taskValue['create_time']) ?>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <hr/>
        <?php }} else {?>
            <div>暂无数据</div>
        <?php }?>
    </div>
</div>