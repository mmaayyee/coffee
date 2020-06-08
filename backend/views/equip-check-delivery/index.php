<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipDeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '投放记录管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
	.right-img,.error-img{
		display: inline-block;
	}
</style>
<div class="equip-delivery-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <a href="/equipments/view?id=<?php echo Yii::$app->request->get('equip_id') ?>" class="btn btn-success">返回上一页</a>
    </p>
    <!-- <input type="text" class="delivery_id" name="deliveryId" value="" /> -->
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '楼宇名称',
            'format' => 'text',
            'value'  => function ($model) {
                return \common\models\Building::getBuildingDetail('name', ['id' => $model->build_id])['name'];
            },
        ],
        [
            'label' => '接收任务时间',
            'value' => function ($model) {
                return !empty($model->recive_time) ? date('Y-m-d', $model->recive_time) : '';
            },
        ],
        [
            'label' => '开始验收时间',
            'value' => function ($model) {
                return !empty($model->start_repair_time) ? date('Y-m-d H:i:s', $model->start_repair_time) : '';
            },
        ],
        [
            'label' => '结束验收时间',
            'value' => function ($model) {
                return !empty($model->end_repair_time) ? date('Y-m-d H:i:s', $model->end_repair_time) : '';
            },
        ],
        [
            'label'  => '开始验收位置',
            'format' => 'html',
            'value'  => function ($model) {
                return !empty($model->start_longitude) ? "<a href='/equip-task/task-map?lat=" . $model->start_latitude . "&lng=" . $model->start_longitude . "'>" . $model->start_address . "</a>" : '';
            },
        ],
        [
            'label'  => '结束验收位置',
            'format' => 'html',
            'value'  => function ($model) {
                return !empty($model->end_latitude) ? "<a href='/equip-task/task-map?lat=" . $model->end_latitude . "&lng=" . $model->end_longitude . "'>" . $model->end_address . "</a>" : '';
            },
        ],

        [
            'label' => '验收结果',
            'value' => function ($model) {
                $res = $model->acceptance->accept_result ?? '';
                if ($res == 1) {
                    return '设备通过，灯箱未通过';
                } else if ($res == 2) {
                    return '设备未通过，灯箱通过';
                } else if ($res == 3) {
                    return '全部通过';
                } else {
                    return '全部未通过';
                }
            },
        ],

        [
            'label'  => '详情',
            'format' => 'raw',
            'value'  => function ($model) {
                return Html::button('查看', array('class' => 'view', "data-toggle" => "modal", "data-target" => "#myModal", 'data-id' => $model->acceptance->Id ?? '', 'data-result' => $model->acceptance->accept_result ?? '', 'delivery_id' => $model->relevant_id, 'is_lightbox' => $model->delivery->is_lightbox ?? ''));
            },
        ],
    ],
]);?>
</div>
<script type="text/javascript" src="/js/third-party/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/js/tmpl.min.js"></script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active">
                    <a href="#home" data-toggle="tab" class="equip_detail">设备验收记录</a>
                </li>
                <li id="lightBoxDetail">
                    <a href="#ios" data-toggle="tab" class="lightbox_detail">灯箱验收记录</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="home">
                    <p>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">设备验收记录</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered equip_debug">
                                <thead>
                                    <tr>
                                        <td>序号</td>
                                        <td>调试项目</td>
                                        <td>结果</td>
                                    </tr>
                                </thead>
                                <tbody id="debug_tbody"></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </p>
                </div>
                <div class="tab-pane fade" id="ios">
                    <p>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">灯箱验收记录</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered equip_lightbox">
                                <thead>
                                    <tr>
                                        <td>序号</td>
                                        <td>调试项目</td>
                                        <td>结果</td>
                                    </tr>
                                </thead>
                                <tbody id="lightbox_tbody"></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </p>

                </div>
            </div>
        <script>
            $(function(){
                $('#ios').hide();
                $('.view').click(function(event) {
                    $('#ios').hide();
                    var deliveryId  =   $(this).attr("delivery_id");
                    var is_lightbox =   $(this).attr("is_lightbox");
                    debugResult("equip_detail", deliveryId);
                    lightboxResult("lightbox_detail",deliveryId);
                    if(is_lightbox <= 0){
                        $("#lightBoxDetail").hide();
                        $(".equip_detail").trigger('click');
                        $('#ios').hide();
                        $('#home').show();
                    }else{
                        $("#lightBoxDetail").show();
                        if ($("#lightBoxDetail").hasClass('active')) {
                            $('#ios').show();
                        }
                    }
                });
            })

            $('.lightbox_detail').click(function() {
              $('#ios').show();
              $('#home').hide();
            });
            $('.equip_detail').click(function() {
              $('#ios').hide();
              $('#home').show();
            });

            //设备验收函数
            function debugResult(ident, deliveryId){
                $.get("<?php echo Url::to(['equip-check-delivery/ajax-equip-acceptance']) ?>" , {'delivery_id': deliveryId,'detail': ident},function(data){
                    if(data){
                        $('#debug_tbody').empty();
                        var j = 1;
                         for (var i in data) {
                            if(data[i].ret_result == 'true'){
                               var tr  = "<tr><td>"+j+"</td><td style='width:80%'>"+data[i].debug_item+"</td><td style='color:green'><div class='right-img'></div></td></tr>";
                            }else{
                               var tr  = "<tr><td>"+j+"</td><td style='width:80%'>"+data[i].debug_item+"</td><td style='color:red'><div class='error-img'></div></td></tr>";
                            }
                            j++;
                            $("#debug_tbody").append(tr);
                         }
                    }

                },
                'json'
                 );
            }

            // 灯箱验收函数
            function lightboxResult(ident, deliveryId){
                $.get("<?php echo Url::to(['equip-check-delivery/ajax-equip-acceptance']) ?>" , {'delivery_id': deliveryId,'detail': ident},function(data){
                    if(data){
                        $('#lightbox_tbody').empty();
                        var j = 1;
                         for (var i in data) {
                            if(data[i].ret_result == 'true'){
                               var tr  = "<tr><td>"+j+"</td><td style='width:80%'>"+data[i].debug_item+"</td><td><div class='right-img'></div></td></tr>";
                            }else{
                               var tr  = "<tr><td>"+j+"</td><td style='width:80%'>"+data[i].debug_item+"</td><td><div class='error-img'></div></td></tr>";
                            }
                            j++;
                            $("#lightbox_tbody").append(tr);
                         }
                    }

                },
                'json'
                 );
            }
        </script>
        </div>
    </div>
</div>