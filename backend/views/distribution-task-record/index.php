<?php

use backend\models\DistributionTask;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '配送任务记录管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/tmpl.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs("
    $('.view').click(function(){
        $.get(
            '/distribution-task-record/filler',
            {id:$(this).attr('data-id')},
            function(data){
                // console.log(data);
                if (data.error == 0){
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
<div class="distribution-task-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'equip_id' => $equip_id]); ?>
	<p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $equip_id, ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'task_type',
            'value'     => function ($model) {
                return DistributionTask::getTaskType($model->task_type);
            },
        ],
        [
            'attribute' => 'build_id',
            'format'    => 'text',
            'value'     => function ($model) {
                if ($model->buildName) {
                    return $model->buildName->name;
                }
            },
        ],
        [
            'attribute' => 'content',
            'format'    => 'html',
            'value'     => function ($model) {
                if ($model->task_type == DistributionTask::URGENT || $model->task_type == DistributionTask::CLEAN) {
                    //紧急
                    return $model->content;
                } else {
                    //配送
                    return DistributionTask::getDistributionData($model->id);
                }
            },
        ],
        [
            'attribute' => 'assign_userid',
            'value'     => function ($model) {
                return !empty($model->assign_userid) ? WxMember::getMemberDetail("name", array('userid' => $model->assign_userid))['name'] : '';
            },
        ],

        [
            'attribute' => 'start_delivery_time',
            'value'     => function ($model) {
                return $model->create_time ? date("Y-m-d H:i:s", $model->start_delivery_time) : '';
            },
        ],
        [
            'attribute' => 'end_delivery_time',
            'value'     => function ($model) {
                return $model->create_time ? date("Y-m-d H:i:s", $model->end_delivery_time) : '';
            },
        ],
        [
            'attribute' => 'start_address',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->start_address ? "<a href='/equip-task/task-map?&lat=" . $model->start_latitude . "&lng=" . $model->start_longitude . "'>" . $model->start_address . "</a>" : '';
            },
        ],
        [
            'attribute' => 'end_address',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->end_address ? "<a href='/equip-task/task-map?lat=" . $model->end_latitude . "&lng=" . $model->end_longitude . "'>" . $model->end_address . "</a>" : '';
            },
        ],
        'meter_read',
        [
            'format' => 'raw',
            'value'  => function ($model) {
                if ($model->task_type == DistributionTask::SERVICE || $model->task_type == DistributionTask::DELIVERY || $model->task_type == DistributionTask::REFUEL) {
                    //维修 、 维修配送
                    return Html::button('查看', array('class' => 'view', "data-toggle" => "modal", "data-target" => "#myModal", 'data-id' => $model->id));
                }

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
        <h4 class="modal-title" id="myModalLabel">配送任务记录</h4>
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
    <th>物料分类</th>
    <th>物料名称</th>
    <th>物料规格</th>
    <th>料仓</th>
    <th>数量</th>
    <th>添加时间</th>
    <th>添加人</th>
</tr>
{% for (var i in o.distributionFiller) { %}
<tr>
    <td>{%=o.distributionFiller[i].material_type%}</td>
    <td>{%=o.distributionFiller[i].material_id%}</td>
    <td>{%=o.distributionFiller[i].weight%}</td>
    <td>{%=o.distributionFiller[i].stock_id%}</td>
    <td>{%=o.distributionFiller[i].number%}</td>
    <td>{%=o.distributionFiller[i].create_date%}</td>
    <td>{%=o.distributionFiller[i].add_material_author%}</td>
</tr>
{% } %}
</tbody>
</script>
