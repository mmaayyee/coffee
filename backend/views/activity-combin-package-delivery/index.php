<?php

use backend\models\ActivityCombinPackageDelivery;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivityCombinPackageDeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '需邮寄用户列表';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$url = Url::to(["activity-combin-package-delivery/deliver-goods"]);
$this->registerJs('
    $(".delivery").click(function(){
        var deliver = $(this).parents("tr").children().eq(4).html();
        if (!deliver) {
            alert("当前用户未填写收货地址,无法进行发货!");
            return false;
        }
        $("#addMyModals").modal();
        var delivery_id = $(this).attr("delivery_id");
        var activity_id = $(this).attr("activity_id");
        $("#delivery_value_id").val(delivery_id);
        $("#activity_value_id").val(activity_id);
    })
    $("#cancel").validation();
    // 一开始隐藏快递单号
    $("#user_courier_number").hide();
    // 获取配送方式
    $("#distribution_type").change(function(){
        var distributionType = $("input[type=radio]:checked").val();
        $(".help-block").text("");
        if(distributionType == 1){ // 运维
            // 显示运维人员，隐藏快递
            $("#distributio_user").show();
            $("#user_courier_number").hide();
            $("#user_courier_number input").removeAttr("check-type");
            $("#distributio_user select").attr("check-type","required");
            $("#user_courier_number").removeClass("has-error");
        }
        if(distributionType == 2){ // 快递
            // 显示快递，隐藏运维人员
            $("#distributio_user").hide();
            $("#user_courier_number").show();
            $("#distributio_user select").removeAttr("check-type");
            $("#user_courier_number input").attr("check-type","required");
            $("#distributio_user").removeClass("has-error");
        }
    });
    $(".sure-submit").on("click",function(){
        if($("#cancel").valid() == false){
            return false
        }else{
            $("#cancel").submit();
        }
    })
    $(".btn-default").on("click",function(){
        $(".help-block").text("");
        $("#distributio_user").removeClass("has-error");
        $("#user_courier_number").removeClass("has-error");
    });
');

?>

<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="activity-combin-package-delivery-index">

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '购买时间',
            'value' => function ($model) {
                return $model->create_time ? date("Y-m-d H:i:s", $model->create_time) : '';
            },
        ],

        [
            'label' => '用户手机号',
            'value' => function ($model) {
                return $model->user_mobile;
            },
        ],
        [
            'label' => '商品数量',
            'value' => function ($model) {
                return $model->commodity_num;
            },
        ],
        [
            'label' => '收货地址',
            'value' => function ($model) {
                return empty($model->address) ? '' : $model->address;
            },
        ],
        [
            'label' => '配送方式',
            'value' => function ($model) {
                return $model->distribution_type ? ActivityCombinPackageDelivery::getdistributioTypeList()[$model->distribution_type] : '';
            },
        ],
        [
            'label' => '收货人',
            'value' => function ($model) {
                return empty($model->receiver) ? '' : $model->receiver;
            },
        ],
        [
            'label' => '配送方式',
            'value' => function ($model) {
                return $model->distribution_type_info;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{deliverGoods}',
            'buttons'  => [
                'deliverGoods' => function ($url, $model, $key) {
                    return \Yii::$app->user->can('自组合用户发货') ?
                        ($model->is_delivery == 0  ? Html::button('发货', ['class' => 'btn btn-success delivery',
                                                                        'delivery_id' => $model->delivery_id,
                                                                        'activity_id' => $model->activity_id])
                            : ($model->is_delivery == 1 ?  "已发货" : '已退款'
                        )) : '';
                },
            ],
        ],
    ],
]);?>
</div>


<!-- 发货 -->
<div class="modal fade" id="addMyModals"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?=Html::beginForm(['activity-combin-package-delivery/deliver-goods'], 'post', ['id' => 'cancel'])?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">发货</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="delivery_id" id="delivery_value_id" value="">
                    <input type="hidden" name="activity_id" id="activity_value_id" value="">
                    <div class="form-group" id="distribution_type">
                        <label for="">配送方式：</label>
                        <input type="radio" name="distribution_type" value="1" checked/>运维
                        <input type="radio" name="distribution_type" value="2" />快递
                    </div>

                    <div class="form-group" id="distributio_user">
                        <label for="">运维人员：</label>
                        <select name="distribution_user_id" id="distribution_user_id" check-type="required" mail-message="请选择内容不能为空">
                            <option value="">请选择</option>
                            <?php foreach (WxMember::getDistributionList() as $key => $value) {?>
                            <option value="<?php echo $value['userid'] ?>"><?php echo $value['name'] ?></option>
                            <?php }?>
                        </select>
                    </div>

                    <div class="form-inline" id="user_courier_number">
                        <label for="">快递单号：<span style="font-size: 10px;  color: red;">提示：最长不可超过30位</span></label>
                        <div class="form-group">
                            <input type="text" name="courier_number" value=""  maxlength="30">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <input type="button" class="btn btn-primary sure-submit" value="确定"></div>
        </div>
        <?=Html::endForm()?>
    </div>
</div>
