<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model backend\models\WxTag */

$this->title = '添加标签用户';
$this->params['breadcrumbs'][] = ['label' => '标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-tag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="wx-tag-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'wx_memberid')->checkboxList(\common\models\WxMember::getMembers($tagid)) ?>
        <?= Html::hiddenInput('WxMemberTagAssoc[wx_tagid]',$tagid) ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
