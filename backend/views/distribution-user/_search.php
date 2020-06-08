<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\WxMember;
use common\models\Building;
use kartik\select2\Select2;
use backend\models\Manager;
use backend\models\Organization;
use backend\models\DistributionUser;
$organization = Organization::getBranchArray();
unset($organization[1]);
$org_id = Manager::getManagerBranchID();

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>运维员</label>
            <div class="select2-search">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'userid',
                    'data' => WxMember::getDistributionUserArr(3),
                    'options' => ['multiple' => false, 'placeholder' => '请选择运维人员'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
            </div>
        </div>
        <div class="form-group">
            <label>楼宇</label>
            <div class="select2-search">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'build_id',
                    'data' => Building::getOrgBuild(['build_status' => Building::SERVED]),
                    'options' => ['multiple' => false, 'placeholder' => '请选择楼宇'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
            </div>
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="select2-search">
                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'user_status',
                    'data' => DistributionUser::$user_status,
                    'options' => ['multiple' => false, 'placeholder' => '请选择状态'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <?php if($org_id==1){ ?>
        <div class="form-group">
            <label>分公司</label>
            <div class="select2-search">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'orgId',
                    'data' => $organization,
                    'options' => ['multiple' => false, 'placeholder' => '请选择分公司'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            ?>
            </div>
        </div>
        <?php } ?>
        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
