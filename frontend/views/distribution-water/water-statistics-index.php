<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = '水单统计';

$this->registerJsFile('@web/js/mobiscroll_date.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/validate_time.js',['depends' => [\yii\web\JqueryAsset::className()]]); 
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
    $("#distributionwater-starttime, #distributionwater-endtime").mobiscroll($.extend(opt["date"], opt["default"]));
      
');
?>

    <?php $form = ActiveForm::begin(['action' => [''], 'method' => '']);?>
    <div class="form-group form-inline">
        <?= $form->field($model, 'startTime')->textInput() ?>
	</div>

	<div class="form-group form-inline">
        <?= $form->field($model, 'endTime')->textInput() ?>
    </div>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
	<div class="water-statistic">
	
	</div>
<?php
	
	$this->registerJs('
        $(".btn-primary").click(function(){
        	var startTime 	=	$("#distributionwater-starttime").val();
        	var endTime		=	$("#distributionwater-endtime").val()+" 23:59:59";
            $.get(
                "/distribution-water/water-statistics-search",
                {startTime:startTime, endTime:endTime},
                function(data) {
                    console.log("返回的data数据："+data);
                    $(".water-statistic").html(data);
                }
            )

        })

    ');


?>
