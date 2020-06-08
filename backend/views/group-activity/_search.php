<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="group-activity-search">
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-left">
            <?php
if (Yii::$app->user->can('拼团活动设置展示')) {
    echo '<li><a href="set" id="view_qun"><span class="glyphicon glyphicon-cog"></span> 活动设置</a></li>';
}
if (Yii::$app->user->can('拼团活动添加/编辑')) {
    echo '<li><a href="save" id="view_qun"><span class="glyphicon glyphicon-plus"></span> 添加活动</a></li>';
}
if (Yii::$app->user->can('拼团活动线上排序')) {
    echo '<li><a href="sort" id="view_qun"><span class="glyphicon glyphicon-sort-by-attributes"></span> 上线排序</a></li>';
}
if (Yii::$app->user->can('拼团活动统计')) {
    echo '<li><a href="statistics" id="view_qun"><span class="glyphicon glyphicon-tasks"></span> 数据统计</a></li>';
}
if (Yii::$app->user->can('拼团活动客服查询')) {
    echo '<li><a href="/group-begin-team/index" id="view_qun"><span class="glyphicon glyphicon-search"></span> 客服查询</a></li>';
}
?>
        </ul>
    </div>

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
<div class="form-group  form-inline">
    <?php echo $form->field($model, 'main_title') ?>
    <?php echo $form->field($model, 'type')->dropDownList($model->dropDown('type')); ?>
    <?php echo $form->field($model, 'status')->dropDownList($model->dropDown('status')); ?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary']);?>
    </div>
    <?php ActiveForm::end();?>
</nav>
</div>



