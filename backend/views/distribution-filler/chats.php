<?php
use backend\models\ScmMaterialType;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionDailyTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物料消耗预测';
$this->registerJsFile('@web/js/chart.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    // function getData(materialTypeId, taici) {
        // $.get(
        //     "/distribution-filler/material-consume",
        //     {materialTypeId:materialTypeId, taici:taici},
        //     function(data){
                var ctx = document.getElementById("myChart").getContext("2d");
                var myChart = new Chart(ctx, {
                  type: "line",
                  data: {
                    labels: ["' . date('Y-m', strtotime('-3 month')) . '","' . date('Y-m', strtotime('-2 month')) . '","' . date('Y-m', strtotime('-1 month')) . '","' . date('Y-m') . '"],
                    datasets: [
                        {
                          label: "台次",
                          data: ' . json_encode($taici) . ',
                          backgroundColor: "rgba(153,255,51,0.4)"
                        },
                        {
                          label: "物料消耗值",
                          data: ' . json_encode($materialConsume) . ',
                          backgroundColor: "rgba(255,153,0,0.4)"
                        }
                    ]
                  }
                });
        //     },
        //     "json"
        // )
    // }
    // getData(1,0);

    // $("#prediction").click(function(){
    //     getData($("#material").val(), $("#taici").val());
    // });


')
?>
<div class="form-group form-inline">
    <form action="/distribution-filler/index" method="get">
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
    </form>
</div>
<canvas id="myChart"></canvas>

