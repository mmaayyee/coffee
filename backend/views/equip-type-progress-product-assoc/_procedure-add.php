<?php

use backend\assets\AppAsset;
$this->registerCssFile("/css/jquery.minicolors.css?v=1.0", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/jquery.minicolors.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/procedure_add.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
?>
<style type="text/css">
    .procedure{
        display: inline-block;
    }
    table .form-control.color{
        background-color: transparent;
        border: none;
        box-shadow: none;
        cursor: default;
    }
    .infos .table-bordered > tbody > tr > td {
        vertical-align: middle;
    }
</style>
<div id="progress">
    <div class="form-inline">
        <button class="btn btn-info add-procedure" type="button">添加工序</button>
        <div class="procedure"></div>
    </div>
    <div class="infos">
        <h5>已有工序信息</h5>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>工序名称</th>
                    <th>工序英文名称</th>
                    <th>色块</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>研磨咖啡豆</td>
                    <td>Grinding</td>
                    <td><input type="input" class="form-control color"  value="#ff8a00" data-control="hue" readonly="readonly" disabled="disabled"/></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script id="procedureTpl" type="text/html">
    <div class="form-group">
        <input class="form-control" name="name" value="" placeholder="请输入工序名称" check-type="required byteLength" maxlength="12" type="text">
    </div>
    <div class="form-group">
        <input class="form-control" name="english" value="" placeholder="请输入工序英文名称" check-type="required english" maxlength="20" type="text">
    </div>
    <div class="form-group">
       <input type="input" class="form-control color" name="color" placeholder="请选择色块" check-type="required" maxlength="10" data-control="hue"/>
    </div>
    <button class="btn btn-success" type="button" onclick="addProcedure()">保存</button>
</script>
<script id="procedureInfoTpl" type="text/html">
    <tr>
        <td>研磨咖啡豆</td>
        <td>Grinding</td>
        <td><input type="input" class="form-control color"  value="#ff8a00" data-control="hue" readonly="readonly" disabled="disabled"/></td>
        <td></td>
    </tr>
</script>