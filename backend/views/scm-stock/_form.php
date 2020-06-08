<?php

use backend\models\ScmSupplier;
use backend\models\ScmWarehouse;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmStock */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.8.3.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/js/select2/select2-zh-CN.js?v=1.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/scmStock.js?v=5.2', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<script type="text/javascript">
// 获取物料列表
var materialList = <?php echo json_encode($model::getCompanymaterialArr()); ?>;
var stock = <?php echo json_encode($stock); ?>
</script>
<style>
	@media (max-width: 975px) {
		.form-inline .form-group{
			display: block;
    		width: 100%;
		}
		.form-inline .form-control {
			width:100%;
		}
		.del{
			margin-top: 1%;
		}
	}
	@media (min-width: 976px) {
		/*.form-group .field-scmstock-material_id{
			width:63%;
		}
        .form-group .field-scmstock-material_id select{
            width:90%;
        }*/
		.form-group .field-scmstock-material_num{
			width:30%;
		}
        .form-group .field-scmstock-material_gram{
            width:30%;
        }
	}
</style>
<div class="scm-stock-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'warehouse_id')->widget(\kartik\select2\Select2::classname(), [
    'data'          => ScmWarehouse::getWarehouseList('*', ['use' => ScmSupplier::MATERIAL]),
    'options'       => ['placeholder' => '请选择分库'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])?>


    <?=$form->field($model, 'reason')->dropDownList($model->getCompanyReasonArr())?>


    <?=!Yii::$app->user->can('配送员归还') ? '' : $form->field($model, 'distribution_clerk_id')->dropDownList(WxMember::getDistributionUserArr(3))?>
    <div id="material-item">
    <div class="form-group">

    <?=$form->field($model, 'material_id')->dropDownList($model::getCompanymaterialArr(), ['name' => 'ScmStock[material_id][]'])?>

    <div class="form-inline">
        <?=$form->field($model, 'material_num')->textInput(['name' => 'ScmStock[material_num][]' ,'maxlength' => 5])?>

        <?=$form->field($model, 'material_gram')->textInput(['name' => 'ScmStock[material_gram][]', 'maxlength' => 5])?>
    </div>

    </div>
    </div>
    <div class="form-group">
        <?=Html::button('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'save'])?>
        <?php if ($model->isNewRecord) {?>
        <input type="button" class="btn btn-primary addmaterial" value='增加物料选项'/>
        <?php }?>
    </div>

    <?php ActiveForm::end();?>

</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("[name='ScmStock[material_id][]']").select2();
    });
</script>

<!--提示框-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 id="myModalLabel">提示框</h5>
            </div>
            <div class="modal-body">
                <h5 class="form-group title text-center"></h5>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
