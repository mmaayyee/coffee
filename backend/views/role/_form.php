<?php
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$this->registerCssFile('/js/ztree//css/zTreeStyle/zTreeStyle.css', ['position' => View::POS_HEAD]);
$this->registerCssFile('/js/ztree//css/zTreeStyle/zTreeStyle.css', ['position' => View::POS_HEAD]);
$this->registerJsFile('/js/ztree/js/jquery.ztree.core-3.5.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/ztree/js/jquery.ztree.excheck-3.5.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="auth-item-form">

    <?php $form = ActiveForm::begin(['id' => 'roleForm']);?>

    <?php if ($model->name == $model::SUPER_MASTER): ?>
        <?=$form->field($model, 'name')->textInput(['maxlength' => 64, 'readonly' => true])?>
    <?php else: ?>
        <?=$form->field($model, 'name')->textInput(['maxlength' => 64])?>
    <?php endif;?>
    <div class="form-group field-authitem-rights">
    <label class="control-label" for="authitem-rights">角色权限</label>
    <div class="content_wrap">
            <div class="zTreeDemoBackground left">
                    <ul id="treeDemo" class="ztree"></ul>
            </div>
    </div>
<div class="help-block rights"></div>
</div>
    <div class="form-group">
        <input type="hidden" name="rightlist" id="rightlist" value="" />
        <?=Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'submitRight'])?>
    </div>

    <?php ActiveForm::end();?>

</div>


<?php
$this->registerJs(' <!--
		var setting = {
			check: {
				enable: true
			},
			data: {
				simpleData: {
					enable: true
				}
			}
		};

		var zNodes =[
                    ' . $rightsList . '
		];

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);

		});
                $("#roleForm").submit(function(){
                    var rights = new Array();
                    var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
                    var nodes = treeObj.getCheckedNodes(true);
                    if(nodes.length == 0){
                        $(".rights").html("请选择权限！");
                        return false;
                    }else{
                        $(".rights").html("");
                    }
                    for(var i=0; i< nodes.length; i++){
                        rights.push(nodes[i].id);
                    }
                    $("#rightlist").attr("value",rights.join("|"));
                });
		//-->
	', 5);
?>