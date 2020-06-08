<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27
 * Time: 0:10
 */
use backend\models\TemporaryAuthorization;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

$this->registerJsFile("@web/js/temporary-authorization.js", ["depends" => [JqueryAsset::className()]]);

?>
<div class="temporary-authorization-search">
    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'temporaryAuthorizationForm',
]);?>
    <div class="form-group form-inline">
        <?=$form->field($model, 'build_name')?>
        <div class="form-group">
            <label>所属分公司</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'orgId',
    'data'          => \backend\models\Organization::getOrganizationList(),
    'options'       => ['placeholder' => '分公司'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'orgType')->dropDownList(\common\models\Equipments::$orgType)?>
        <?=$form->field($model, 'wx_member_name')?>
         <?=$form->field($model, 'state')->dropDownList(TemporaryAuthorization::$state)?>
        <div class="form-group">
            <?=Html::Button('搜索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
            <?php if (Yii::$app->user->can('导出门禁卡临时开门记录')) {?>
        <?=Html::Button('导出', ['class' => 'btn btn-primary', 'id' => 'export'])?>
    <?php }?>
        </div>
    </div>
    <?php ActiveForm::end();?>
</div>
