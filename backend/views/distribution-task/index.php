<?php

use backend\models\DistributionTask;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '运维任务管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Yii::$app->user->can('添加运维任务') ? Html::a('添加紧急任务', ['create'], ['class' => 'btn btn-success']) : ''?>
        <?=Yii::$app->user->can('添加运维任务') ? Html::a('添加临时任务', ['distribution-temporary-task/create'], ['class' => 'btn btn-success']) : ''?>
        <?=Yii::$app->user->can('日常任务管理') ? Html::a('日常任务管理', ['distribution-daily-task/index'], ['class' => 'btn btn-success']) : ''?>
        <?=Yii::$app->user->can('设备故障任务管理') ? Html::a('设备故障任务管理', ['equip-abnormal-task/index'], ['class' => 'btn btn-success']) : ''?>
        <?=!Yii::$app->user->can('设置备用料包') ? '' : Html::button('设置备用料包', ['class' => 'btn btn-primary btn-blue', 'data-toggle' => 'modal', 'data-target' => '#spare-packets'])?>
        <?=$this->render('/scm-warehouse-out/spare_packets', [])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'showFooter'   => true, //设置显示最下面的footer
    'id'           => 'grid',
    'columns'      => [
        [
            'class'         => \yii\grid\CheckboxColumn::className(),
            'name'          => 'id', //设置每行数据的复选框属性
            'headerOptions' => ['width' => '30'],
            'footer'        => '<button onclick="changeUser()" href="#" class="btn btn-success" url="' . Url::toRoute('#') . '">批量更换运维人员</button>',
            'footerOptions' => ['colspan' => 8], //设置删除按钮垮列显示；
        ],
        ['class' => 'yii\grid\SerialColumn', 'footerOptions' => ['class' => 'hide']],
        [
            'attribute'     => 'task_type',
            'value'         => function ($model) {
                $taskTypeArr = explode(',', $model->task_type);
                $taskType    = '';
                foreach ($taskTypeArr as $type) {
                    $taskType .= DistributionTask::$taskType[$type] . ',';
                }
                $taskType = substr($taskType, 0, -1);
                return $taskType;
            },
            'footerOptions' => ['class' => 'hide'],
        ],
        [
            'attribute'     => 'build_name',
            'format'        => 'text',
            'value'         => function ($model) use ($buildName) {
                return $buildName[$model->build_id] ?? '';
            },
            'footerOptions' => ['class' => 'hide'],
        ],
        [
            'attribute'     => 'assign_userid',
            'value'         => function ($model) {
                return !empty($model->assign_userid) ? WxMember::getMemberDetail("name", array('userid' => $model->assign_userid))['name'] : '';
            },
            'footerOptions' => ['class' => 'hide'],
        ],

        [
            'attribute'     => 'recive_time',
            'label'         => '任务接收状态',
            'value'         => function ($model) {
                if ($model->recive_time > 0 && $model->start_delivery_time == 0 && $model->is_sue == 1) {
                    return '已接收';
                } elseif ($model->recive_time > 0 && $model->start_delivery_time > 0 && $model->is_sue == 1) {
                    return '已打卡';
                } elseif ($model->is_sue == 2) {
                    return '已完成';
                } elseif ($model->is_sue == 3) {
                    return '已作废';
                } else {
                    return '未接收';
                }
            },
            'footerOptions' => ['class' => 'hide'],
        ],

        [
            'attribute'     => 'create_time',
            'format'        => 'text',
            'value'         => function ($model) {
                if (empty($model->create_time)) {
                    return '暂无';
                } else {
                    return date("Y-m-d H:i:s", $model->create_time);
                }
            },
            'footerOptions' => ['class' => 'hide'],
        ],

        [
            'class'         => 'yii\grid\ActionColumn',
            'header'        => '操作',
            'template'      => '{view} {update} {delete} {abolish}',
            'buttons'       => [
                //已发布的可暂停
                'update'  => function ($url, $model, $key) {
                    if (!$model->start_delivery_time && \Yii::$app->user->can('编辑运维任务')) {
                        if (in_array(DistributionTask::URGENT, explode(',', $model->task_type))) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['distribution-task/update', 'id' => $model->id]));
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['distribution-temporary-task/update', 'id' => $model->id]));
                        }
                    }
                },
                'delete'  => function ($url, $model) {
                    return (!$model->start_delivery_time && Yii::$app->user->can('删除运维任务')) ? Html::a('', $url, ['onclick' => 'return confirm("确定删除吗？");', 'class' => 'glyphicon glyphicon-trash', 'title' => '删除']) : '';
                },
                'abolish' => function ($url, $model) {
                    return Yii::$app->user->can('作废运维任务') ? Html::a('', 'javascript:void(0);', ['class' => 'glyphicon glyphicon-remove', 'title' => '作废', 'onClick' => 'return checkTip(' . $model['id'] . ')']) : '';
                },

            ],
            'footerOptions' => ['class' => 'hide'],
        ],

    ],
]);?>

</div>
<div class="dialog">
    <h3>运维任务作废</h3>
    <textarea  cols="20" style="width:306px; height:70px; font-size:12px; overflow: hidden; resize:none;" line-height="20px" class="reason">作废理由</textarea>
    <p>
        <a  href="javascript:void(0)" type="button" class="btn btn-primary" onclick="cancelBtn(1)">任务作废</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a  type="button" class="btn btn-primary" href="javascript:void(0)"  onclick="cancelBtn(0)">取消作废</a>
    </p>
    <img src="/images/flag_close.png" alt="关闭" class="close-btn">
</div>
<!-- 开头 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">更换运维人员</h4>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin();?>
        <?=$form->field($searchModel, 'assign_userid')->dropDownList(\common\models\WxMember::getDistributionUserArr(3), ['id' => 'sel'])?>
        <?php $form = ActiveForm::end();?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary rightClick" onclick="getUserId()">确定</button>
      </div>
    </div>
  </div>
</div>
<!-- 结尾 -->
<script>
    var taskId="";
    function checkTip(taskid){
        taskId=taskid;
        $(".dialog").show(200);
        $(".close-btn").on("click",function(){
            $(".dialog").hide(200);
        })
        return false;
    }
    function cancelBtn(index){
        var reason=$(".reason").val();
        window.location.href="/distribution-task/abolish?result="+index+"&id="+taskId+"&reason="+reason;
    }

    function changeUser(){
        $('#myModal').modal();

    }
    function getUserId(){
        var ids = $("#grid").yiiGridView("getSelectedRows");
        var selectedVal = $("#sel option:selected").val();
        $('#myModal').modal('hide');
        $.ajax({
            type : 'GET',
            url : 'change-user',
            data: {taskId:ids,userId:selectedVal},
            error : function() {
                alert('修改运维人员失败');
            },
            success : function(data) {
                if(data){
                    alert('修改运维人员成功');

                }else{
                    alert('修改运维人员失败');
                }
            }
        });
    }
</script>

<style>
    .dialog{
        position:relative;
        width: 500px;
        height: 200px;
        border: solid 1px #b5b0b0;
        border-radius: 5px;
        position: absolute;
        left: 30%;
        bottom: 30%;
        background: #eee;
        line-height: 40px;
        text-align: center;
        display: none;
        font-size:16px;
    }
    .btn-blue{
        position: relative;
        top:-5px;
    }
    .close-btn{
        position:absolute;
        right:0;
        top:0;
        width:40px;
        height:40px;
        cursor:pointer;
    }
</style>


