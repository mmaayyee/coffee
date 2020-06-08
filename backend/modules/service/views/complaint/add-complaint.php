<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<h3>添加客诉</h3>
<script type="text/javascript">
var optionData = <?php echo json_encode($optionData); ?>;
var building_id =<?php echo isset($model->building_id) ? $model->building_id : 0; ?>;
var org_id =<?php echo isset($model->org_id) ? $model->org_id : 0; ?>;
var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
var historyUrl = "/service/complaint/index";
var questionID ="<?php echo $model->question_type_id; ?>";
var id = "<?php echo Yii::$app->request->get('complain_id', ''); ?>";
var type = id==""?"1":"2";
var saveUrl = '/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=11';
function uploadFile() {
    $(".btn-success").attr('disabled','disabled');
    $("#w0").find('input,select,textarea').trigger('blur');
    setTimeout(function () {
        if($(".has-error").length>0){
            $(".btn-success").removeAttr('disabled');
            return false;
        }
        var formData = $("form").serializeArray();
        $.ajax({
            url: url+"erpapi/customer-service/add-complaint.html",
            dataType: 'json',
            type: 'post',
            data: formData,
            success: function(data) {
                console.log(data);
                if(data.error_code == 0) {
                    $.ajax({
                        type: "GET",
                        url: saveUrl,
                        success: function(data) {
                            window.location.href=historyUrl;
                        },
                        error: function() {
                            window.location.href=historyUrl;
                        }
                    });
                }else {
                    $(".btn-success").removeAttr('disabled');
                    alert(data.msg);
                    $(".submit-error").html("客诉添加失败!");
                }
            },
            error: function() {
                $(".btn-success").removeAttr('disabled');
                $(".submit-error").html('客诉添加失败!');
            }
        });
    },1000);
}

</script>

<?php $form = ActiveForm::begin([
    'options'     => ['class' => 'form-horizontal'],
    'fieldConfig' => [ //统一修改字段的模板
        'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-3\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-2 control-label'], //修改label的样式
    ],
]);?>

    <?=$form->field($model, 'user_consume_id')->hiddenInput([$userConsumeId])->label(false)?>
    <?=$form->field($model, 'complaint_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'user_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'manager_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'manager_name')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => $orgList,
    'options'       => ['placeholder' => '请选择机构', 'id' => 'org-name'],
    'pluginOptions' => ['allowClear' => true]])->label('机构名称')?>
    <?=$form->field($model, 'building_id')->widget(Select2::classname(), [
    'data'          => $buildingList,
    'options'       => ['placeholder' => '请输入点位名称'],
    'pluginOptions' => ['allowClear' => true]])->label('点位名称')?>

    <?=$form->field($model, 'org_city')->input('text', ['readonly' => 'readonly', 'id' => 'org-city'])->label('所在城市')?>
    <?=$form->field($model, 'advisory_type_id')->dropDownList($advisoryList, ['prompt' => '请选择', 'id' => 'advisory-id'])->label('咨询类型 * ')?>
    <?=$form->field($model, 'question_type_id')->dropDownList([], ['readonly' => 'readonly', 'id' => 'question-type'])->label('问题类型 * ')?>
    <?=$form->field($model, 'question_describe')->textarea()->label('问题描述 * ')?>
    <?=$form->field($model, 'equipment_last_log')->input('text', ['readonly' => 'readonly', 'id' => 'equipment_last_log'])->label('后台显示问题')?>
    <?=$form->field($model, 'equipment_type')->input('text', ['id' => 'equipment_type', 'readonly' => 'readonly'])->label('设备类型')?>
    <?=$form->field($model, 'customer_name')->label('客户名称')?>
    <?=$form->field($model, 'callin_mobile')->label('来电号码')?>
    <?=$form->field($model, 'register_mobile')->label('注册电话')?>
    <?=$form->field($model, 'nikename')->label('昵称')?>
    <?=$form->field($model, 'pay_type')->dropDownList($payWayList)->label('支付方式')?>
    <?=$form->field($model, 'buy_type')->label('购买品种')?>
    <?=$form->field($model, 'buy_time')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('购买时间')?>
    <?=$form->field($model, 'solution_id')->dropDownList($solutionList, ['prompt' => '请选择'])->label('协商解决方案')?>
    <?=$form->field($model, 'retired_coffee_type')->label('已退咖啡品种')?>
    <?=$form->field($model, 'order_refund_price')->label('需退款金额（元）')?>
    <?=$form->field($model, 'order_code')->textarea()->label('订单编号')?>
    <?=$form->field($model, 'latest_refund_time')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('最迟退款日期')?>
    <?=$form->field($model, 'real_refund_time')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('实际退款日期')?>

    <?=$form->field($model, 'process_status')->dropDownList(['' => '请选择', 0 => '未解决', 1 => '已解决'])->label('进度 * ')?>
    <?=$form->field($model, 'is_consumption')->dropDownList(['' => '请选择', 0 => '否', 1 => '是'])->label('退款是否消费')?>
    <?=$form->field($model, 'customer_type')->dropDownList(['' => '请选择'] + $model::$customerTypeList)->label('客户区分')?>
    <div class="form-group">
        <a class="btn btn-default"  style="position:relative;left:300px;width:150px;"  href="<?php echo Yii::$app->request->getReferrer(); ?>">返回</a>
        <?=Html::button('提交', ['class' => 'btn btn-success ',
    'onclick'                              => 'uploadFile()',
    'style'                                => 'position:relative;left:350px;width:160px',
])?>

    </div>
<?php ActiveForm::end();?>
<?php ob_start();?>

if (org_id){
    $('#org-city').val(optionData.cityList[org_id]);
}else{
    var orgId = $("#org-name").val();
    $('#org-city').val(optionData.cityList[orgId]);
}

$('#org-name').change(function(){
	var orgId = $(this).val();
	$('#org-city').val(optionData.cityList[orgId]);
});
if (building_id){
    $('#equipment_last_log').val(optionData.equipLogList[building_id]);
    $('#equipment_type').val(optionData.equipTypeList[building_id]);
}
$('#complaint-building_id').change(function(){
	$('#equipment_last_log').val(optionData.equipLogList[$(this).val()]);
    $('#equipment_type').val(optionData.equipTypeList[$(this).val()]);
});

    var adId =$("#advisory-id").val();
    if ( adId ){
        $.each(optionData.questionList[adId],function(index,item){
                    $("#question-type").append('<option value='+index+'>'+item+'</option>');
        })
    }
    if (questionID){
        $("#question-type").val(questionID)
    }
    var quId =$("#question-type").val();
    $('#advisory-id').change(function(){
        adId = $(this).val();
        if (adId != ''){
            $("#question-type").empty();
            $.each(optionData.questionList[$(this).val()],function(index,item){
                $("#question-type").append('<option value='+index+'>'+item+'</option>');
            })
            quId =$("#question-type").val();
        }else{
            $("#question-type").empty();
        }
     });
    $('#question-type').change(function(){
            quId =$(this).val();
    });




<?php $this->registerJs(ob_get_clean());?>

<style>
    .btn-success {
        margin-bottom: 0px;
    }
</style>
