<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\LinkPager;

$this->title = '领料记录';
$this->params['breadcrumbs'][] = ['label' => '运维人员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="/distribution-user/receive-material-record" method="get">
    <div class="form-group form-inline">
        <div class="form-group">
            <label>运维日期</label>
            <?php 
            echo DatePicker::widget([
                'name' => 'startDate', 
                'value' => $startDate,
                'options' => ['placeholder' => '开始查询日期', 'class'=>'form-control'],
                'dateFormat' => 'yyyy-MM-dd',
            ]).' 至 ' . DatePicker::widget([
                'name' => 'endDate', 
                'value' => $endDate,
                'options' => ['placeholder' => '结束查询日期', 'class'=>'form-control'],
                'dateFormat' => 'yyyy-MM-dd',
            ]);
            ?>
        </div>
        <div class="form-group">
            <?= Html::hiddenInput('author',$author) ?>
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    </form>
    <p>
        <?= Html::a('返回上一页', '/distribution-user/view?id='.$author, ['class' => 'btn btn-primary']) ?>
    </p>
    <table class="table table-bordered"> 
        <thead> 
            <tr> 
                <th>时间</th>
                <th>物料名称</th> 
                <th>物料数量</th> 
                <th>分库</th> 
            </tr>
        </thead> 
        <tbody>
            <?php foreach ($recordList as $recordObj) { ?>
            <tr> 
                <td><?php echo $recordObj->date; ?></td>
                <td><?php echo isset($recordObj->material->materialType->material_type_name) ? $recordObj->material->materialType->material_type_name.$recordObj->material->weight.$recordObj->material->materialType->spec_unit : ''; ?></td>
                <td><?php echo $recordObj->material_out_num . $recordObj->material->materialType->unit; ?></td>
                <td><?php echo isset($recordObj->warehouse->name) ? $recordObj->warehouse->name : '';?></td>
            </tr> 
            <?php } ?>
        </tbody> 
    </table>
    <?php echo LinkPager::widget(['pagination' => $pages]); ?>

</div>
