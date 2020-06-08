<?php

use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryBuilding */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/jquery.cxselect.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/delivery-build.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="delivery-building-form">

    <?php $form = ActiveForm::begin();?>

<?=$form->field($model, 'business_time')->textInput(['maxlength' => true])->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'time',
    'clientOptions' => [
        'showSeconds' => false,
    ],
]);
?>
<?=$form->field($model, 'end_time')->textInput(['maxlength' => true])->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'time',
    'clientOptions' => [
        'showSeconds' => false,
    ],
]);
?>
    <?=$form->field($model, 'min_consum')->textInput()?>
    <?=$form->field($model, 'delivery_region_id')->hiddenInput()->label(false);?>
    <div style="padding:10px;">
        <p><label class="control-label">添加配送员</label>&nbsp;&nbsp;<a class="btn btn-success" id="addDeliveryPersonBtn">添加</a></p>
        <div id="deliveryPerson">
        </div>
            <div class="form-group">
                </div>
        <div class="form-group field-deliverybuilding-coverage_radius required">
            <label class="control-label" for="deliverybuilding-coverage_radius"><input type="radio" id="deliverybuilding-radius" name="DeliveryBuilding[radius]" value="0" aria-required="true">范围半径</label>
            <input type="text" id="deliverybuilding-coverage_radius" class="form-control" name="DeliveryBuilding[coverage_radius]" value="20.00" aria-required="true">
            <div class="help-block"></div>
            <label class="control-label" for="deliverybuilding-coverage_radius"><input type="radio" id="deliverybuilding-radius" name="DeliveryBuilding[radius]" value="1" aria-required="true">配送范围（多边形）</label><a
                    href="#" class="btn" id="editable">编辑</a>
            <div id="container" style="width:100%;height:400px;"></div>
        </div>


        <p style="width:100%;text-align: left;margin-top:20px;"><a class="btn btn-success" id="confirmBtn">保存</a></p>

    </div>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(function(){
            function getDeliveryPersonList(deliveryPersonList){
                var newBuildingDeliveryData = <?php echo json_encode($personList); ?>;
                var deliveryPersonOptionHtml = "";
                for(var i=0;i<newBuildingDeliveryData.length;i++){
                    var idStatus = false;
                    for(var j=0;j<deliveryPersonList.length;j++){
                        if(newBuildingDeliveryData[i].person_id == deliveryPersonList[j]){
                            idStatus = true;
                        }
                    }
                    if(!idStatus){
                        deliveryPersonOptionHtml+='<option value="'+newBuildingDeliveryData[i].person_id+'">'+newBuildingDeliveryData[i].person_name+'</option>';
                    }
                }
                var deliveryPersonHtml = '<div class="form-group  form-inline"><div calss="form-group"><label class="control-label">配送员<span></span>:&nbsp;&nbsp;</label><select class="form-control delivery-form-control" name="DeliveryBuilding[person_info][]"><option value="">请选择</option>'+deliveryPersonOptionHtml+'</select>&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-success">删除</a></div></div>';
                return deliveryPersonHtml;
            }
            $("#addDeliveryPersonBtn").on("click",function(){
                // console.log("add")
                // deliveryPersonList是配送员id数组
                var deliveryPersonList = [];
                $("#deliveryPerson").find("select").each(function(index,item){
                    deliveryPersonList.push($(item).val());
                    // deliveryPersonList.push($(item).find("option:selected").text());
                });
                var newPerson = $(getDeliveryPersonList(deliveryPersonList));
                $("#deliveryPerson").append(newPerson);
                $(newPerson).find(".btn").on("click",function(){
                    $(newPerson).remove();
                });

            });
            $("#confirmBtn").on("click",function(){
                console.log("确认");
                var verifySelect = true;
                // if($("#deliveryPerson").find("select").length==0){
                //     alert("请选择配送员！");
                //     return false;
                // }
                $("#deliveryPerson").find("select").each(function(index,item){
                    if($(item).val()==""){
                        verifySelect = false;
                    }
                })
                if(!verifySelect) {
                    alert("请选择配送员！");
                    return false;
                }
                // deliveryPersonList是配送员id数组
                deliveryPersonList = [];
                $("#deliveryPerson").find("select").each(function(index,item){
                    deliveryPersonList.push($(item).val());
                    // deliveryPersonList.push($(item).find("option:selected").text());
                });
                var arrayRepeatFlag = false;
                var deliveryPersonListStr = JSON.stringify(deliveryPersonList);
                for(var i=0;i<deliveryPersonList.length;i++){
                    if(deliveryPersonListStr.indexOf('"'+String(deliveryPersonList[i])+'"')!=deliveryPersonListStr.lastIndexOf('"'+String(deliveryPersonList[i])+'"')){
                        arrayRepeatFlag = true;
                        break;
                    }
                }
                if(arrayRepeatFlag){
                    alert("配送员选择有重复，请重新选择！");
                    return false;
                }
                //绑定submit事件
                $('#w0').submit();
            });
            //编辑
            var pageType = '<?php echo $sign; ?>'; // 编辑为 edit
            var editPersonList = <?php echo json_encode($model->person_info); ?>;
            if(pageType=="edit"){
                for(var k=0;k< editPersonList.length;k++){
                    var paiIdArray = <?php echo json_encode($model->person_info); ?>;
                    var reslist = [];
                    //加入除了本次id之外的所有id
                    var idKeys = paiIdArray.indexOf(editPersonList[k]);
                    if(idKeys > -1){
                        paiIdArray.splice(idKeys,1);
                    }
                    var newPerson = $(getDeliveryPersonList(paiIdArray));
                    $("#deliveryPerson").append(newPerson);
                    $(newPerson).find("select").val(editPersonList[k]);
                    (function(newPerson){
                        $(newPerson).find(".btn").on("click",function(){
                            $(newPerson).remove();
                        });
                    })(newPerson);
                }
            }
        })

    </script>


    <?php ActiveForm::end();?>

</div>
