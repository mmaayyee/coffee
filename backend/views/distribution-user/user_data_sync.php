<?php

use yii\helpers\Html;
use yii\jui\DatePicker;

$this->title = '个人数据统计';
$this->params['breadcrumbs'][] = ['label' => '运维人员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="/distribution-user/user-data-sync" method="get">
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

    <dl>
        <dt>总工作时长</dt>
        <dd><?php echo $data['workTimeStr']; ?></dd>
    </dl>
     <dl>
        <dt>总配送时长</dt>
        <dd><?php echo $data['distributionTimeStr']; ?></dd>
    </dl>
    <dl>
        <dt>总维修时长</dt>
        <dd><?php echo $data['repairTimeStr']; ?></dd>
    </dl>
    <dl>
        <dt>总台次</dt>
        <dd><?php echo $data['taiCi']; ?></dd>
    </dl>
    <dl>
        <dt>领料数</dt>
        <dd>
            <?php if ($data['material']) foreach ($data['material'] as $materialTypeName => $materialList) {
                foreach ($materialList as $materialId => $materialArr) {
                echo "<p>".$materialTypeName." ".$materialArr['content'].$materialArr['packets'].$materialArr['unit']."</p>";
            }} ?>
        </dd>
    </dl>
    

</div>
