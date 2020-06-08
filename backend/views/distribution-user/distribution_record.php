<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\LinkPager;

$this->title = '配送记录';
$this->params['breadcrumbs'][] = ['label' => '运维人员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="/distribution-user/distribution-record" method="get">
    <div class="form-group form-inline">
        <div class="form-group">
            <label>配送日期</label>
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
                <th>配送日期</th>
                <th>楼宇名称</th> 
                <th>配送的物料</th> 
                <th>配送的数量</th> 
            </tr>
        </thead> 
        <tbody>
            <?php foreach ($recordList as $recordObj) { ?>
            <tr> 
                <td><?php echo $recordObj->create_date; ?></td>
                <td><?php echo $recordObj->build->name; ?></td>
                <?php $specUnit = $recordObj->materialType->spec_unit ? $recordObj->material->weight . $recordObj->materialType->spec_unit : '';?>
                <td><?php echo isset($recordObj->materialType) && isset($recordObj->material) ? $recordObj->materialType->material_type_name . $specUnit : ''; ?></td>
                <td><?php echo $recordObj->number . $recordObj->materialType->unit; ?></td>
            </tr> 
            <?php } ?>
        </tbody> 
    </table>
    <?php echo LinkPager::widget(['pagination' => $pages]); ?>

</div>
