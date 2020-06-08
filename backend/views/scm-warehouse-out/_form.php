<?php

use backend\models\ScmStock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseOut */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-warehouse-out-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'author')->textInput(['maxlength' => 8])?>

    <?=$form->field($model, 'warehouse_id')->dropDownList(\backend\models\ScmWarehouse::getWarehouseNameArray())?>

	<div id="material-item">
    <div class="form-inline form-group">
    <?=$form->field($model, 'material_id')->dropDownList(ScmStock::getCompanymaterialArr(), ['name' => 'ScmWarehouseOut[material_id][]'])?>

    <?=$form->field($model, 'material_out_num')->textInput(['name' => 'ScmWarehouseOut[material_out_num][]'])?>
    </div>
    </div>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
        <input type="button" class="btn btn-primary addmaterial" value='增加物料包'/>
    </div>

    <?php ActiveForm::end();?>

</div>
<script type="text/javascript" src="/js/third-party/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
    $(function(){
        var materialList = '<?php echo json_encode(ScmStock::getCompanymaterialArr()); ?>';

        materialList = JSON.parse(materialList);

        $(".addmaterial").click(function(){
            var html = "";
            html += '<div class="form-inline form-group"><label class="control-label" for="scmwarehouseout-material_id">出库物料</label> <select class="form-control" name="ScmWarehouseOut[material_id][]"><option value="">请选择</option>';
            if (materialList){
                for (var i in materialList) {
                    if (i != '') {
                        html += '<option value="'+i+'">'+materialList[i]+'</option>';
                    }
                }
            }

            html += '</select> <label class="control-label" for="scmwarehouseout-material_out_num">物料数量/包</label> <input type="text" id="scmwarehouseout-material_out_num" class="form-control" name="ScmWarehouseOut[material_out_num][]" /> <input type="button" class="btn btn-danger del" value="删除" /></div>';
            $("#material-item").append(html);
            $(".del").click(function(){
                $(this).parent().remove();
            })
        });
    });
</script>