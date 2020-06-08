<?php
use frontend\models\JSSDK;
use yii\grid\GridView;

$this->title = '投放待办';
$this->registerJsFile('@web/js/bootstrap3-validation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$jssdk       = new JSSDK(yii::$app->params['corpid'], yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();

$this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('@web/js/equipDelivery.js?v=201901301129', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<script type="text/javascript" src="/js/vconsole.min.js"></script>
<script type="text/javascript">
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
</script>
<script type="text/javascript">
    var appId = '<?php echo $signPackage["appId"]; ?>',
        timestamp = <?php echo $signPackage["timestamp"]; ?>,
        nonceStr = '<?php echo $signPackage["nonceStr"]; ?>',
        signature = '<?php echo $signPackage["signature"]; ?>';
</script>
<style>
.acceptance_fail .form-group {
    margin-bottom: 5px;
}
.acceptance,.submit-acceptance,.button-return,.acceptance_success,.acceptance_fail,.total_submit,.back_page,.error,.error1{
    display: none;
}
a:hover{
    text-decoration: none;
}
.app_version{
    margin-left: 10px;
}
#app_version_error{
    color: red;
    display: none;
 }
 .mask {
	display:none;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	margin:0 auto;
	position: fixed;
	z-index: 50;
}
.l-wrapper {
	position: absolute;
	width: 8rem;
	height: 8rem;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	margin: auto;
	text-align: center;
}
.l-wrapper img {
	max-width:50% ;
	overflow: visible;
}
.delivery_content,.table-bordered{
    border:1px solid #ccc;
    padding: 10px;
    overflow: hidden;
}
.line1{
    display: inline-block;
    width:90%;
}
</style>
    <!-- 绑定操作 -->
<form id="w0" action="/equip-delivery/bind-and-pro-group?delivery_id=<?php echo $deliveryModel->Id; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" name="_csrf" >
    <!-- 第一页开始 -->
    <div class="bind">
        <div class="form-group">
            <label for="exampleInputFactoryCode">出厂编号</label>
            <input type="text" class="form-control" id="exampleInputEmail1" name="factory_code" check-type="required"  maxlength='50' oncopy="return false;" onpaste="return false;" required-message="出厂编号不能为空！" data-deliverId = "<?php echo $deliveryModel->Id; ?>">
        </div>
        <div class="form-group">
            <label for="exampleInputFactoryCode">重复出厂编号</label>
            <input type="text" class="form-control" id="exampleInputEmail2" name="repeat_factory_code" check-type="required"  maxlength='50' onpaste="return false;" required-message="重复出厂编号不能为空！" data-deliverId = "<?php echo $deliveryModel->Id; ?>">
        </div>
        <div class="form-group">
            <label for="device">设备编号</label>
            <input type="text" class="form-control" id="device" disabled name="device" maxlength="50" value="">
        </div>
        <div class="form-group">
            <label for="exampleInputGroup">产品组</label>
            <select class="form-control" check-type="required" required-message='产品组不可为空' id="equipments-pro_group_id" name="pro_group_id">
                <!-- <option class="a" value="">请选择</option> -->
                <?php foreach ($proGroupArr as $key => $value) {?>
                <option class="a" value="<?php echo $key ?>">
                    <?php echo $value ?>
                </option>
                <?php }?>
            </select>
        </div>
        <div>
            <table class="table equipments-product table-bordered"></table>
        </div>
        <a href="#w0">
            <button type="button" class="btn btn-block btn-primary load-acceptance" data-init="1">下一步</button>
        </a>
    </div>
    <!-- 第一页结束 -->

    <!-- 第二页开始 -->
    <!-- 验收表单模块 -->
    <div class="acceptance">
        <ul id="myTab" class="nav nav-tabs">
            <li class="active">
                <a href="#home" data-toggle="tab">设备调试</a>
            </li>
            <?php if ($deliveryModel->is_lightbox > 0) {?>
            <li>
                <a href="#ios" data-toggle="tab">灯箱验收</a>
            </li>
            <?php }?>
        </ul>
        <!-- 设备验收项开始 -->
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade in active" id="home">
                <div class="form-group">
                    <label for="exampleInputEmail1">请选择网络类型</label>
                    <select class="form-control" name="sim_card" style="margin-bottom:2%;">
                        <option value="1">移动</option>
                        <option value="2">联通</option>
                        <option value="3">电信</option>
                        <option value="4">WIFI选项</option>
                    </select>
                </div>
                <div id="sim_number_id" class="form-group">
                    <label>卡号</label>
                    <span id="autoreqmark" style="color:#FF9966"> *</span>
                    <input type="text" check-type="required number4" maxlength='35' required-message="卡号不可为空" class="form-control" name="sim_number" placeholder="请输入卡号">
                </div>
                <div class="form-group">
                    <label>浓度值</label>
                    <input id="concentration" type="text" check-type="required number" class="form-control" name="concentration" range="0.5~99.0"/>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">大屏APP版本号</label>
                    <div class="app_version">
                        <?php if ($appVersionArr['big_screen_version']) {echo $appVersionArr['big_screen_version'];} else {echo "暂无大屏版本号";}?>
                    </div>
                    <input type="hidden" class="form-control big_app_number" name="big_app_number" value="<?php echo $appVersionArr['big_screen_version'] ?>" >
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">小屏APP版本号</label>
                    <div class="app_version">
                        <?php if ($appVersionArr['small_screen_version']) {echo $appVersionArr['small_screen_version'];} else {echo "暂无小屏版本号";}?>
                    </div>
                    <input type="hidden" class="form-control app_number" name="app_number" value="<?php echo $appVersionArr['small_screen_version'] ?>" >
                </div>

                <div id="app_version_error">大小屏App版本号不可为空</div>
                <!-- 设备调试项 -->
                <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'options'      => ['class' => 'grid-view', 'style' => 'overflow:auto', 'id' => 'grid'],
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class'    => 'yii\grid\CheckboxColumn',
            'name'     => 'id',
            'multiple' => false,
        ],
        [
            'label' => '设备调试项',
            'value' => function ($model) {
                return $model->debug_item;
            },
        ],

    ],
]);
?>
                <?=$this->render('/site/water-form', ['orgId' => $orgId]);?>

                <br/>
            </div>
            <!-- 设备验收结束 -->
            <?php if ($deliveryModel->is_lightbox > 0) {?>
            <!-- 灯箱验收开始 -->
            <div class="tab-pane fade" id="ios">
                <div class="form-group">
                    <label for="exampleInputEmail1">漏电断路器型号</label>
                    <input type="text" class="form-control" check-type="required" required-message='漏电断路器型号不可为空' name="leakage_circuit" maxlength='30' value="">
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">电表型号</label>
                    <input type="text" class="form-control" maxlength='30' check-type="required" required-message='电表型号不可为空' name="meter_model" value="">
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">定时器型号</label>
                    <input type="text" class="form-control" maxlength='30' check-type="required" required-message='定时器型号不可为空' name="timer_model" value="">
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">电表初始值</label>
                    <input type="text" class="form-control"  maxlength='30' check-type="required number" required-message='电量数值不可为空' name="power_value" value="">
                </div>
                <!-- 灯箱调试项 -->
                <span style="font-weight:bold">请勾选通过的项目：</span>
                <table class="table table-bordered">
                    <?php foreach ($lightBoxDebugArr as $key => $value) {?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td>
                            <input type="checkbox" name="debug_item[]" value="<?php echo $value['Id'] ?>" />
                        </td>
                        <td><?php echo $value['debug_item'] ?></td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <!-- 灯箱验收结束 -->
            <?php }?>
        </div>
    </div>
    <div style="text-align: center;width:100%;">
        <a href="#w0">
            <button type="button" class="btn btn-primary submit-acceptance" style="width:30%;margin-right: 5%;">下一步</button>
        </a>
        <a href="#w0">
            <button type="button" class="btn btn-info button-return" style="width:30%;">返回</button>
        </a>
    </div>
    <!-- 第二页结束 -->
    <!--成功显示的页面-->
    <div class="acceptance_success">
        <table style="width:100%">
            <tr class="operation-status" >
                <td >
                    <label>运营状态</label>
                    <span id="autoreqmark" style="color:#FF9966"> *</span>
                    <select class="form-control" name="delivery_result" id="delivery_result">
                        <option value="">请选择</option>
                        <option value="0">商业运营</option>
                        <option value="1">未运营</option>
                        <option value="2">内部使用</option>
                        <option value="3">测试使用</option>
                        <option value="4">临时运营</option>
                    </select>
                </td>
            </tr>
            <tr class="reson">
                <td class="form-group" style="width:100%;">
                    <span>原因：</span>
                    <textarea class="form-control" name="reason" rows="3" style="width:100%;"></textarea>
                </td>
            </tr>
            <tr class="remark">
                <td class="form-group" style="width:100%;">
                    <span>备注：</span>
                    <textarea class="form-control" name="remark" rows="3"></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="task_id" value="<?php echo $taskId; ?>" >
    </div>

    <!-- 失败跳转的页面 -->
    <div class="acceptance_fail">
        <table class="table table-bordered">
            <h5 style="font-weight: bold;">勾选故障现象：</h5>
            <?php foreach ($malfunctionArr as $key =>
    $value) {?>
            <div class="form-group">
                <span style="display: inline-block;width:4%;vertical-align: top;">
                    <input type="checkbox" name="content[]" value="<?php echo $value['id'] ?>" style="display: inline-block;"/></span>
                <span style="display: inline-block;width:5%;vertical-align: top;text-align:right;">
                    <?php echo $key + 1; ?>.</span>
                <span style="display: inline-block;width:80%">
                    <?php echo $value['symptom'] ?></span>

            </div>
            <?php }?>
            <div class="form-group">
                <p style="font-weight: bold">备注</p>
                <textarea class="form-control" name="fail_remark" rows="3"></textarea>
            </div>
            <div class="error1" style="color:red;margin-bottom: 2%;">*故障现象和备注至少填写一项</div>
        </table>
    </div>

    <!-- 表单提交 -->
    <div style="text-align: center;margin-top: 5%;">
        <input type="hidden" id="end_latitude" name="end_latitude" value="" />
        <input type="hidden" id="end_longitude" name="end_longitude" value="" />
        <input type="hidden" id="end_address" name="end_address" value="" />

        <button type="button" id="task_submit" class="btn btn-success total_submit" style="width:30%;margin-right: 5%;">提交</button>
        <a href="#w0"><input type="button" class="btn btn-info back_page"  value="返回" style="width:30%;"></a>
    </div>
</form>
<!--加载动画-->
<div class="mask">
	<div class="l-wrapper">
		<img src="/images/loading.gif"/>
	</div>
</div>
