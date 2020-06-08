<?php
use backend\assets\AppAsset;
use common\models\EquipProductGroupApi;
use kartik\select2\Select2;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerCssFile("/css/equipment_product_group.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/jquery.lazyload.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/uploadPreview.min.js?v=1.4", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/equipment_product_group.js?v=3.8", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile('@web/js/group_create_build.js?v=1.8', ['depends' => [JqueryAsset::className()]]);
$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '产品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<script>
    // 标签数据
    var equipLabelList = <?php echo $equipLabelList; ?>;
    var editBuildingList = <?php echo $buildList; ?>;
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var isCopy=<?php echo $isCopy ?? 0; ?>;
</script>
<?php $form = ActiveForm::begin();?>
    <input type="hidden" id="csrf" value="<?php echo Yii::$app->request->csrfToken; ?>">
    <div class="form-inline product-group">
        <?=$form->field($model, 'group_name')->textInput()?>
        <?=$form->field($model, 'group_desc')->textInput()?>
    </div>
    <div class="row-fluid">
        <?=$form->field($model, 'is_update_product')->radioList(['0' => '否', '1' => '是'])?>
        <?=$form->field($model, 'is_update_recipe')->radioList(['0' => '否', '1' => '是'])?>
        <?=$form->field($model, 'setup_get_coffee')->dropDownList(['0' => '是', '1' => '否'])?>
        <?=$form->field($model, 'setup_no_coffee_msg')->textInput(['maxlength' => '50'])?>
        <?=$form->field($model, 'is_update_progress')->radioList(['0' => '否', '1' => '是'])?>

        <div class="form-group">
            <label>产品组料仓信息</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'pro_group_stock_info_id',
    'data'          => EquipProductGroupApi::getGroupStockIdAndName(),
    'options'       => ['multiple' => false, 'placeholder' => '请选择产品组料仓信息', "onchange" => "changeStockInfo(this)", "check-type" => "required"],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
    </div>
    <input type="hidden" class="groupId" name="EquipmentProductGroup[product_group_id]" value="<?php echo $model->product_group_id; ?>">
    <div class="form-inline field-equipmentproductgroup-group_status">
        <?=$form->field($model, 'build_type_upload')->dropDownList(['0' => '请选择', '1' => '批量上传楼宇', '2' => '搜索楼宇'], ['onChange' => 'addBuildingType($(this))'])?>
    </div>
    <div class="add-building-file">
        <?=$this->render('create-build.php', ['model' => $model]);?>
    </div>
    <div class="panel panel-default search-building">
        <p class="panel-heading">添加楼宇</p>
        <div class="panel-body">
            <?=$this->render('_building_search_where.php');?>
            <?=$this->render('_building.php');?>
        </div>
    </div>
    <div>
        <p class="panel-heading" style="background-color: #f5f5f5; color:#333;">请选择单品</p>
        <div class="form-group pdtType"><span style="margin-right:10px;"><input type="radio" pid="0" checked  name="cfName" id="cfNormal" class="cfType">普通单品</span><span><input type="radio" pid="1" name="cfName" id="cfZselect" class="cfType">臻选咖啡</span></div>
        <div>
            <div class="product-list"></div>
            <div class="product-list2"></div>
            <div class="product-list3"></div>
        </div>
    </div>
    <div class="submit-error"></div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">保存</button>
    </div>
<?php ActiveForm::end();?>
<!--提示框-->
<?=$this->render('/coupon-send-task/_tip.php');?>
<script id="groupCoffeeListTpl" type="text/html">
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>产品名称</th>
                <th>产品价格</th>
                <th>是否选糖</th>
                <th>根据需要修改默认出糖值(秒)</th>
                <th>旧版菜单饮品图</th>
                <th>新版菜单饮品图</th>
                <th>新版详情成分图</th>
                <th>产品顺序</th>
                <th>是否使用优惠券</th>
                <th><input type="checkbox" value="" data-check=".id-checkbox"/>全选</th>
            </tr>
        </thead>

        <tbody>
            {{# var i = 0;}}
            {{# $.each(d, function(index, item){  }}
            <tr data-key="{{item.product_id}}">
                <td class="form-group">
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][product_id]" value="{{item.product_id}}">
                    <input class="form-control groupCoffeName" name="groupCoffeeList[{{item.product_id}}][group_coffee_name]" value="{{item.product_name}}" maxlength="25" type="text" check-type="required">
                </td>
                <td class="form-group">
                    <input id="equipmentproductgrouplist-group_coffee_price" class="form-control" name="groupCoffeeList[{{item.product_id}}][group_coffee_price]" maxlength="6" value="{{item.product_price}}" type="text" check-type="plus">
                 </td>
               {{# if(item.cf_choose_sugar === 1) { }}
                    <td class="is-choose-sugar">
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="0" type="radio" onchange="isChooseSugar($(this))"> 否</label>
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="1" type="radio" checked="checked" onchange="isChooseSugar($(this))"> 是</label>
                    </td>
                    <td>
                        <div class="choose-sugar">
                            <div class="form-inline form-group field-equipmentproductgrouplist-half_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-half_sugar">半糖</label>
                                <input id="equipmentproductgrouplist-half_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][half_sugar]" maxlength="5" value="{{item.half_sugar}}" type="text">
                            </div>
                            <div class="form-inline form-group field-equipmentproductgrouplist-full_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-full_sugar">全糖</label>
                                <input id="equipmentproductgrouplist-full_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][full_sugar]" value="{{item.full_sugar}}" maxlength="5" type="text">
                            </div>
                        </div>
                    </td>
                {{# }else{ }}
                    <td class="is-choose-sugar">
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="0" type="radio" checked="checked" onchange="isChooseSugar($(this))"> 否</label>
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="1" type="radio" onchange="isChooseSugar($(this))"> 是</label>
                    </td>
                    <td>
                        <div class="choose-sugar">
                            <div class="form-inline form-group field-equipmentproductgrouplist-half_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-half_sugar">半糖</label>
                                <input id="equipmentproductgrouplist-half_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][half_sugar]" value="" type="text">
                            </div>
                            <div class="form-inline form-group field-equipmentproductgrouplist-full_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-full_sugar">全糖</label>
                                <input id="equipmentproductgrouplist-full_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][full_sugar]" value="" type="text">
                            </div>
                        </div>
                    </td>
                {{# } }}
                <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_cover">
                        <input name="" value="" type="hidden">
                        <input id="cover_{{i}}" name="EquipmentProductGroupList[group_coffee_cover][{{item.product_id}}]" value="{{item.group_coffee_cover}}" type="file">
                    </div>
                    <div class="form-group" id="imgdiv{{i}}">
                        <img class="lazy" id="imgShow_{{i}}" data-original="{{item.group_coffee_cover}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_cover]" value="{{item.group_coffee_cover}}">
                </td>
                 <!-- 新增产品封面图 -->
                <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_new_cover">
                        <input name="" value="" type="hidden">
                        <input id="coverb_{{i}}" name="EquipmentProductGroupList[group_coffee_new_cover][{{item.product_id}}]" value="{{item.group_coffee_new_cover}}" type="file">
                    </div>
                    <div class="form-group" id="imgdivb{{i}}">
                        <img class="lazy is_upload" id="imgShowb_{{i}}" data-original="{{item.group_coffee_new_cover}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_new_cover]" value="{{item.group_coffee_new_cover}}">
                    <label id="product_img_errors{{i}}" style="color:red"></label>
                </td>

                <!-- 新增产品流程图 -->
                <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_flowchart">
                        <input name="" value="" type="hidden">
                        <input id="covera_{{i}}" name="EquipmentProductGroupList[group_coffee_flowchart][{{item.product_id}}]" value="{{item.group_coffee_flowchart}}" type="file">
                    </div>
                    <div class="form-group" id="imgdiva{{i}}">
                        <img class="lazy isupload" id="imgShowa_{{i}}" data-original="{{item.group_coffee_flowchart}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_flowchart]" value="{{item.group_coffee_flowchart}}">
                    <label id="product_img_error{{i}}" style="color:red"></label>
                </td>
                 <td class="form-group">
                    <input id="equipmentproductgrouplist-group_coffee_sort" class="form-control" name="groupCoffeeList[{{item.product_id}}][group_coffee_sort]" maxlength="5" value="{{item.group_coffee_sort}}" type="text" check-type="plus">
                 </td>
                <td>
                    {{# if(item.is_use_coupon == 1) { }}
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="0"type="radio"> 否
                        <input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="1" checked="checked" type="radio"> 是</label>
                    {{# }else{ }}
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" checked="checked" value="0" type="radio"> 否
                        <input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="1"type="radio"> 是</label>
                    {{# } }}
                </td>
                <td>
                    {{# if(item.isSelect == 1) { }}
                        <input class="id-checkbox" type="checkbox" checked="checked" name="groupCoffeeList[{{item.product_id}}][isSelect]" value="1"/>
                    {{# }else{ }}
                        <input class="id-checkbox" type="checkbox" name="groupCoffeeList[{{item.product_id}}][isSelect]" value="0"/>
                    {{# } }}
                </td>
             </tr>
             {{# i++;}}
            {{# }) }}
        </tbody>
    </table>
</script>
<!--臻选咖啡列表模板-->
<script id="groupCoffeeList2Tpl" type="text/html">
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>产品名称</th>
                <th>产品价格</th>
                <th>是否选糖</th>
                <th>根据需要修改默认出糖值(秒)</th>
                <th>旧版菜单饮品图</th>
                <th>新版菜单饮品图</th>
                <th>新版详情成分图</th>
                <th>产品顺序</th>
                <th>是否使用优惠券</th>
                <th><input type="checkbox" value="" data-check=".id-checkbox"/>全选</th>
            </tr>
        </thead>

        <tbody>
            {{# var i = 0;}}
            {{# $.each(d, function(index, item){  }}
            <tr data-key="{{item.product_id}}">
                <td class="form-group">
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][product_id]" value="{{item.product_id}}">
                    <input class="form-control groupCoffeName" name="groupCoffeeList[{{item.product_id}}][group_coffee_name]" value="{{item.product_name}}" maxlength="25" type="text" check-type="required">
                </td>
                <td class="form-group">
                    <input id="equipmentproductgrouplist-group_coffee_price" class="form-control" name="groupCoffeeList[{{item.product_id}}][group_coffee_price]" maxlength="6" value="{{item.product_price}}" type="text" check-type="plus">
                 </td>
               {{# if(item.cf_choose_sugar === 1) { }}
                    <td class="is-choose-sugar">
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="0" type="radio" onchange="isChooseSugar($(this))"> 否</label>
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="1" type="radio" checked="checked" onchange="isChooseSugar($(this))"> 是</label>
                    </td>
                    <td>
                        <div class="choose-sugar">
                            <div class="form-inline form-group field-equipmentproductgrouplist-half_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-half_sugar">半糖</label>
                                <input id="equipmentproductgrouplist-half_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][half_sugar]" maxlength="5" value="{{item.half_sugar}}" type="text">
                            </div>
                            <div class="form-inline form-group field-equipmentproductgrouplist-full_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-full_sugar">全糖</label>
                                <input id="equipmentproductgrouplist-full_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][full_sugar]" value="{{item.full_sugar}}" maxlength="5" type="text">
                            </div>
                        </div>
                    </td>
                {{# }else{ }}
                    <td class="is-choose-sugar">
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="0" type="radio" checked="checked" onchange="isChooseSugar($(this))"> 否</label>
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_choose_sugar]" value="1" type="radio" onchange="isChooseSugar($(this))"> 是</label>
                    </td>
                    <td>
                        <div class="choose-sugar">
                            <div class="form-inline form-group field-equipmentproductgrouplist-half_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-half_sugar">半糖</label>
                                <input id="equipmentproductgrouplist-half_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][half_sugar]" value="" type="text">
                            </div>
                            <div class="form-inline form-group field-equipmentproductgrouplist-full_sugar">
                                <label class="control-label" for="equipmentproductgrouplist-full_sugar">全糖</label>
                                <input id="equipmentproductgrouplist-full_sugar" class="form-control" name="groupCoffeeList[{{item.product_id}}][full_sugar]" value="" type="text">
                            </div>
                        </div>
                    </td>
                {{# } }}
                <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_cover">
                        <input name="" value="" type="hidden">
                        <input id="zcover_{{i}}" name="EquipmentProductGroupList[group_coffee_cover][{{item.product_id}}]" value="{{item.group_coffee_cover}}" type="file">
                    </div>
                    <div class="form-group" id="zimgdiv{{i}}">
                        <img class="lazy" id="zimgShow_{{i}}" data-original="{{item.group_coffee_cover}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_cover]" value="{{item.group_coffee_cover}}">
                </td>
                <!-- 新增产品封面图 -->
                 <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_new_cover">
                        <input name="" value="" type="hidden">
                        <input id="zcoverb_{{i}}" name="EquipmentProductGroupList[group_coffee_new_cover][{{item.product_id}}]" value="{{item.group_coffee_new_cover}}" type="file">
                    </div>
                    <div class="form-group" id="zimgdivb{{i}}">
                        <img class="lazy is_uploads" id="zimgShowb_{{i}}" data-original="{{item.group_coffee_new_cover}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_new_cover]" value="{{item.group_coffee_new_cover}}">
                     <label id="product_imgs_errors{{i}}" style="color:red"></label>
                </td>
                 <!-- 新增产品流程图 -->
                 <td>
                    <div class="form-group field-equipmentproductgrouplist-group_coffee_flowchart">
                        <input name="" value="" type="hidden">
                        <input id="zcovera_{{i}}" name="EquipmentProductGroupList[group_coffee_flowchart][{{item.product_id}}]" value="{{item.group_coffee_flowchart}}" type="file">
                    </div>
                    <div class="form-group" id="zimgdiva{{i}}">
                        <img class="lazy isuploads" id="zimgShowa_{{i}}" data-original="{{item.group_coffee_flowchart}}" width="100" height="100">
                    </div>
                    <input type="hidden" name="groupCoffeeList[{{item.product_id}}][group_coffee_flowchart]" value="{{item.group_coffee_flowchart}}">
                     <label id="product_imgs_error{{i}}" style="color:red"></label>
                </td>
                 <td class="form-group">
                    <input id="equipmentproductgrouplist-group_coffee_sort" class="form-control" name="groupCoffeeList[{{item.product_id}}][group_coffee_sort]" maxlength="5" value="{{item.group_coffee_sort}}" type="text" check-type="plus">
                 </td>
                <td>
                    {{# if(item.is_use_coupon == 1) { }}
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="0"type="radio"> 否
                        <input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="1" checked="checked" type="radio"> 是</label>
                    {{# }else{ }}
                        <label><input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" checked="checked" value="0" type="radio"> 否
                        <input name="groupCoffeeList[{{item.product_id}}][is_use_coupon]" value="1"type="radio"> 是</label>
                    {{# } }}
                </td>
                <td>
                    {{# if(item.isSelect == 1) { }}
                        <input class="id-checkbox" type="checkbox" checked="checked" name="groupCoffeeList[{{item.product_id}}][isSelect]" value="1"/>
                    {{# }else{ }}
                        <input class="id-checkbox" type="checkbox" name="groupCoffeeList[{{item.product_id}}][isSelect]" value="0"/>
                    {{# } }}
                </td>
             </tr>
             {{# i++;}}
            {{# }) }}
        </tbody>
    </table>
</script>

<!---->
<!--<script id="equipmentLabelTpl" type="text/html">-->
<!--    {{# if(d.equipmentLabelArr){ }}-->
<!--        {{# $.each(d.equipmentLabelArr, function(index, item){ }}-->
<!--        <div class="form-inline" id="{{item.label_id}}">-->
<!--            <div class="form-group">-->
<!--                <label>标签名称</label>-->
<!--                <input class="form-control" id="labelName" type="text" name="equipLabelList[{{index}}][label_name]" value="{{item.label_name}}" check-type="required" maxlength="10"/>-->
<!--            </div>-->
<!--            <div class="form-group productNum">-->
<!--                <label>单品数量</label>-->
<!--                <span id="productNum">{{item.checkProductNum}}</span>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--                <label class="control-label">顺序</label>-->
<!--                <input class="form-control" id="labelSort" type="text" name="equipLabelList[{{index}}][sort]" value="{{item.sort}}" check-type="int" maxlength="10" onchange="labelSortDistinct(this)"/>-->
<!--            </div>-->
<!--            <div class="form-group">-->
<!--               <button class="btn btn-danger btn-sm" type="button" onclick="delTag(this)">删除</button>-->
<!--            </div>-->
<!--            <div class="checkbox">-->
<!--                {{# $.each(d.groupCoffeNameArr, function(key, value){ }}-->
<!--                    <label>-->
<!--                    {{# if(item.labelProductIdList.indexOf(Number(key)) > -1){  }}-->
<!--                        <input class="product-checkbox" type="checkbox" name="equipLabelList[{{index}}][labelProductIdList][]" value="{{key}}" checked="checked" onclick="getProductNum(this)"/>{{value}}-->
<!--                    {{#   }else{ }}-->
<!--                        <input class="product-checkbox" type="checkbox" name="equipLabelList[{{index}}][labelProductIdList][]" value="{{key}}" onclick="getProductNum(this)"/>{{value}}-->
<!--                    {{#   } }}-->
<!--                    </label>-->
<!--                {{# })  }}-->
<!--            </div>-->
<!--        </div>-->
<!--        {{# }) }}-->
<!--    {{# }else{ }}-->
<!--        <div class="form-inline" id="tagID{{d.tagNum}}">-->
<!--        <div class="form-group">-->
<!--            <label>标签名称</label>-->
<!--            <input class="form-control" id="labelName" type="text" name="equipLabelList[{{tagNum}}][label_name]" value="" check-type="required" maxlength="10"/>-->
<!--        </div>-->
<!--        <div class="form-group productNum">-->
<!--            <label>单品数量</label>-->
<!--            <span id="productNum">（0）</span>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <label class="control-label">顺序</label>-->
<!--            <input class="form-control" id="labelSort" type="text" name="equipLabelList[{{tagNum}}][sort]" value="" check-type="int" maxlength="10" onchange="labelSortDistinct(this)"/>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--           <button class="btn btn-danger btn-sm" type="button" onclick="delTag(this)">删除</button>-->
<!--        </div>-->
<!--        <div class="checkbox">-->
<!--            {{# $.each(d.groupCoffeNameArr, function(key, value){ }}-->
<!--                <label>-->
<!--                    <input class="product-checkbox" type="checkbox" name="equipLabelList[{{tagNum}}][labelProductIdList][]" value="{{key}}" onclick="getProductNum(this)"/>{{value}}-->
<!--                </label>-->
<!--            {{# })  }}-->
<!--        </div>-->
<!--    </div>-->
<!--    {{# } }}-->
<!---->
<!---->
<!--</script>-->

