<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserTag */

$this->title                   = '添加备注: ' . ' ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => '黑白名单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '添加备注';
?>
<div class="user-tag-update">

    <h1><?=Html::encode($this->title)?></h1>


<div class="user-tag-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'black_white_list_remarks')->textArea(['maxlenth' => 100])?>

    <?=$form->field($model, 'user_id')->hiddenInput()->label(false)?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>


</div>
