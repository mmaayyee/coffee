<?php
use backend\assets\AppAsset;
use backend\models\Manager;
use common\models\Api;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingHolidayStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '楼宇节假日运维管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/batch_operation.css?v=1.0", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/laypage.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/building-holiday.js?v=1.8", ["depends" => [\yii\web\JqueryAsset::className()]]);

?>

<div class="form-inline">
    <input type="hidden" id="flag" value="<?php echo $flag; ?>">
    <div class="form-group">
        <label>楼宇名称</label>
        <input type="text" name="buildingName" class="form-control" placeholder="关键字检索" maxlength="50">
    </div>

    <?php $orgId = Manager::getManagerBranchID();?>
    <?php if ($orgId == 1): ?>
    <div class="form-group">
        <label>分公司</label>
        <select name="branch" class="form-control">
            <?php foreach (json_decode(Api::getOrgListByType(0), true) as $key => $value) {?>
                <option value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php }?>
        </select>
    </div>
    <?php endif;?>

    <div class="form-group">
        <label>设备类型</label>
        <select name="equipmentType" class="form-control">
            <?php foreach (Api::getEquipTypeList() as $key => $value) {?>
                <option value="<?php echo $key ?>"><?php echo $value ?></option>
            <?php }?>
        </select>
    </div>

    <div class="form-group">
        <label>渠道</label>
        <select name="buildingType" class="form-control">
            <option value="">请选择</option>
            <?php foreach (json_decode(Api::getBuildType(), true) as $key => $value) {?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['type_name'] ?></option>
            <?php }?>
        </select>
    </div>

    <button class="btn btn-primary search"  id="searchResult">检索</button>
</div>
<div class="block-a">
    <div class="searchResult">
        <h5>搜索结果</h5>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>序号</th>
                <th>楼宇名称</th>
                <th><button class="btn btn-primary allAdd">批量添加</button></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="pages"></div>
    </div>
    <div class="addPreview">
        <h5><?php echo $flag == 'add' ? '节假日不运维' : '节假日运维'; ?></h5>
        <form action="/building-holiday-status/modify-building-status?flag=<?=$flag;?>" method="post">
            <input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
            <div class="overflow">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>楼宇名称</th>
                        <th><button class="btn btn-primary allDelete" type="button" disabled="disabled">批量移除</button></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success" disabled="disabled">保存</button>
        </form>
    </div>
</div>
<div class="text-center no-data">暂无数据</div>
<!--楼宇模板-->
<script id="building_template" type="text/html">
    {{#
    $.each(d,function(key,value){ }}
    <tr>
        <td class="SortId" data-text=""></td>
        <td>{{value.buildingName}}</td>
        <td>
            <input type="hidden" name="buildingIdArr[]" value="{{value.id}}"/>
            <button type="button" class="btn btn-primary btn-sm add">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            </button>
        </td>
    </tr>
    {{# })}}
</script>
