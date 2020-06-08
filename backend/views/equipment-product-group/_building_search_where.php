<?php

use backend\models\Organization;
use common\models\Api;
use common\models\CoffeeBackApi;
// 获取机构列表
$getOrgIdNameList = CoffeeBackApi::getOrganizationIdName();
// 获取机构范围
$orgRange = Organization::getOrgRange();
// 获取场景列表
$getBuildTypeList = Api::getBuildTypeList();
?>

<div class="form-inline search">
    <div class="form-group">
        <label>楼宇名称</label>
        <input type="text" name="buildingName" value="" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <div class="form-group">
        <label>楼宇类型</label>
        <select name="buildingType" class="form-control">
            <?php foreach ($getBuildTypeList as $key => $value) {?>
                <option value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php }?>
        </select>
    </div>
    <div class="form-group">
        <label>分公司</label>
        <select name="branch" class="form-control" onchange="changeBranch(this);">
        <?php foreach ($getOrgIdNameList as $key => $value) {?>
            <option value="<?php echo $key; ?>"><?php echo $value ?></option>
        <?php }?>
        </select>
    </div>
    <div class="form-group">
        <label>机构类型</label>
        <select name="orgRange" class="form-control">
        <?php foreach ($orgRange as $key => $value) {?>
            <?php if (!empty($whereString['orgRange']) && $key == $whereString['orgRange']): ?>
            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value ?></option>
            <?php else: ?>
            <option value="<?php echo $key; ?>"><?php echo $value ?></option>
            <?php endif?>
        <?php }?>
        </select>
    </div>
    <button type="button" class="btn btn-primary search"  id="searchResult">检索</button>
</div>