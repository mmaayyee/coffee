<?php

use backend\assets\AppAsset;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\SpecialSchedul */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '设备端活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/equipment_product_group.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/uploadPreview.min.js");
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/special_schedul.js?v=201904251650", ["depends" => [\yii\web\JqueryAsset::className()]]);
?>
<script type="text/javascript">
    var editBuildingList = <?php echo $model->buildIdList; ?>;
    var restriction = <?php if ($model->restriction_type) {echo $model->restriction_type;} else {echo '{"1":0}';}?>;
    var url = "<?php echo Yii::$app->params['fcoffeeUrl'] ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
    var activity = <?php echo Json::encode($model->discountType) ?>;
    var specialSchedulProductList = <?php echo $model->specialSchedulProductList; ?>;
    // console.log("specialSchedulProductList...",specialSchedulProductList);
    var specialSchedulData = {'specialSchedulProductList': specialSchedulProductList, 'activity':activity};
</script>
<div class="special-schedul-form">
    <?php $form = ActiveForm::begin();?>
        <div class="row-fluid">
        <?=$form->field($model, 'special_schedul_name')->textInput(['maxlength' => 50])?>
        <?=$form->field($model, 'state')->dropDownList($model->stateList)?>
        <?=$form->field($model, 'is_coupons')->dropDownList($model->isCoupon)?>
    </div>
    <div class="form-inline">
        <?=$form->field($model, 'start_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly', 'check-type' => 'required'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]]);?>

        <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly', 'check-type' => 'required compareDate'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
    ]]);?>
        <?=$form->field($model, 'user_type')->dropDownList($model->userType)?>
        <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    </div>
    <div class="form-inline">
        <div class="form-group">
            <label>限制条件</label>
            <?php if ($model->restriction_type): ?>
            <label class="limit">限制数量<input id="restriction_type" type="checkbox" checked="checked" onchange="isChangeLimitNumber(this);"/></label>
            <?php else: ?>
            <label class="limit">限制数量<input id="restriction_type" type="checkbox" onchange="isChangeLimitNumber(this);"/></label>
            <?php endif?>
            <span style="color:red">活动购买次数达到活动限购总数，系统会自动下架该活动</span>
        </div>
    </div>
    <div class="limit-condition"></div>
    <input type="hidden" id="csrf" value="<?php echo Yii::$app->request->csrfToken; ?>">
    <div class="form-group field-Specialschedul-add_build_type">
        <label class="control-label" for="SpecialSchedul-add_build_type">添加点位</label>
        <select id="SpecialSchedul-add_build_type" class="form-control" name="add_build_type" onchange="addBuildingType($(this))">
            <?php if (empty($model->where_string['add_build_type']) || $model->where_string['add_build_type'] == 1): ?>
                <option value="1" selected="selected">搜索添加</option>
                <option value="0">导入点位</option>
            <?php else: ?>
                <option value="1">搜索添加</option>
                <option value="0" selected="selected">导入点位</option>
            <?php endif?>

        </select>
    </div>
    <div class="block-file">
        <div class="form-group add-building-file">
            <label>上传点位</label>
            <div class="hint-block">（<span id="autoreqmark" style="color:#FF9966">*</span>导入文件必须是TXT格式的，且每个点位独占一行）</div>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'add-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadBuildFile(this)", 'check-type' => 'required fileFormat', 'fileFormat-message' => '文件上传格式不正确'])?>
            <input type="hidden" name="addBuildingFile" value=""/>
        </div>
        <div class="form-group verify-result"></div>
    </div>
    <div class="search-building">
        <?=$this->render('_building_search_where.php', ['whereString' => $model->where_string]);?>
        <?=$this->render('/equipment-product-group/_building.php');?>
    </div>
    <div>
        <h5>请选择单品</h5>
        <div class="form-group updateProductList">
            <label><input type="radio"  class="logChange"/>批量修改单品活动</label><span style="margin:0 10px;"><input type="radio" pid="0" checked  name="cfName" id="cfNormal" class="cfType">普通单品</span><span><input type="radio" pid="1" name="cfName" id="cfZselect" class="cfType">臻选咖啡</span>
        </div>
        <div class="specialSchedul"></div>
        <div class="specialSchedul2"></div>
        <div class="specialSchedul3"></div>
    </div>
    <div class="form-group">
        <?=Html::hiddenInput('isCopy', $model->isCopy)?>
        <?=Html::hiddenInput('copySpecialID', $model->copySpecialID)?>
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>
    <!--提示弹框-->
    <?=$this->render('/coupon-send-task/_tip.php');?>
    <!--批量修改单品活动弹框-->
    <div class="modal fade bs-example-modal-sm" id="batchUpdataModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 id="myModalLabel">批量修改单品活动</h4>
            </div>
            <div class="modal-body">
              <h4 class="form-group title"></h4>
            </div>
            <div class="modal-footer">
              <button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
          </div>
        </div>
    </div>
</div>
<script id="specialSchedulProductListTpl" type="text/html">
{{# var i = 0;}}
    <div class="productList">

        <table class="table table-striped table-bordered table-condensed">
            <thead>
                <tr class="text-center">
                    <th>饮品名称</th>
                    <th>单品原价</th>
                    <th>根据需要修改单品图片</th>
                    <th><input type="checkbox" data-check=".id-checkbox"/>全选</th>
                    <th>设置单品活动</th>
                </tr>
            </thead>
            <tbody>
                {{# $.each(d.specialSchedulProductList, function(index, item){  }}
                <tr data-key="{{item.product_id}}">
                    <td>{{item.product_name}}</td>
                    <td class="form-group">{{item.product_price}}</td>
                    <td>
                        <div class="form-group field-equipmentproductgrouplist-group_coffee_cover">
                            <input id="{{d.productType}}cover_{{i}}" name="SpecialSchedulProductList[special_product_photo][{{index}}]" value="{{item.product_cover}}" type="file">
                        </div>
                        <div class="form-group" id="{{d.productType}}imgdiv{{i}}">
                            <img id="{{d.productType}}imgShow_{{i}}" src="{{item.product_cover}}" width="100" height="100">
                        </div>
                    </td>
                    <td>
                        {{# if(item.is_select){ }}
                        <input class="id-checkbox" type="checkbox" name="SpecialSchedulProductList[{{index}}][product_id]" checked value="{{index}}"/>
                        {{# } else { }}
                        <input class="id-checkbox" type="checkbox" name="SpecialSchedulProductList[{{index}}][product_id]" value="{{index}}"/>
                        {{# } }}
                    </td>
                    <td class="set-up-activity">
                        <select class="form-group form-control" name="SpecialSchedulProductList[{{index}}][activity_type]" disabled="disabled" onchange="isChangeActivityType(this)" >
                        {{# for(var x in d.activity){ }}
                        {{# if(item.activity_info.activity_type && item.activity_info.activity_type== x) { }}
                           <option value="{{x}}" selected="selected">{{d.activity[x].name}}</option>
                        {{# }else{ }}
                           <option value="{{x}}">{{d.activity[x].name}}</option>
                        {{# } }}
                        {{# } }}
                        </select>
                        <div class="form-inline">
                            {{# if(item.activity_info.activity_type) { }}
                                {{# for(var configKey in item.activity_info.activity_config){ }}
                                <div>
                                {{# for(var x in d.activity[item.activity_info.activity_type].typeParam){ }}
                                    {{# if (item.activity_info.activity_type == 1){ }}
                                        <div class="form-group">
                                            <lable>{{d.activity[item.activity_info.activity_type].typeParam[x]}}</lable>
                                        {{# if(x == "activity_name"){ }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required" maxlength="6"/>
                                        {{# }  else { }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required number" maxlength="5" style="width:50px"/>&nbsp;&nbsp;&nbsp;
                                        {{# } }}
                                        </div>
                                    {{# } else if (item.activity_info.activity_type == 2){ }}
                                        <div class="form-group">
                                            <lable>{{d.activity[item.activity_info.activity_type].typeParam[x]}}</lable>
                                        {{# if(x == "activity_name"){ }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required" maxlength="6" style="width:150px"/>
                                        {{# }  else { }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required number" maxlength="5" style="width:50px"/>杯{{# if(x == "gift_cups"){ }}&nbsp;&nbsp;&nbsp;{{# } }}
                                        {{# } }}
                                        </div>
                                    {{# } else if (item.activity_info.activity_type == 3){ }}
                                        <div class="form-group">
                                            <lable>{{d.activity[item.activity_info.activity_type].typeParam[x]}}</lable>
                                        {{# if(x == "activity_name"){ }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required" maxlength="6" style="width:150px"/>
                                        {{# }  else { }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required number" maxlength="5" style="width:50px"/>{{# if(x == "add_money"){ }}元{{# } else if (x == "gift_cups"){ }}杯&nbsp;&nbsp;&nbsp;{{# } }}
                                        {{# } }}
                                        </div>
                                    {{# } else if (item.activity_info.activity_type == 4){ }}
                                        <div class="form-group">
                                            <lable>{{# if(x == "last_value"){ }}第{{Number(configKey)+1}}杯 {{# } }}{{d.activity[item.activity_info.activity_type].typeParam[x]}}</lable>
                                        {{# if(x == "activity_name"){ }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required" maxlength="6" style="width:150px"/>
                                        {{# }  else { }}
                                            <input class="form-control" type="text" name="SpecialSchedulProductList[{{item.product_id}}][activity_info][{{configKey}}][{{x}}]" value="{{item.activity_info.activity_config[configKey][x]}}" check-type="required number" maxlength="5" style="width:50px"/>{{# if(x == "last_value"){ }}元&nbsp;&nbsp;&nbsp;{{# } }}
                                        {{# } }}
                                        </div>
                                    {{# } }}
                                {{# } }}
                                </div>
                                {{# if((configKey==(Object.keys(item.activity_info.activity_config)[Object.keys(item.activity_info.activity_config).length-1]))&&(item.activity_info.activity_type==2||item.activity_info.activity_type==3||item.activity_info.activity_type==4)){ }}
                                <button type="button" id="btn" class="btn btn-primary" onclick="ladderClick(this,'{{item.activity_info.activity_type}}','{{item.product_id}}',{{# if (Object.keys(item.activity_info.activity_config).length==1){ }}0{{# }  else { }}1{{# } }})">{{# if(Object.keys(item.activity_info.activity_config).length==1){ }}增加梯度{{# }  else { }}删除梯度{{# } }}</button>
                                {{# } }}
                                {{# } }}
                            {{# } }}
                        </div>
                    </td>
                 </tr>
                 {{# i++; }}
                {{# }) }}
            </tbody>
        </table>
    </div>
</script>
<!--批量修改活动模板-->
<script id="batchUpdataTpl" type="text/html">
    <select class="form-group form-control" onchange="isChangeActivityType(this);">
        {{# for(var x in d){ }}
           <option value="{{x}}">{{d[x].name}}</option>
        {{# } }}
    </select>
    <div class="form-inline activity-strategy"></div>
</script>
<!--具体活动模板-->
<script id="activityStrategyTpl" type="text/html">
    {{# for(var formIndex in d.formArr){ }}
    <div>
    {{# for(var x in d.activity[d.activityType].typeParam){ }}
        {{# if (d.activityType == 1){ }}
            <div class="form-group">
                <lable>{{d.activity[d.activityType].typeParam[x]}}</lable>
            {{# if(x == "activity_name"){ }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required" maxlength="6"/>
            {{# }  else { }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required number" maxlength="5" style="width:50px"/>&nbsp;&nbsp;&nbsp;
            {{# } }}
            </div>
        {{# } else if (d.activityType == 2){ }}
            <div class="form-group">
                <lable>{{d.activity[d.activityType].typeParam[x]}}</lable>
            {{# if(x == "activity_name"){ }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required" maxlength="6" style="width:150px"/>
            {{# }  else { }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required number" maxlength="5" style="width:50px"/>杯{{# if(x == "gift_cups"){ }}&nbsp;&nbsp;&nbsp;{{# } }}
            {{# } }}
            </div>
        {{# } else if (d.activityType == 3){ }}
            <div class="form-group">
                <lable>{{d.activity[d.activityType].typeParam[x]}}</lable>
            {{# if(x == "activity_name"){ }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required" maxlength="6" style="width:150px"/>
            {{# }  else { }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required number" maxlength="5" style="width:50px"/>{{# if(x == "add_money"){ }}元{{# } else if (x == "gift_cups"){ }}杯&nbsp;&nbsp;&nbsp;{{# } }}
            {{# } }}
            </div>
        {{# } else if (d.activityType == 4){ }}
            <div class="form-group">
                <lable>{{# if(x == "last_value"){ }}第{{Number(d.formArr[formIndex])+1}}杯 {{# } }}{{d.activity[d.activityType].typeParam[x]}}</lable>
            {{# if(x == "activity_name"){ }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required" maxlength="6" style="width:150px"/>
            {{# }  else { }}
                <input class="form-control" type="text" name="SpecialSchedulProductList[{{d.productID}}][activity_info][{{d.formArr[formIndex]}}][{{x}}]" value="" check-type="required number" maxlength="5" style="width:50px"/>{{# if(x == "last_value"){ }}元&nbsp;&nbsp;&nbsp; {{# } }}
            {{# } }}
            </div>
        {{# } }}
    {{# } }}
    </div>
    {{# if((formIndex==(d.formArr.length-1))&&(d.activityType==2||d.activityType==3||d.activityType==4)){ }}
    <button type="button" id="btn" class="btn btn-primary" onclick="ladderClick(this,'{{d.activityType}}','{{d.productID}}',{{# if (d.formArr.length==1){ }}0{{# }  else { }}1{{# } }})">{{# if(d.formArr.length==1){ }}增加梯度{{# }  else { }}删除梯度{{# } }}</button>
    {{# } }}
    {{# } }}
</script>

<!--限制条件模板-->
<script id="limitConditionTpl" type="text/html">
    {{# if (d.restriction[1] < 1 || d.limitConditionNum > 0) { }}
        <div class="form-inline">
            <div class="form-group restriction-list">
                <lable>限制类型</lable>
                <select class="form-control" data-value="1" onchange="restrictionChange($(this))" name="restriction[restriction_type][]" check-type="required">
                    <option value="1" selected="selected">每人总数</option>
                    <option value="2">每人每天总数</option>
                    <option value="3">活动总数</option>

                </select>
            </div>
             <div class="form-group">
                <lable>数量</lable>
                <input class="form-control" type="text" id="" name="restriction[restriction_num][]" value="" check-type="int" maxlength="10"/>
            </div>
            {{# if (d.limitConditionNum < 1) { }}
            <button class="btn btn-primary" type="button" onclick="addLimitCondition(this);">增加限制条件</button>
            {{# } else { }}
            <button class="btn btn-danger" type="button" onclick="delLimitCondition(this);">删除限制条件</button>
            {{# } }}
        </div>
    {{# } else { }}
        {{# for(var x in d.restriction){ }}
            <div class="form-inline">
                <div class="form-group restriction-list">
                    <lable>限制类型</lable>
                    <select class="form-control" data-value="{{x}}" onchange="restrictionChange($(this))" name="restriction[restriction_type][]" check-type="required">
                        {{# if(x == 1){ }}
                        <option value="1" selected="selected">每人总数</option>
                        <option value="2">每人每天总数</option>
                        <option value="3">活动总数</option>
                        {{# }else if(x == 2){ }}
                        <option value="1">每人总数</option>
                        <option value="2" selected="selected">每人每天总数</option>
                        <option value="3" >活动总数</option>
                        {{# }else if(x == 3){ }}
                        <option value="1" >每人总数</option>
                        <option value="2" >每人每天总数</option>
                        <option value="3" selected="selected">活动总数</option>
                        {{# } }}
                    </select>
                </div>
                 <div class="form-group">
                    <lable>数量</lable>
                    <input class="form-control" type="text" id="" name="restriction[restriction_num][]" value="{{ d.restriction[x]}}" check-type="required number" maxlength="10"/>
                </div>
                {{# if (d.limitConditionNum < 1) { }}
                <button class="btn btn-primary" type="button" onclick="addLimitCondition(this);">增加限制条件</button>
                {{# } else { }}
                <button class="btn btn-danger" type="button" onclick="delLimitCondition(this);">删除限制条件</button>
                {{# } }}
            </div>
        {{# d.limitConditionNum++ } }}
    {{# } }}
</script>