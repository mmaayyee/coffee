<?php
use backend\models\ScmMaterialType;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionDailyTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物料消耗预测';
$this->registerJsFile('@web/js/echarts.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    $("#container").height($(window).height()-200);

    var option = {
        title: {
            text: "物料消耗预测"
        },
        tooltip : {
            trigger: "axis"
        },
        legend: {
            data:["台次", "物料消耗值"]
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: "0%",
            right: "2%",
            bottom: "0%",
            containLabel: true
        },
        xAxis : [
            {
                type : "category",
                boundaryGap : false,
                data : ["' . date('Y-m', strtotime('-3 month')) . '","' . date('Y-m', strtotime('-2 month')) . '","' . date('Y-m', strtotime('-1 month')) . '","' . date('Y-m') . '"]
            }
        ],
        yAxis : [
            {
                type : "value"
            }
        ],
        series : [
            {
                name:"台次",
                type:"line",
                stack: "总量",
                areaStyle: {normal: {}},
                data:[120, 132, 101, 134]
            },
            {
                name:"物料消耗值",
                type:"line",
                stack: "总量",
                areaStyle: {normal: {}},
                data:[120, 132, 101, 134]
            }
        ]
    }
    var dom = document.getElementById("container");
    var myChart = echarts.init(dom);
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }

')
?>
<div class="form-group form-inline">
    <div class="form-group">
        <label class="control-label">请选择物料</label>
        <?=Html::dropDownList('material', '', ScmMaterialType::getIdNameArr(2), ['class' => 'form-control'])?>
    </div>
    <div class="form-group">
        <label class="control-label">请输入台次</label>
        <?=Html::textInput('taici', '', ['class' => 'form-control'])?>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">预测</button>
    </div>
</div>
<div id="container"></div>

