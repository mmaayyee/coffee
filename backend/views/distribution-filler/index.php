<?php
use backend\models\ScmMaterialType;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionDailyTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物料消耗预测';
$this->registerJsFile('https://code.highcharts.com/highcharts.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    $("#container").height($(window).height()-200);
    function getData(materialTypeId, taici) {
        $.get(
            "/distribution-filler/material-consume",
            {materialTypeId:materialTypeId, taici:taici},
            function(data){
                Highcharts.chart("container", {
                    title: {
                        text: "物料消耗预测",
                        x: -20 //center
                    },
                    xAxis: {
                        categories: ["' . date('Y-m', strtotime('-3 month')) . '","' . date('Y-m', strtotime('-2 month')) . '","' . date('Y-m', strtotime('-1 month')) . '","' . date('Y-m') . '(预测)"],
                    },
                    yAxis: {
                        title: {
                            text: "消耗值"
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: "#808080"
                        }]
                    },
                    legend: {
                        layout: "vertical",
                        align: "right",
                        verticalAlign: "middle",
                        borderWidth: 0
                    },
                    series: [{
                        name: "消耗值",
                        data: data.materialConsume,
                        tooltip: {
                            pointFormatter: function() {
                                return "台次: <b>"+data.taici[this.x]+"台</b><br/>"+this.series.name + ": <b>"+this.y+"kg</b><br/>"
                            },
                        },
                    }]
                });
            },
            "json"
        )
    }
    getData(1,0);
    $("#prediction").click(function(){
        getData($("#material").val(), $("#taici").val());
    });


')
?>
<div class="form-group form-inline">
    <!-- <form action="/distribution-filler/index" method="get"> -->
    <div class="form-group">
        <label class="control-label">请选择物料</label>
        <?=Html::dropDownList('materialTypeId', '', ScmMaterialType::getIdNameArr(2), ['class' => 'form-control', 'id' => 'material'])?>
    </div>
    <div class="form-group">
        <label class="control-label">请输入台次</label>
        <?=Html::textInput('taici', '', ['class' => 'form-control', 'id' => 'taici'])?>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" id="prediction">预测</button>
    </div>
    <!-- </form> -->
</div>
<div id="container"></div>