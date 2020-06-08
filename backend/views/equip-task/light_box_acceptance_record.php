<?php

use common\models\EquipTask;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile('@web/js/tmpl.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
     $(".view").click(function(){
            $.get(
                "/equip-task/acceptance-record",
                {
                    id:$(this).attr("data-id"),
                    process_result:$(this).attr("data-result"),
                    equipId:$(this).attr("data-equipId")
                },
                function(data){
                    var html = "";
                    for (var i in data.res) {
                        var sort = parseInt(i)+1;
                        html+= "<tr><td>"+sort+"</td>";
                        html += tmpl("tmpl",data.res[i]);
                        html+= "</tr>";
                    }
                    $("#detail").html(html);
                },
                "json"
            )
        })
');
?>
<div class="equip-task-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=Html::a('返回上一页', '/equipments/view?id=' . $_GET['EquipTaskSearch']['equip_id'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'     => '楼宇名称',
            'attribute' => 'build_id',
            'value'     => function ($model) {
                return $model->build->name;
            },
        ],

        [
            'label'     => '任务创建时间',
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
        [
            'label'     => '任务接收时间',
            'attribute' => 'recive_time',
            'value'     => function ($model) {
                return $model->recive_time ? date('Y-m-d H:i:s', $model->recive_time) : '';
            },
        ],
        [
            'label'     => '开始验收时间',
            'attribute' => 'start_repair_time',
            'value'     => function ($model) {
                return $model->start_repair_time ? date('Y-m-d H:i:s', $model->start_repair_time) : '';
            },
        ],
        [
            'label'     => '开始验收位置',
            'attribute' => 'start_address',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->start_address ? "<a href='/equip-task/task-map?&lat=" . $model->start_latitude . "&lng=" . $model->start_longitude . "'>" . $model->start_address . "</a>" : '';
            },
        ],
        [
            'label'     => '结束验收时间',
            'attribute' => 'end_repair_time',
            'value'     => function ($model) {
                return $model->end_repair_time ? date('Y-m-d H:i:s', $model->end_repair_time) : '';
            },
        ],
        [
            'label'     => '结束验收位置',
            'attribute' => 'end_address',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->end_address ? "<a href='/equip-task/task-map?lat=" . $model->end_latitude . "&lng=" . $model->end_longitude . "'>" . $model->end_address . "</a>" : '';
            },
        ],
        [
            'label' => '验收结果',
            'value' => function ($model) {
                return EquipTask::$acceptance_result[$model->process_result];
            },
        ],
        [
            'label'     => '验收负责人',
            'attribute' => 'assign_userid',
            'value'     => function ($model) {
                return $model->assign_userid ? WxMember::getMemberDetail("*", ['userid' => $model->assign_userid])['name'] : '';
            },
        ],
        [
            'label'     => '漏电断路器型号',
            'attribute' => '',
            'value'     => function ($model) {
                return isset($model->acceptanceResult) ? $model->acceptanceResult->breaker_type : '';
            },
        ],
        [
            'label'     => '电表型号',
            'attribute' => '',
            'value'     => function ($model) {
                return isset($model->acceptanceResult) ? $model->acceptanceResult->ammeter_type : '';
            },
        ],
        [
            'label'     => '电表读数',
            'attribute' => '',
            'value'     => function ($model) {
                return isset($model->acceptanceResult) ? $model->acceptanceResult->ammeter_number : '';
            },
        ],
        [
            'label'  => '详情',
            'format' => 'raw',
            'value'  => function ($model) {
                return $model->process_result > 1 ? Html::button('查看', array('class' => 'view', "data-toggle" => "modal", "data-target" => "#myModal", 'data-id' => $model->id, 'data-result' => $model->process_result, 'data-equipId' => $model->equip_id)) : '';
            },
        ],
    ],
]);?>

</div>
<style>
.table{
    text-align: center;
}
.right-img,.error-img{
	display: inline-block;
}
</style>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">灯箱验收记录</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
        <thead>
            <tr>
                <td>序号</td>
                <td>调试项目</td>
                <td>结果</td>
            </tr>
        </thead>
        <tbody id="detail">

        </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script type="text/x-tmpl" id="tmpl">
    <td>{%=o.debug_item%}</td>
    {% if (o.result == 1) { %}
    <td><div class="right-img"></div></td>
    {% } else { %}
    <td><div class="error-img"></div></td>
    {% } %}
</script>

