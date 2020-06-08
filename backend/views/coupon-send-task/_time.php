<?php

$this->registerJsFile("/js/My97DatePicker/WdatePicker.js", ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/add-condition.js?v=1.0", ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="condition">

</div>
<?=$this->render('_tip.php');?>
<script id="conditionTemplat" type="text/html">
   {{# $.each(d,function(key,value){ }}
     <div class="groups">
        <div class="form-inline">
            <div class="form-group">
                <label>选择时间单位</label>
                <select class="form-control timeUnit" name="date_type[]" onChange="isTime($(this))">
                <?php foreach ($model->dateType as $dateTypeID => $dateType): ?>
                    {{# if(value.date_type == "<?php echo $dateTypeID; ?>") { }}
                        <option selected="selected" value="<?php echo $dateTypeID ?>"><?php echo $dateType ?></option>
                    {{# } else { }}
                        <option value="<?php echo $dateTypeID ?>"><?php echo $dateType ?></option>
                    {{# } }}
                <?php endforeach?>
                </select>
            </div>
            <div class="form-group time">
                <label>选择时间</label>
                <input class="form-control" value="{{value.date}}" id="dayTime" type="text" readonly="readonly" onclick="createWdatePicker(this);" />
                <input class="form-control" value="{{value.date}}" id="weekTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="{{value.date}}" id="TenTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="{{value.date}}" id="monthTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="{{value.date}}" id="seasonTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="{{value.date}}" id="halfYearTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="{{value.date}}" id="YearTime" type="text" readonly="readonly" onclick="createWdatePicker(this);" />
                <input type="hidden" name="date[]" id="time" value="{{value.date}}" />
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label>消费金额</label>
                <input class="form-control" type="text" maxlength="9" range="0~100000" check-type="number" name="pay_amount[]" onchange="valueCompare(this)" value="{{value.pay_amount[0]}}"/>
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="9" range="0~100000" check-type="number compare" name="pay_amount[]"  value="{{value.pay_amount[1]}}"/>
            </div>
            <div class="form-group">
                <label>消费频次</label>
                <input class="form-control" type="text" maxlength="3" range="0~500" check-type="number ints" name="pay_number[]" onchange="valueCompare(this)" value="{{value.pay_number[0]}}"/>
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="3" range="0~500" check-type="number ints compare" name="pay_number[]" value="{{value.pay_number[1]}}" />
            </div>
            <div class="form-group">
                <label>杯均价</label>
                <input class="form-control" type="text" maxlength="6" range="0~500" check-type="number" name="average_price[]" onchange="valueCompare(this)" value="{{value.average_price[0]}}" />
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="6" range="0~500" check-type="number compare" name="average_price[]" value="{{value.average_price[1]}}" />
            </div>
            {{# if(key == 0){ }}
            <button type="button" class="btn btn-success add-condition" onClick="addCondition()">添加条件</button>
            {{# } else { }}
            <button type="button" class="btn btn-danger" onClick="delCondition(this)">删除条件</button>
            {{# } }}
        </div>
    </div>
    {{# })}}
</script>
<script id="addConditionTemplat" type="text/html">
     <div class="groups">
        <div class="form-inline">
            <div class="form-group">
                <label>选择时间单位</label>
                <select class="form-control timeUnit" name="date_type[]" onChange="isTime($(this))">
                <?php foreach ($model->dateType as $dateTypeID => $dateType): ?>
                    <option value="<?php echo $dateTypeID ?>"><?php echo $dateType ?></option>
                <?php endforeach?>
                </select>
            </div>
            <div class="form-group time">
                <label>选择时间</label>
                <input class="form-control" value="" id="dayTime" type="text"  readonly="readonly" onclick="createWdatePicker(this);" />
                <input class="form-control" value="" id="weekTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="" id="TenTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="" id="monthTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="" id="seasonTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="" id="halfYearTime" type="text" readonly="readonly" onclick="createWdatePicker(this);"/>
                <input class="form-control" value="" id="YearTime" type="text" readonly="readonly" onclick="createWdatePicker(this);" />
                <input type="hidden" name="date[]" id="time" value="" />
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label>消费金额</label>
                <input class="form-control" type="text" maxlength="9" range="0~100000" check-type="number" name="pay_amount[]" onchange="valueCompare(this)" value=""/>
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="9" range="0~100000" check-type="numbe compare" name="pay_amount[]"  value=""/>
            </div>
            <div class="form-group">
                <label>消费频次</label>
                <input class="form-control" type="text" maxlength="3" range="0~500" check-type="number ints" name="pay_number[]" onchange="valueCompare(this)" value=""/>
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="3" range="0~500" check-type="number ints compare" name="pay_number[]" value="" />
            </div>
            <div class="form-group">
                <label>杯均价</label>
                <input class="form-control" type="text" maxlength="6" range="0~500" check-type="number" name="average_price[]" onchange="valueCompare(this)" value="" />
            </div>
            <div class="form-group">
                <span>→</span>
                <input class="form-control" type="text" maxlength="6" range="0~500" check-type="number compare" name="average_price[]" value="" />
            </div>
            <button type="button" class="btn btn-success add-condition" onClick="addCondition()">添加条件</button>
        </div>
    </div>
</script>