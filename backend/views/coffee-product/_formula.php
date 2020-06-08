<?php
?>
<div class="formulas"></div>
<script id="formulaTpl" type="text/html">
    {{# $.each(d.equipTypeStockList, function(idx, item) { }}
    <div class="panel panel-default">
        <p class="panel-heading">设备类型</p>
        <div class="panel-body" id="{{item.equip_type_id}}" >
            {{# var isChecked = false; }}
            {{# $.each(d.proStockRecipe, function(key, value) { }}
                {{# if(value.productSetUp && value.equip_type_id == item.equip_type_id) { }}
                    <label>
                        <input class="equipment-type" type="checkbox" name="CoffeeProduct[equipTypeRepice][{{item.equip_type_id}}][equipTypeId]" value="{{item.equip_type_id}}" onchange="isChecked(this)" checked="checked"/>{{item.equip_type_name}}
                    </label>
                {{#  isChecked = true; return false; } }}
            {{# }) }}
            {{# if(!isChecked){  }}
                <label>
                    <input class="equipment-type" type="checkbox" name="CoffeeProduct[equipTypeRepice][{{item.equip_type_id}}][equipTypeId]" value="{{item.equip_type_id}}" onchange="isChecked(this)"/>{{item.equip_type_name}}
                </label>
            {{# }  }}
                <div class="table-responsive formula"></div>
            </div>
        </div>
    </div>
{{# }) }}
</script>
<script id="addFormulaTpl" type="text/html">
  {{# if(d.checked){  }}
  {{# var modify = false; }}
    {{# $.each(d.proStockRecipe, function(key, value) { }}
       {{# if(value.productSetUp && value.equip_type_id == d.equipTypeId) { }}
            <button class="btn btn-primary btn-sm" type="button" onclick="addFormula(this);">添加单品配方</button>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                    {{# for (var x in d.equipTypeStockList[value.equip_type_id].readableAttribute){}}
                        <td>{{d.equipTypeStockList[value.equip_type_id].readableAttribute[x]}}</td>
                    {{# } }}
                        <td>操作</td>
                    </tr>
                    {{# var n=1; $.each(value.productSetUp, function(index, vals) { }}
                    <tr data-length="{{n}}">
                    {{# for (var stockName in d.equipTypeStockList[value.equip_type_id].readableAttribute){}}
                        {{# if(stockName == 'stock_code'){ }}
                            <td>
                                <select name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][stock_code]" value="{{vals.stock_code}}" onchange="selectValueChange(this)">
                                    {{# for (var x in d.equipTypeStockList[value.equip_type_id].stock){}}
                                        {{# if ( x == vals.stock_code) { }}
                                        <option value="{{x}}" selected="selected">{{d.equipTypeStockList[value.equip_type_id].stock[x]}}</option>
                                        {{# }else{ }}
                                        <option value="{{x}}">{{d.equipTypeStockList[value.equip_type_id].stock[x]}}</option>
                                        {{# } }}
                                    {{# } }}
                                </select>
                            </td>
                        {{# } else if(stockName == 'order_number'){ }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][order_number]" type="text" value="{{vals.order_number}}" check-type="required number" maxlength="4">
                            </td>
                        {{# } else if(stockName == 'water'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][water]" type="text" value="{{vals.water}}" check-type="required number" range="0.00~9999.99" maxlength="7">
                            </td>
                        {{# } else if(stockName == 'delay'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][delay]" type="text" value="{{vals.delay}}" check-type="required number" range="0.00~9999.99" maxlength="7">
                            </td>
                        {{# } else if(stockName == 'volume'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][volume]" type="text" value="{{vals.volume}}" check-type="required number" range="0.00~9999.99" maxlength="7">
                            </td>
                        {{# } else if(stockName == 'stir'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][stir]" type="text" value="{{vals.stir}}" check-type="required number" range="0.00~9999.99" maxlength="7">
                            </td>
                        {{# } else if(stockName == 'blanking'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][blanking]" type="text" value="{{vals.blanking}}" check-type="required number" maxlength="4">
                            </td>
                        {{# } else if(stockName == 'mixing'){  }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][mixing]" type="text" value="{{vals.mixing}}" check-type="required number" maxlength="4">
                            </td>
                        {{# } else if(stockName == 'consume'){   }}
                            <td class="form-group">
                                <input name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][equipTypeStockList][{{n}}][consume]" type="text" value="{{vals.consume}}" check-type="required number" range="0.00~9999.99" maxlength="7">
                           </td>
                        {{# } }}

                     {{# } }}
                            <td>
                                {{# if(n>1){ }}
                                <a onclick="delFormula(this)"><span class="glyphicon glyphicon-minus removeFrame_2"></span></a>
                                {{# } }}
                            </td>
                    </tr>
                {{# n++; }) }}
              </tbody>
            </table>
            <div class="setup-choose-sugare">

                {{# if(value.proConfigList.cf_choose_sugar != "1"){ }}
                <div class="form-inline">
                    <label>是否选糖</label> <label><input
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][cf_choose_sugar]" value="0"
                            checked="checked" type="radio"> 否</label> <label><input
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][cf_choose_sugar]" value="1"
                            type="radio"> 是</label>
                </div>
                <div class="form-inline choose-sugar" style="display:none;">
                    <div class="form-group field-equipmentproductgrouplist-half_sugar">
                        <label for="equipmentproductgrouplist-half_sugar">输入半糖出糖量(秒)</label> <input
                            class="form-control"
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][half_sugar]"
                            value="{{value.proConfigList.half_sugar}}" type="text"
                            range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-full_sugar">
                        <label for="equipmentproductgrouplist-full_sugar">输入全糖出糖量(秒)</label> <input
                             class="form-control"
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][full_sugar]"
                            value="{{value.proConfigList.full_sugar}}" type="text"
                            range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-half_sugar_total">
                        <label for="equipmentproductgrouplist-half_sugar_total">半糖出糖总量(克)</label> <input
                                class="form-control"
                                name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][half_sugar_total]"
                                value="{{value.proConfigList.half_sugar_total}}" type="text"
                                range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-full_sugar_total">
                        <label for="equipmentproductgrouplist-full_sugar_total">全糖出糖总量(克)</label> <input
                                class="form-control"
                                name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][full_sugar_total]"
                                value="{{value.proConfigList.full_sugar_total}}" type="text"
                                range="0.00~9999.99" maxlength="7">
                    </div>
                </div>
                {{# }else{ }}
                <div class="form-inline">
                    <label>是否选糖</label> <label><input
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][cf_choose_sugar]" value="0"
                            type="radio"> 否</label> <label><input
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][cf_choose_sugar]" value="1"
                            checked="checked" type="radio"> 是</label>
                </div>
                <div class="form-inline choose-sugar" style="">
                    <div class="form-group field-equipmentproductgrouplist-half_sugar">
                        <label for="equipmentproductgrouplist-half_sugar">输入半糖出糖量(秒)</label> <input
                            class="form-control"
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][half_sugar]"
                            value="{{value.proConfigList.half_sugar}}" type="text" check-type="required number decimal"
                            range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-full_sugar">
                        <label for="equipmentproductgrouplist-full_sugar">输入全糖出糖量(秒)</label> <input
                            name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][full_sugar]"
                            value="{{value.proConfigList.full_sugar}}" type="text" check-type="required number decimal"
                            range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-half_sugar_total">
                        <label for="equipmentproductgrouplist-half_sugar_total">半糖出糖总量(克)</label> <input
                                class="form-control"
                                name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][half_sugar_total]"
                                value="{{value.proConfigList.half_sugar_total}}" type="text" check-type="required number decimal"
                                range="0.00~9999.99" maxlength="7">
                    </div>
                    <div class="form-group field-equipmentproductgrouplist-full_sugar_total">
                        <label for="equipmentproductgrouplist-full_sugar_total">全糖出糖总量(克)</label> <input
                                 class="form-control"
                                name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][full_sugar_total]"
                                value="{{value.proConfigList.full_sugar_total}}" type="text" check-type="required number decimal"
                                range="0.00~9999.99" maxlength="7">
                    </div>
                </div>
                {{# } }}
        </div>
        <div class="form-inline flag">
            <div class="form-group">
                <span>冲泡时间上限</span>
                <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][brew_up]" type="text" value="{{value.proConfigList.brew_up}}" check-type="positive" maxlength="4">
            </div>
            <div class="form-group">
                <span>冲泡时间下限</span>
                <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{value.equip_type_id}}][brew_down]" type="text" value="{{value.proConfigList.brew_down}}" check-type="positive compare" maxlength="4">
            </div>
        </div>
        {{#  modify = true;} }}
     {{# }) }}
    {{# if(!modify){}}
    <button class="btn btn-primary btn-sm" type="button" onclick="addFormula(this);">添加单品配方</button>
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                {{# for (var x in d.equipTypeStockList[d.equipTypeId].readableAttribute){}}
                <td>{{d.equipTypeStockList[d.equipTypeId].readableAttribute[x]}}</td>
                 {{# } }}
                <td>操作</td>
            </tr>
            <tr data-length="1">
                {{# for (var stockName in d.equipTypeStockList[d.equipTypeId].readableAttribute){}}
                    {{# if(stockName == 'stock_code'){ }}
                        <td>
                            <select name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][stock_code]" onchange="selectValueChange(this)">
                                {{# for (var x in d.equipTypeStockList[d.equipTypeId].stock){}}
                                    <option value="{{x}}">{{d.equipTypeStockList[d.equipTypeId].stock[x]}}</option>
                                {{# } }}
                            </select>
                        </td>
                    {{# } else if(stockName == 'order_number'){ }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][order_number]" type="text" value="" check-type="required number" maxlength="4">
                        </td>
                    {{# } else if(stockName == 'water'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][water]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                        </td>
                    {{# } else if(stockName == 'delay'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][delay]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                        </td>
                    {{# } else if(stockName == 'volume'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][volume]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                        </td>
                    {{# } else if(stockName == 'stir'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][stir]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                        </td>
                    {{# } else if(stockName == 'blanking'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][blanking]" type="text" value="" check-type="required number" maxlength="4">
                        </td>
                    {{# } else if(stockName == 'mixing'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][mixing]" type="text" value="" check-type="required number" maxlength="4">
                        </td>
                    {{# } else if(stockName == 'consume'){  }}
                        <td class="form-group">
                            <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][1][consume]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                        </td>
                    {{# }   }}
                 {{# } }}
                <td></td>
            </tr>
        </tbody>
        </table>
        <div class="setup-choose-sugare">
            <div class="form-inline">
                <label>是否选糖</label>
                <label><input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][cf_choose_sugar]" value="0" type="radio"> 否</label>
                <label><input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][cf_choose_sugar]" value="1" checked="" type="radio"> 是</label>
            </div>
            <div class="form-inline choose-sugar">
                <div class="form-group field-equipmentproductgrouplist-half_sugar">
                    <label for="equipmentproductgrouplist-half_sugar">输入半糖出糖量(秒)</label>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][half_sugar]" value="" type="text" check-type="required number decimal" range="0.00~9999.99" maxlength="7">
                </div>
                <div class="form-group field-equipmentproductgrouplist-full_sugar">
                    <label for="equipmentproductgrouplist-full_sugar">输入全糖出糖量(秒)</label>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][full_sugar]" value="" type="text" check-type="required number decimal" range="0.00~9999.99" maxlength="7">
                </div>
                <div class="form-group field-equipmentproductgrouplist-half_sugar_total">
                    <label for="equipmentproductgrouplist-half_sugar_total">半糖出糖总量(克)</label>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][half_sugar_total]" value="" type="text" check-type="required number decimal" range="0.00~9999.99" maxlength="7">
                </div>
                <div class="form-group field-equipmentproductgrouplist-full_sugar_total">
                    <label for="equipmentproductgrouplist-full_sugar_total">全糖出糖总量(克)</label>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][full_sugar_total]" value="" type="text" check-type="required number decimal" range="0.00~9999.99" maxlength="7">
                </div>
            </div>
            <div class="form-inline flag">
                <div class="form-group">
                    <span>冲泡时间上限</span>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][brew_up]" type="text" value="" check-type="positive" maxlength="4">
                </div>
                <div class="form-group">
                    <span>冲泡时间下限</span>
                    <input class="form-control" name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][brew_down]" type="text" value="" check-type="positive compare" maxlength="4">
                </div>
            </div>
        {{# } }}
    {{# } else { }}
        <tr data-length="{{d.formulaNum}}">
            {{# for (var stockName in d.equipTypeStockList[d.equipTypeId].readableAttribute){}}
             {{# if(stockName == 'stock_code'){ }}
                <td>
                    <select name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][stock_code]" onchange="selectValueChange(this)">
                        {{# for (var x in d.equipTypeStockList[d.equipTypeId].stock){}}
                           1 <option value="{{x}}">{{d.equipTypeStockList[d.equipTypeId].stock[x]}}</option>
                        {{# } }}
                    </select>
                </td>
            {{# } else if(stockName == 'order_number'){ }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][order_number]" type="text" value="" check-type="required number" maxlength="4">
                </td>
            {{# } else if(stockName == 'water'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][water]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                </td>
            {{# } else if(stockName == 'delay'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][delay]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                </td>
            {{# } else if(stockName == 'volume'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][volume]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                </td>
            {{# } else if(stockName == 'stir'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][stir]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                </td>
            {{# } else if(stockName == 'blanking'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][blanking]" type="text" value="" check-type="required number" maxlength="4">
                </td>
            {{# } else if(stockName == 'mixing'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][mixing]" type="text" value="" check-type="required number" maxlength="4">
                </td>
            {{# } else if(stockName == 'consume'){  }}
                <td class="form-group">
                    <input name="CoffeeProduct[equipTypeRepice][{{d.equipTypeId}}][equipTypeStockList][{{d.formulaNum}}][consume]" type="text" value="" check-type="required number" range="0.00~9999.99" maxlength="7">
                </td>
            {{# }   }}
         {{# } }}
            <td>
                <a onclick="delFormula(this)"><span class="glyphicon glyphicon-minus removeFrame_2"></span></a>
            </td>
        </tr>
    {{# } }}
</script>
