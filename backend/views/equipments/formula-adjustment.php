<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Equipments */
$this->title                   = '本机配方调整';
$this->params['breadcrumbs'][] = ['label' => '本机配方调整', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="formula-wrap">
<div class="formula-adjustment-view">
    <p>
         <?=Html::a('配方调整', ['equipments/formula-adjustment', 'equipTypeId' => $equipTypeId, 'equip_code' => $equipCode], ['class' => 'btn btn-default']);?>
         <?=Html::a('配方调整修改日志', ['equipments/formula-adjustment-log', 'equipTypeId' => $equipTypeId, 'equip_code' => $equipCode], ['class' => 'btn btn-default']);?>
    </p>
 <?php $form = ActiveForm::begin();?>
<?php
//只能输入或粘贴整数
$onkeyup      = "this.value=this.value.replace(/\D/g,'')";
$onafterpaste = "this.value=this.value.replace(/\D/g,'')";
echo '<p><span style="color:#cccccc">校正后的料仓出料时间范围必须在1~25.5之间</span></p>';
echo '<p><span class="lcang">料仓</span><span class="percent">百分比（100%）</span></p>';
foreach ($formulaList as $formula) {
    $stockCode = $formula['stock_code'];
    $value     = $formula['value'];
    echo '<p style="width:300px"><label class="control-label">' . $stockCode . '号料仓</label><input onkeyup="' . $onkeyup . '" onafterpaste="' . $onafterpaste . '" type="text"  class="form-control" width="100px" size="100" name="formula[' . $stockCode . ']" value="' . $value . '" ></p>';
}

?>
    <?=Html::hiddenInput('equipment_code', $equipCode)?>
    <div class="form-group">
        <?=Html::submitButton('确认修改', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>
</div>
<div style="clear:both"></div>
</div>
<style>
    .form-control{
        width:100px;
    }
    .formula-wrap{
        width:80%;
        margin:0 auto;
    }
    .formula-adjustment-view{
        width:30%;
        float:left;
    }
    .formula-adjustment-view label{
        height: 34px;
        line-height: 34px;
        float:left;
    }
    .formula-adjustment-view input{
        display: inline-block;
        width:150px;
        margin-left:20px;
    }
    .formula-adjustment-view  p span,.log h4{
        display: inline-block;
        font-weight: bold;
        font-size:16px;
        width:180px;
        text-align: center;
    }
    .formula-adjustment-view  p span.lcang{
        width:50px;
    }
    .log{
        width:40%;
        text-align: center;
        float:left;
        margin-left:10%;
    }
    .log h4{
        width:100%;
        text-align: center;
    }
    .log-list{
        height: 380px;
        overflow:hidden;
        border:1px solid #ccc;
        border-radius: 5px;
        padding:10px 0;
    }
    .log-list-cont{
        width:106%;
        height: 360px;
        max-height: 360px;
        overflow: hidden;
        overflow-y:scroll;
    }
    .log-list-cont h5{
        font-size:14px;
        font-weight: bold;
    }
    .btn-success{
        margin:20px 0 0 80px;
    }
</style>
