<?php

use backend\models\DistributionUser;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\WxMember;
$this->registerJsFile('@web/js/distribut_user.js',['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '添加配送员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'userid')->textInput()?>
    <?= $form->field($model, 'work_date')->checkboxList(DistributionUser::getWorkOnDate()) ?>
    <?= $form->field($model, 'is_leader')->dropDownList(DistributionUser::$is_leader) ?>
    <?= $form->field($model, 'leader_id')->dropDownList(DistributionUser::orgLeaderArr()) ?>
    <?= $form->field($model, 'user_status')->radioList(DistributionUser::$user_status) ?>
    <div class="form-group">
        <?= Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
