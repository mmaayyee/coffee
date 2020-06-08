<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/18
 * Time: 下午8:05
 */
use backend\models\ShopOrder;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<div class="shop-order-search">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="#" id="view_order"><span class="glyphicon glyphicon-eye-open"></span> 查看</a></li>
                <?php if (Yii::$app->user->can('退款审核')) {?>
                <li><a href="#" id="order_refund"><span class="glyphicon glyphicon-usd"></span> 退款审核</a></li>
                <?php }?>
            </ul>
        </div>

        <div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="gridSystemModalLabel">提示</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">是否通过此订单申请?</div>
                            <div class="form-group">
                                <label for="refund-reason" class="control-label">拒绝原因(选填):</label>
                                <input type="text" class="form-control" id="refund-reason">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="refund">拒绝</button>
                        <button type="button" class="btn btn-primary" id="pass">通过</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog"  id="tsModal">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <title>结果:</title>
                        <p id="success"></p>
                        <p id="fail"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sure" data-dismiss="modal">确认</button>
                    </div>
                </div>
            </div>
        </div>


        <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
        <div class="form-inline container-fluid">
            <?=$form->field($model, 'order_id')->textInput();?>
            <?=$form->field($model, 'order_code')->textInput();?>
            <?=$form->field($model, 'express_code')->textInput();?>
            <?=$form->field($model, 'phone')->textInput();?>
            <?=$form->field($model, 'mobile')->textInput();?>
            <?=$form->field($model, 'order_status')->dropDownList(ShopOrder::getOrderStatus())?>
            <?=$form->field($model, 'begin_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('下单开始时间')?>

            <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('下单结束时间')?>
            <div class="form-group">
                <?=Html::hiddenInput('export', '0', ['id' => 'export'])?>
                <?=Html::button('搜索', ['class' => 'btn btn-primary', 'id' => 'searchBut'])?>
                <?=Html::button('导出', ['class' => 'btn btn-primary', 'id' => 'exportBut'])?>
            </div>
        </div>
        <?php ActiveForm::end();?>
    </nav>
</div>
<?php
$this->registerJS('
    $("#exportBut").click(function(){
        $("#export").val(1);
        $("#w0").submit();
    })
    $("#searchBut").click(function(){
        $("#export").val(0);
        $("#w0").submit();
    })
$("#view_order").on("click",function(){
        var checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        window.location.replace("' . Url::toRoute('shop-order/view') . '" + "?id=" + checkList.attr("value"));
});

$("#order_refund").on("click",function(){
        var checkList = $("input[name=\'selection[]\']:checked");
        if(checkList.length < 1){
            alert("请至少选中一项进行操作");
            return false;
        }
         checkList.each(function(i,obj){
            var statusText=$(obj).parent().siblings().find(".status").text();
            if(statusText!="待退款"){
                alert("选择的商品不满足条件");
                isTrue=false;
                return false;
            }else{
                isTrue=true;
            }
        })
        if(isTrue){
           $("#myModal").modal();
        }

});
$("#pass").on("click",function(){
        $("#myModal").modal("hide");
        editOrder(1);
});
$("#refund").on("click",function(){
        $("#myModal").modal("hide");
        editOrder(2);
});

function editOrder(actionVal){
        var checkId = [];
      $("input[name=\'selection[]\']:checked").each(function(){
          checkId.push(this.value);
        });
        var refundReason = $("#refund-reason").val();
        $.ajax({
           "data":{"orderList":JSON.stringify(checkId),"refundReason":refundReason,"actionVal":actionVal,"userId":"' . Yii::$app->user->id . '"},
           "method":"post",
           "postType":"json",
           "url":"' . Url::toRoute('shop-order/order-refund') . '",
           "success":function(data){
             var data = $.parseJSON(data);
             if(data.length == 0){
               $("#tsModal #fail").text("操作失败");
               $("#tsModal").modal();
               return false;
             }
             if(data.failOrder.length > 0){
               $("#tsModal #success").text("操作失败");
             }
              if(data.successOrder.length > 0){
                $("#tsModal #fail").text("操作成功");
              }
              $("#tsModal").modal();
           }
        })
        $("#tsModal").on("hidden.bs.modal", function (e) {
            window.location.reload();
        })
        }

         $("#refund-reason").on("keypress",function (event) {
        if (event.keyCode == "13"){
            return false;
        }
    });
');?>