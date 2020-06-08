<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/14
 * Time: 下午1:53
 */
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\ActiveForm;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/regular_verification.js", ["depends" => [JqueryAsset::className()]]);
?>
<style type="text/css">
.form-group.express-conditions.has-error #valierr{
    position: absolute;
    top: 34px;
    left: 38px;
}
</style>
<div class="shop-goods-search">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <ul class="nav navbar-nav navbar-left">
                <?php if (Yii::$app->user->can('添加商品')): ?>
                <li><a href="<?=Url::to(['shop-goods/create']);?>"><span class="glyphicon glyphicon-plus"></span> 添加商品</a></li>
                <?php endif?>
                <?php if (Yii::$app->user->can('编辑商品')): ?>
                <li><a href="javascript:void(0);" id="update_goods" onClick="updateGoods();"><span class="glyphicon glyphicon-pencil"></span> 修改</a></li>
                <?php endif?>
                <?php if (Yii::$app->user->can('查看商品')): ?>
                <li><a href="javascript:void(0);" id="view_goods" onClick="viewGoods();"><span class="glyphicon glyphicon-eye-open"></span> 查看</a></li>
                <?php endif?>
                <?php if (Yii::$app->user->can('删除商品')): ?>
                <li><a href="javascript:void(0);" id="delete_goods" onClick="deleteGoods();"><span class="glyphicon glyphicon-minus"></span> 删除</a></li>
                <?php endif?>
                <?php if (Yii::$app->user->can('审核商品')): ?>
                <li><a href="javascript:void(0);" id="check_goods" onClick="checkGoods();"><span class="glyphicon glyphicon-check"></span> 审核</a></li>
                <?php endif?>
                <?php if (Yii::$app->user->can('邮费设置')): ?>
                <li><a href="javascript:void(0);" data-toggle="modal" data-target="#expressModal"><span class="glyphicon glyphicon-plane"></span> 邮费设置</a></a></li>
                <?php endif?>
            </ul>
        </div>
        <!-- 邮费设置模态框 -->
        <div class="modal fade" id="expressModal" tabindex="-1" role="dialog" aria-labelledby="expressModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="expressModalLabel">邮费设置</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="post_type" class="control-label">包邮类型:</label>
                                <?=Html::dropDownList(
    'post_type',
    $model->mail['post_type'],
    ['0' => '不包邮', '1' => '商品金额(元)', '2' => '商品数量(件)'],
    ['class' => 'form-control', 'onchange' => 'postTypeChange(this)'])
?>
                            </div>

                            <div class="form-group express-conditions" style="display:<?php echo $model->mail['post_type'] == 0 ? 'none' : 'block'; ?>">
                                <label for="express-condition" class="control-label">条件:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">满</span>
                                    <?=Html::input('text', 'amount',
    $model->mail['post_type'] == 2
    ? $model->mail['amount']
    : $model->mail['money'],
    ['class' => 'form-control', 'maxlength' => "10"]);
?>
                                    <span class="input-group-addon">包邮</span>
                                </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" id="save-post" onClick="mailSetting();">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 审核商品模态框 -->
        <div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-labelledby="gridSystemModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="gridSystemModalLabel">提示</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">是否通过此商品操作申请?</div>
                            <div class="form-group refund-reason">
                                <label for="refund-reason" class="control-label">拒绝原因(选填):</label>
                                <input type="text" class="form-control" id="refund-reason" onBlur="reasonBlur(this)">
                                <p id="error-info" style="color:red"></p>
                                <p id="success-info" style="color:green"></p>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="refund" onClick="checkSure(1);">拒绝</button>
                        <button type="button" class="btn btn-primary" id="pass" onClick="checkSure(2);">通过</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $form = ActiveForm::begin([
    'action'  => ['index'],
    'method'  => 'get',
    'options' => ['class' => 'navbar-form navbar-left'],
]);?>
        <div class="form-group form-inline">
            <?=$form->field($model, 'goods_name')->textInput(['class' => 'form-control']);?>
            <?=$form->field($model, 'status')->dropDownList(['' => '请选择'] + $model->getStatus())?>
            <?=$form->field($model, 'begin_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('开始时间')?>

            <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('截至时间')?>

            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
        <?php ActiveForm::end();?>
    </nav>

</div>
<script type="text/javascript">
    //邮费初始化

    // 选择的商品列表
    var checkList = [];
    // 选择包邮类型
    function postTypeChange(obj){
      $(".express-conditions").removeClass("has-error").show();
      $(".express-conditions #valierr").text("");
      if($(obj).val() == 1){
        $("#expressModal").find("input[name=amount]").val("").attr("check-type","decimal");
      }else if($(obj).val() == 2){
        $("#expressModal").find("input[name=amount]").val("").attr("check-type","nonnegativeInteger");
      }else{
        $(".express-conditions").hide();
        $("#expressModal").find("input[name=amount]").val("").removeAttr("check-type");
      }
    }
    // 验证是否选择商品
    function selectCheck()
    {
        checkList = $("input[name=\'selection[]\']:checked");
        console.log(checkList);
        if(checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        return true;
    }

    // 验证是否选择商品
    function selectCheck(type=1)
    {
        checkList = $("input[name=\'selection[]\']:checked");
        if(type == 1 && checkList.length !== 1){
            alert("请选中一项进行操作");
            return false;
        }
        if(type == 2 && checkList.length<1){
            alert("至少选择一项进行操作");
            return false;
        }
        return true;
    }
    // 修改商品
    function updateGoods(){
        if (selectCheck()) {
            window.location.replace("/shop-goods/update?id=" + checkList.attr("value"));
        }
    }
    // 查看商品
    function viewGoods(){
        if (selectCheck()) {
            window.location.replace("/shop-goods/view?id=" + checkList.attr("value"));
        }
    }
    // 删除商品
    function deleteGoods(){
        if (selectCheck(2) && confirm('确定要删除选择的商品吗？')) {
            var goodsId =[];
            checkList.each(function(){
                goodsId.push(this.value);
            });
            $.post(
                "/shop-goods/delete",
                {"goods_id":goodsId},
                function(res){
                    res = JSON.parse(res);
                    if(res.code == 1){
                        alert("操作成功");
                        window.location.reload();
                    } else {
                        var msg = res.data ? res.data : res.msg;
                        alert(msg);
                    }
                }
            )
        }
    }
    // 审核商品
    function checkGoods(){
        if(selectCheck(2)){
            var isTrue=true;
            checkList.each(function(i,obj){
                // 获取商品状态
                var statusText=$(obj).parent().siblings().find(".status").text();
                if(statusText!="待审核"){
                    alert("只有待审核商品才可以进行审核");
                    isTrue=false;
                    return false;
                }
            })
            // 打开模态框
            if(isTrue){
               $("#myModal").modal();
            }
        }
    }

    // 失败原因失去焦点事件
    function reasonBlur(obj)
    {
        if ($(obj).val() != '') {
            $("#error-info").html("");
        }
    }

    // 审核
    function checkSure(checkStatus){
        var checkId = [];
        checkList.each(function(){
          checkId.push(this.value);
        });
        var reason=$("#refund-reason").val();
        if (checkStatus==1 && !reason) {
            $("#error-info").html("请填写拒绝原因");
            $("#success-info").html("");
        } else {
            reason = checkStatus == 1 ? reason : '';
            $.post(
                "/shop-goods/check",
                {"goods_id":checkId,"checkStatus":checkStatus,"refundReason":reason},
                function(res){
                    if(res==0){
                        $("#error-info").html("操作失败");
                        $("#success-info").html("");
                    } else {
                        $("#error-info").html("");
                        $("#success-info").html("操作成功");
                        window.location.reload();
                    }
                }
            )
        }
    }
    // 设置邮费
    function mailSetting()
    {
        var postType = $("[name='post_type']").val();
        var amount = '';
        if (postType > 0) {
            amount = $("[name='amount']").val();
        }
        $.post(
            "/shop-goods/set-post-type",
            {"postType":postType,"amount":amount},
            function(data){
                if (data == 1){
                    if (postType == 1) {
                        mailMethod.money = amount;
                    }
                    if (postType == 2) {
                        mailMethod.amount = amount;
                    }
                    $("#expressModal").modal("hide")
                }
            }
        )
    }

</script>
<?php
$this->registerJS('
 $("#refund-reason").on("keypress",function (event) {
        if (event.keyCode == "13"){
            return false;
        }
    });
    $.get(
        "/shop-goods/get-mail-method",
        function(data){
            mailMethod =  JSON.parse(data);
        }
    );',
    View::POS_READY
);