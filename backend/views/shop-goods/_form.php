<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/14
 * Time: 下午7:11
 */
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [\backend\assets\AppAsset::className()]]);
$this->registerJsFile("@web/js/laytpl.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/regular_verification.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/ueditor.config.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/ueditor.all.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("/js/uploadPreview.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/shop_goods_form.js?v=4.4", ["depends" => [JqueryAsset::className()]]);
?>
<style type="text/css">
    .attribute > div {
        margin: 5px 0;
        padding: 5px;
        border:1px solid #ccc;
    }
    .attribute > div > .attrValue:first-child{
        margin-left: 10px;
    }

    @media (min-width: 768px){
        .attribute  .attrValue {
            display: inline-block;
            width:30%;
            margin: 5px 0 5px 5px;
            vertical-align: top;
        }
        .attrValue .form-group {
            display: inline-block;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .attrValue .form-control{
            display: inline-block;
            width: auto;
            vertical-align: middle;
        }
        .attrValue .btn{
            vertical-align: top;
        }
        .photo .form-inline{
            margin-top: 10px;
        }
        .photo .form-inline .form-group{
            margin-right: 10px;
        }
        .submit-error,.error{
            color: #a94442;
        }
        .error{
            display:none;
        }
    }
    .btn-success {
        margin-bottom: 0;
    }
</style>
<div class="shop-goods-form">

    <?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]);?>

    <?=$form->field($model, 'goods_id')->hiddenInput(['name' => 'ShopGoods[goods_id]', 'id' => 'goods_id', 'value' => $goods_id])->label(false);?>

    <?=$form->field($model, 'goods_name')->textInput(['name' => 'ShopGoods[goods_name]', 'maxlength' => 50, 'check-type' => 'required']);?>

    <!--修改时图片数据-->
    <?=$form->field($model, 'image')->hiddenInput(['name' => 'ShopGoods[image]', 'id' => 'image'])->label(false);?>
    <!--删除的图片数据-->
    <?=$form->field($model, 'delete_image')->hiddenInput(['name' => 'ShopGoods[delete_image]', 'id' => 'delete_image', 'value' => ''])->label(false);?>

    <div class="photo">
        <label class="control-label">商品图片</label>
        <button id="z_photo" class="btn btn-sm" type="button"><span class="glyphicon glyphicon-plus"></span></button>
        <div>上传图片必须小于200k,尺寸要求750x750,点击图片可删除</div>
        <div class="form-inline"></div>
        <p class="error">请上传图片</p>
    </div>

    <div class="form-group form-inline" style="line-height:60px;">
        <?=$form->field($model, 'specification')->textInput(['name' => 'ShopGoods[specification]', 'maxlength' => 20, 'check-type' => 'required']);?>

        <?=$form->field($model, 'suttle')->textInput(['name' => 'ShopGoods[suttle]', 'maxlength' => 20, 'check-type' => 'required']);?>

        <?=$form->field($model, 'expiration')->textInput(['name' => 'ShopGoods[expiration]', 'maxlength' => 20, 'check-type' => 'required']);?>
    </div>

    <?=$form->field($model, 'producter')->textInput(['name' => 'ShopGoods[producter]', 'maxlength' => 50, 'check-type' => 'required']);?>

    <?=$form->field($model, 'status')->radioList([$model::OFFLINE => '下架', $model::WAIT_CHECK => '申请上架']);?>

    <div class="attribute">
        <div class="product_attr product_attr1">
            <div class="form-inline Father_Title" propid="1">
                <div class="form-group">
                    <label>属性</label>
                    <input class="form-control" id="attr1" type="text" name="attr[1][name]" value="" onchange="getAttrValue(this,1)" check-type="required" maxlength="10"/>
                </div>
                <button class="btn btn-sm addBtn" type="button" onclick="addAttr(this)"><span class="glyphicon glyphicon-plus"></span></button>
                <button class="btn btn-sm" type="button" onclick="addAttrValue(this)"><span class="glyphicon glyphicon-plus"></span>属性值</button>
            </div>
            <div class="attrValue form-inline">
                <div class="form-group" propvalid="-1">
                    <label>属性值</label>
                    <input name="attr[1][value][0]" onchange="getAttrValue(this,2);"  data-id="prop-attr1_0" class="form-control" type="text" check-type="required" maxlength="10"/>
                </div>
            </div>
        </div>
    </div>
    <div id="createTable" class="details"></div>

    <div class="table-field-activity-activity_desc ">
        <label class="control-label" for="activity-activity_desc">详情</label>
        <script id="editor" type="text/plain"></script>
        <span class="help-block error" id="valierr">请输入产品详情</span>
    </div>
    <div class="submit-error form-group"></div>
    <div class="form-group">
        <?=Html::button('保存', ['class' => 'btn btn-success', 'onclick' => 'uploadFile()'])?>
        <?=Html::button('取消', ['class' => 'btn btn-info', 'id' => 'cancel', 'onclick' => 'skipList()'])?>
    </div>
    <?php ActiveForm::end();?>

</div>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="delModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>是否删除图片？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default cancel" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary sure" data-dismiss="modal">确认</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog"  id="tsModal">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary sure" data-dismiss="modal">确认</button>
            </div>
        </div>
    </div>
</div>
<script>
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var image = "<?php echo $model->image; ?>";
    var skuAttr = '<?php echo $model->sku_attr; ?>';
    var skuList = '<?php echo $model->sku_list; ?>';
    var content = '<?php echo $model->content; ?>';
</script>
<script id="attrTpl" type="text/html">
    {{# var n =1}}
    {{# if(d.type == 1){ }}
    <div class="product_attr product_attr{{n+1}}">
        <div class="form-inline Father_Title">
            <div class="form-group">
                <label>属性</label>
                <input  class="form-control" id="attr{{d.attrNum}}" type="text" name="attr[{{d.attrNum}}][name]" onchange="getAttrValue(this,1)" check-type="required" maxlength="10"/>
            </div>
            <button class="btn btn-sm" type="button" onclick="delAttr($(this).parent())"><span class="glyphicon glyphicon-remove"></span></button>
            <button class="btn btn-sm" type="button" onclick="addAttrValue(this)"><span class="glyphicon glyphicon-plus"></span>属性值</button>
        </div>
        <div class="attrValue form-inline">
            <div class="form-group" propvalid="-1">
                <label>属性值</label>
                <input data-id="prop-attr{{d.attrNum}}_0" class="form-control" type="text" name="attr[{{d.attrNum}}][value][0]" onchange="getAttrValue(this,2);" check-type="required" maxlength="10"/>
            </div>
        </div>
    </div>
    {{# }else if(d.type == 2){ }}
    <div class="attrValue form-inline">
        <div class="form-group">
            <label>属性值</label>
            <input data-id="prop-{{d.vauleID}}_{{d.indx}}" class="form-control" type="text" name="attr[{{d.id}}][value][{{d.attrValueNum}}]" onchange="getAttrValue(this,2);" check-type="required"/>
        </div>
        <button class="btn btn-sm delCol" type="button" onclick="delAttr($(this))"><span class="glyphicon glyphicon-remove"></span></button>
    </div>
    {{# } }}
</script>