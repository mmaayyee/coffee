<?php

use common\models\Api;
$getBuildTypeList = Api::getBuildTypeList();

?>

<div class="form-inline search">
    <div class="form-group">
        <label>楼宇名称</label>
        <input type="text" name="buildingName" value="<?php echo empty($whereString['buildingName']) ? '' : $whereString['buildingName'] ?>" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <div class="form-group">
        <label>楼宇类型</label>
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
        <label>设备编号</label>
        <input type="text" name="equipmentCode" value="<?php echo empty($whereString['equipmentCode']) ? '' : $whereString['equipmentCode'] ?>" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>
    <button type="button" class="btn btn-primary search"  id="searchResult" onclick="buildingSearch()">检索</button>
</div>