<?php

use backend\models\Organization;
use common\models\Api;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProgram */

$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '灯带方案管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    var data = ' . $programList . ';
    var gettpls = document.getElementById("program_template").innerHTML;
    laytpl(gettpls).render(data,function(html){
        $(".program_view tbody").append(html);
    });
');

?>
<style type="text/css">
    tbody {
        counter-reset:sectioncounter;
    }
    .SortId:before {
       content:counter(sectioncounter);
       counter-increment:sectioncounter;
    }
</style>
<table class="table table-bordered program_view">
    <thead>
        <tr>
            <th>灯带方案名称</th>
            <th>灯带场景</th>
            <th>灯带场景时间</th>
            <th>灯带策略</th>
            <th>包含的饮品组</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script id="program_template" type="text/html">
	{{# var length = d.scenarioArr.length+1}}
    <tr>
        <td rowspan="{{length}}">{{d.program_name}}</td>
    </tr>
    {{# $.each(d.scenarioArr,function(index,item){ }}
    <tr>
        <td>{{item.scenario_name}}</td>
        <td>{{item.start_time}}--{{item.end_time}}</td>
        <td>{{item.strategy_name}}</td>
        <td>{{item.product_group_name}}</td>
    </tr>
    {{# }) }}
</script>


<!-- 搜索条件 -->
<div class="light-belt-program-search">

    <?php $form = ActiveForm::begin([
    'action' => ['view', 'id' => $id],
    'method' => 'get',
]);?>
    <div class="form-inline form-group">
        <?=$form->field($model, 'buildName')->label("楼宇名称")?>
        <?=$form->field($model, 'equipType')->dropDownList(Api::getEquipTypeList())->label("设备类型")?>
        <?=$form->field($model, 'branch')->dropDownList(Organization::getManagerOrgIdNameArr())->label("分公司")?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>
</div>

<div class="light-belt-program">
    <table class="table table-bordered">
        <tr>
            <td>序号</td>
            <td>楼宇名称</td>
        </tr>
        <?php if (isset($buildList) && $buildList) {?>
        <?php foreach ($buildList as $key => $build) {?>
            <tr>
                <td><?php echo ($page - 1) * $pageSize + $key + 1 ?></td>
                <td><?php echo $build['build_name'] ?></td>
            </tr>
        <?php }}?>
    </table>
    <?php if (!isset($buildList) || !$buildList) {?>
        <div style="margin-left: 50%; ">暂无数据。</div>
    <?php }?>
    <?=
LinkPager::widget([
    'pagination' => $pages,
]);
?>
</div>