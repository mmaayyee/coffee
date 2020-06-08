<?php

use backend\models\Organization;
use common\models\Api;
use common\models\CoffeeBackApi;
// 获取设备类型列表
$getEquipTypeList = Api::getEquipTypeList();
// 获取机构列表
$getOrgIdNameList = CoffeeBackApi::getOrganizationIdName();
// 获取机构范围
$orgRange = Organization::getOrgRange();
// 获取场景列表
$getBuildTypeList = Api::getBuildTypeList();

?>

<div class="form-inline search">
    <div class="form-group">
        <label>点位名称</label>
        <input type="text" name="buildingName" value="<?php echo empty($whereString['buildingName']) ? '' : $whereString['buildingName'] ?>" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <div class="form-group">
        <label>点位类型</label>
        <select name="buildingType" class="form-control">
            <?php foreach ($getBuildTypeList as $key => $value) {?>
            <?php if (!empty($whereString['buildingType']) && $key == $whereString['buildingType']): ?>
                <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
            <?php else: ?>
                <option value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php endif?>
            <?php }?>
        </select>
    </div>
    <div class="form-group">
        <label>分公司</label>
        <select name="branch" class="form-control">
        <?php foreach ($getOrgIdNameList as $key => $value) {?>
            <?php if (!empty($whereString['branch']) && $key == $whereString['branch']): ?>
            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value ?></option>
            <?php else: ?>
            <option value="<?php echo $key; ?>"><?php echo $value ?></option>
            <?php endif?>
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

    <div class="form-group">
        <label>设备类型</label>
        <select name="equipmentType" id="equip_type" class="form-control">
            <?php foreach ($getEquipTypeList as $key => $value) {?>
            <?php if (!empty($whereString['equipmentType']) && $key == $whereString['equipmentType']): ?>
            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
            <?php else: ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php endif?>
            <?php }?>
        </select>
    </div>
    <div class="form-group">
        <label>设备编号</label>
        <input type="text" name="equipmentCode" value="<?php echo empty($whereString['equipmentCode']) ? '' : $whereString['equipmentCode'] ?>" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <button type="button" class="btn btn-primary search"  id="searchResult">检索</button>
</div>