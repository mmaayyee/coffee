<?php

use backend\models\DistributionMaintenance;
use common\models\EquipTask;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('@web/js/tmpl.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs("
    $('.view').click(function(){
        $.get(
            '/distribution-task-record/detail',
            {id:$(this).attr('data-id')},
            function(data){
                if (data.error == 0 && data.res.length != 0){
                    $('#detail').html(tmpl('tmpl-table',data.res));
                } else {
                    $('#detail').html('');
                    alert(data.msg);
                }
            },
            'json'
        )
    })
");
?>
<style>
.table{
    text-align: center;
}
</style>
<div class="equip-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_repair_search', ['model' => $searchModel]); ?>
    <p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $_GET['DistributionTaskSearch']['equip_id'], ['class' => 'btn btn-success'])?>
    </p>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '任务创建时间',
            'value' => function ($model) {
                return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
        [
            'label' => '任务接收时间',
            'value' => function ($model) {
                return $model->recive_time ? date('Y-m-d H:i:s', $model->recive_time) : '';
            },
        ],
        [
            'label' => '开始维修时间',
            'value' => function ($model) {
                if ($model->task_type == 3) {
                    return isset($model->maintenance->start_repair_time) ? date('Y-m-d H:i:s', $model->maintenance->start_repair_time) : '';
                } else {
                    return $model->start_delivery_time ? date('Y-m-d H:i:s', $model->start_delivery_time) : '';
                }

            },
        ],
        [
            'label' => '结束维修时间',
            'value' => function ($model) {
                if ($model->task_type == 3) {
                    return isset($model->maintenance->end_repair_time) ? date('Y-m-d H:i:s', $model->maintenance->end_repair_time) : '';
                } else {
                    return $model->end_delivery_time ? date('Y-m-d H:i:s', $model->end_delivery_time) : '';
                }
            },
        ],
        [
            'label' => '本次维修时间',
            'value' => function ($model) {
                if ($model->task_type == 3) {
                    return isset($model->maintenance) ? \common\helpers\Tools::time2string($model->maintenance->end_repair_time - $model->maintenance->start_repair_time) : '';
                } else {
                    return \common\helpers\Tools::time2string($model->end_delivery_time - $model->start_delivery_time);
                }
            },
        ],
        [
            'label'  => '任务开始地址',
            'format' => 'html',
            'value'  => function ($model) {
                return $model->start_address ? "<a href='/equip-task/task-map?&lat=" . $model->start_latitude . "&lng=" . $model->start_longitude . "'>" . $model->start_address . "</a>" : '';
            },
        ],
        [
            'label'  => '任务结束地址',
            'format' => 'html',
            'value'  => function ($model) {
                return $model->end_address ? "<a href='/equip-task/task-map?lat=" . $model->end_latitude . "&lng=" . $model->end_longitude . "'>" . $model->end_address . "</a>" : '';
            },
        ],

        [
            'label'  => '维修内容',
            'format' => 'html',
            'value'  => function ($model) {
                return EquipTask::getMalfunctionContent($model->malfunction_task);
            },
        ],
        [
            'label' => '负责人',
            'value' => function ($model) {
                return isset($model->user->name) ? $model->user->name : '';
            },
        ],
        [
            'label' => '处理结果',
            'value' => function ($model) {
                return isset($model->maintenance->process_result) ? DistributionMaintenance::$distribution_maintenance_repair_result[$model->maintenance->process_result] : '';
            },
        ],
        [
            'label'  => '详情',
            'format' => 'raw',
            'value'  => function ($model) {
                return Html::button('查看', array('class' => 'view', "data-toggle" => "modal", "data-target" => "#myModal", 'data-id' => $model->id));
            },
        ],
    ],
]);?>

</div>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">配送维修记录</h4>
      </div>
      <div class="modal-body">
        <table id="detail" class="table table-bordered">
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script type="text/x-tmpl" id="tmpl-table">
<tbody>
<tr>
    <td>设备类型</td>
    <td>{%=o.equiptype%}</td>
    <td>设备编号</td>
    <td colspan="2">{%=o.equip_code%}</td>
</tr>
<tr>
    <td>开始维修时间</td>
    <td>{%=o.start_repair_time%}</td>
    <td>结束维修时间</td>
    <td colspan="2">{%=o.end_repair_time%}</td>
</tr>
<tr>
    <td>维修结果</td>
    <td>{%=o.process_result%}</td>
    <td>维修人员</td>
    <td colspan="2">{%=o.assign_userid%}</td>
</tr>
<tr>
    <td>故障描述</td>
    <td colspan="4">{%=o.malfunction_description%}</td>
</tr>
<tr>
    <td>故障原因</td>
    <td colspan="4">{%=o.malfunction_reason%}</td>
</tr>
<tr>
    <td>处理方法</td>
    <td colspan="4">{%=o.process_method%}</td>
</tr>

<tr>
    <th>备件名称（已换</th>
    <th>备件型号</th>
    <th>原厂编号</th>
    <th>数量</th>
    <th>备注</th>
</tr>
{% for (var i in o.equipTaskFitting) { %}
<tr>
    <td>{%=o.equipTaskFitting[i].fitting_name%}</td>
    <td>{%=o.equipTaskFitting[i].fitting_model%}</td>
    <td>{%=o.equipTaskFitting[i].factory_number%}</td>
    <td>{%=o.equipTaskFitting[i].num%}</td>
    <td>{%=o.equipTaskFitting[i].remark%}</td>
</tr>
{% } %}
</tbody>
</script>


