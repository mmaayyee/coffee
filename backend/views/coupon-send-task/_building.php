<?php

use common\models\Api;
use common\models\CoffeeBackApi;

$this->registerJsFile("/js/add-building.js?v=1.1", ["depends" => [\yii\web\JqueryAsset::className()]]);
?>

<div class="form-inline search">
    <div class="form-group">
        <label>楼宇名称</label>
        <input type="text" name="buildingName" value="<?php echo $model['buildingName']; ?>" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <div class="form-group">
        <label>选择场景</label>
        <select name="buildingType" class="form-control">
            <?php foreach (Api::getBuildTypeList() as $buildTypeID => $buildTypeName): ?>
                <?php if ($model['buildingType'] == $buildTypeID): ?>
                    <option selected="selected" value="<?php echo $buildTypeID; ?>"><?php echo $buildTypeName; ?></option>
                <?php else: ?>
                    <option value="<?php echo $buildTypeID; ?>"><?php echo $buildTypeName; ?></option>
                <?php endif?>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group">
        <label>分公司</label>
        <select name="branch" class="form-control">
            <option value="">请选择</option>
            <?php foreach (CoffeeBackApi::getOrganizationIdName(['organization_type' => 0]) as $orgID => $orgName): ?>
                <?php if ($model['branch'] == $orgID): ?>
                    <option selected="selected" value="<?php echo $orgID; ?>"><?php echo $orgName; ?></option>
                <?php else: ?>
                <option value="<?php echo $orgID ?>"><?php echo $orgName ?></option>
                <?php endif?>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group">
        <label>代理商</label>
        <select name="agent" class="form-control">
            <option value="">请选择</option>
            <?php foreach (CoffeeBackApi::getOrganizationIdName(['organization_type' => 1]) as $orgID => $orgName): ?>
                <?php if ($model['agent'] == $orgID): ?>
                    <option selected="selected" value="<?php echo $orgID; ?>"><?php echo $orgName; ?></option>
                <?php else: ?>
                    <option value="<?php echo $orgID ?>"><?php echo $orgName ?></option>
                <?php endif?>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group">
        <label>设备类型</label>
        <select name="equipmentType" class="form-control">
            <?php foreach (Api::getEquipTypeList() as $equipTypeID => $equipTypeName): ?>
                <?php if ($model['equipmentType'] == $equipTypeID): ?>
                    <option selected="selected" value="<?php echo $equipTypeID; ?>"><?php echo $equipTypeName; ?></option>
                <?php else: ?>
                    <option value="<?php echo $equipTypeID; ?>"><?php echo $equipTypeName; ?></option>
                <?php endif?>
            <?php endforeach?>
        </select>
    </div>
    <button type="button" class="btn btn-primary search"  id="searchResult">检索</button>
</div>
<div class="block-a">
    <div class="searchResult">
        <h5>搜索结果</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>楼宇名称</th>
                    <th><button class="btn btn-primary" id="batchAdd" type="button">批量添加</button></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pages"></div>
        <button class="btn btn-info" id="addAll" type="button">全部添加</button>
    </div>
    <div class="text-center no-data">暂无数据</div>
    <div class="addPreview">
        <h5>添加楼宇预览</h5>
        <div class="overflow">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>楼宇名称</th>
                        <th><button type="button" class="btn btn-primary allDelete" disabled="disabled">批量移除</button></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<!--楼宇模板-->
<script id="building_add_template" type="text/html">
    {{#
    $.each(d,function(key,value){ }}
        <tr>
            <td class="SortId" data-text=""></td>
            <td>{{value.name}}</td>
            <td>
                <input type="hidden" value="{{value.id}}" disabled="disabled" />
                <button type="button" class="btn btn-primary btn-sm add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </td>
        </tr>
    {{# })}}
</script>

<script id="building_del_template" type="text/html">
    {{#
    $.each(d,function(key,value){ }}
        <tr>
            <td class="SortId" data-text=""></td>
            <td>{{value.name}}</td>
            <td>
                <input type="hidden" name="buildingIdArr[]" value="{{value.id}}"/>
                <button type="button" class="btn btn-primary btn-sm add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </td>
        </tr>
    {{# })}}
</script>
