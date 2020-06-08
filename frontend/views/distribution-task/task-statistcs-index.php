<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionUser */
$this->title = "配送数据统计";
$this->registerJsFile('@web/js/mobiscroll_date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/validate_time.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/css/mobiscroll_date.css');
$this->registerJs('
		var currYear = (new Date()).getFullYear();
			var opt={};
			opt.date = {preset : "date"};
			opt.datetime = {preset : "datetime"};
			opt.time = {preset : "time"};
			opt.default = {
				theme: "android-ics light", //皮肤样式
		        display: "modal", //显示方式
		        mode: "scroller", //日期选择模式
				dateFormat: "yyyy-mm-dd",
				lang: "zh",
				showNow: true,
				nowText: "今天",
		        startYear: currYear - 10, //开始年份
		        endYear: currYear + 10 //结束年份
			};
	$("#startDate, #endDate").mobiscroll($.extend(opt["date"], opt["default"]));
	var requestDate1 = $("#startDate").val();
	var requestDate2 = $("#endDate").val();
    	if(requestDate1 != ""){
			requestDate1 = new Date(requestDate1);
			$("#startDate").scroller("setDate", requestDate1, true);
   		}
   		if(requestDate2 != ""){
			requestDate2 = new Date(requestDate2);
			$("#endDate").scroller("setDate", requestDate2, true);
   		}
');
?>

<style>
.form-group,dl {
    margin-bottom: 10px;
}
#startDate,#endDate{
	display: inline-block;
	width:60%;
	margin-top:2% ;
}
.dl-horizontal dt {
    width: 90px;
    text-align: left;
    text-overflow:clip;
    white-space:normal;
}
.dl-horizontal dd {
    margin-left: 95px;
}
p {
    margin: 0px;
}
input[name="endDate"]{
	margin-left:1.5% ;
}
.error{
	margin-top: 2%;
	color:red;
	display: none;
}
</style>
<div class="distribution-user-view">

    <form action="/distribution-task/user-data-sync" method="get">

    <div class="form-group form-inline">
        <p><label>本日已完成台数：</label><label><?php echo $taskDateCount; ?></label>台</p>
    </div>
    <div class="form-group form-inline">
        <p><label>本月已完成台数：</label><label><?php echo $taskMonthCount; ?></label>台</p>
    </div>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>配送开始日期：</label>
            <input type="text" name="startDate" id="startDate" value="<?php echo $startDate; ?>" class="form-control"/>
        </div>
        <div class="form-group">
            <label>配送结束日期：</label><input type="text" name="endDate" id="endDate" value="<?php echo $endDate; ?>" class="form-control"/>
            <p class="error"></p>
        </div>
        <div class="form-group">
            <?=Html::hiddenInput('author', $author)?>
            <?=Html::submitButton('检索', ['class' => 'btn btn-block btn-primary'])?>
        </div>
    </div>
    </form>

    <dl class="dl-horizontal">
        <dt>总工作时长：</dt>
        <dd><?php echo $data['workTimeStr']; ?></dd>
    </dl>
     <dl class="dl-horizontal">
        <dt>总配送时长：</dt>
        <dd><?php echo $data['distributionTimeStr']; ?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>总维修时长：</dt>
        <dd><?php echo $data['repairTimeStr']; ?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>总台次：</dt>
        <dd><?php echo $data['taiCi']; ?></dd>
    </dl>
    <dl>
        <dt>领料数:</dt>
        <dd>
            <?php if ($data['material']) {
    foreach ($data['material'] as $materialTypeName => $materialList) {
        foreach ($materialList as $materialId => $materialArr) {
            echo "<p>" . $materialTypeName . " " . $materialArr['content'] . $materialArr['packets'] . $materialArr['unit'] . "</p>";
        }}
}
?>
        </dd>
    </dl>


</div>