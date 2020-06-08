<?php
use janisto\timepicker\TimePicker;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/ajaxfileupload.js", ["depends" => ["backend\assets\AppAsset"]]);
$this->registerJsFile("@web/js/laytpl.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/uploadPreview.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/product_formula.js?v=1.7", ["depends" => [JqueryAsset::className()]]);
$this->registerJs('new uploadPreview({ UpBtn: "up_img", DivShow: "imgdiv", ImgShow: "imgShow" });', 5);
$this->registerJs('new uploadPreview({ UpBtn: "up_image", DivShow: "imagediv", ImgShow: "imageShow" });', 5);
$this->registerJs("
    // $('.ingredient .check').each(function(i,obj){
    //     $(obj).on('click',function(){
    //         var length=$('.ingredient .check:checked').length;
    //         if($(this).is(':checked')){
    //                 $(this).siblings('.ingredient-text').show();
    //         }else{
    //                 $(this).siblings('.ingredient-text').hide();
    //         }
    //         if(length>6||length<1){
    //                 $('.ingredient .error').html('成分最少选择一项,最多选择6项');
    //         }else{
    //             $('.ingredient .error').html('');
    //         }
    //     })
    // })
");
$this->title                   = '添加单品';
$this->params['breadcrumbs'][] = ['label' => '单品管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    @media only screen and (min-width: 768px) {
        .form-inline > .form-group{
            width: 45%;
            margin-bottom: 10px;
        }
        .form-inline .control-label {
            width: 100px;
        }
        #coffeeproduct-cf_product_hot, #coffeeproduct-cf_product_status{
            min-width: 190px;
        }
    }
        .btn-primary.btn-sm{
        margin-bottom: 10px;
    }
    .formula table input[type="text"]{
        width: 100%;
    }
    .submit-error{
        color: #a94442;
    }
    .setup-choose-sugare .form-inline .control-label{
        width: auto;
    }
    .choose-sugar{
        margin: 10px 0;
    }
    .panel-body{
        border-bottom: 1px dashed #ccc;
    }
    .panel-body:last-child{
        border-bottom: none;
    }
    .panel-heading {
        font-weight: bold;
    }
    .field-coffeeproduct-price_start_time.has-error, .field-coffeeproduct-price_end_time.has-error {
        margin-bottom: 20px;
    }
    .datetime #valierr{
        position: absolute;
        top: 34px;
    }
    .field-coffeeproduct-cf_product_english_name #autoreqmark, .field-up_img #autoreqmark{
         visibility:hidden;
    }
    /*成分*/
    .ingredient span.control-label{
        display: inline-block;
        width:100px;
        float:left;
        font-weight: bold;
        color:#333;
    }
    .ingredient ul{
        width:80%;
        float:left;
        list-style: none;
    }
    .ingredient ul li{
        float:left;
        margin-right:5px;
    }
    .clear{
        clear:both;
    }
    .ingredient-text{
        width:20px;
        height: 20px;
        border:1px solid #ccc;
        margin-left:10px;
        text-align: center;
        outline: none;
        display: none;
    }
    .ingredient .check{
        position: relative;
        top:2px;
    }
    .ingredient .error{
        display: block;
        clear: both;
        color: #a94442;
    }
    .ingredient .help-block{
        color: #a94442;
    }
</style>
<script type="text/javascript">
    var equipTypeStockList = <?php echo $equipTypeStockList; ?>;
    var cofProStockRecipeList = <?php echo $cofProStockRecipeList; ?>;
    var url = "<?php echo Yii::$app->params['fcoffeeUrl'] ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
</script>
    <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'w2', 'options' => ['enctype' => 'multipart/form-data']])?>
    <div class="form-inline">
        <?=$form->field($model, 'cf_product_name')->textInput()?>
        <?=$form->field($model, 'cf_product_english_name')->textInput()?>
        <div class="form-group form-inline">
            <?=$form->field($model, 'cf_product_cover')->fileInput(['id' => 'up_image'])?>
            <div class="form-group" id="imagediv">
                <img id="imageShow" width="100" height="100" src="<?php echo $model->cf_product_cover; ?>"/>
            </div>
        </div>
        <div class="form-group form-inline">
            <?=$form->field($model, 'cf_product_thumbnail')->fileInput(['id' => 'up_img'])?>
            <div class="form-group" id="imgdiv">
                <img id="imgShow" width="100" height="100" src="<?php echo $model->cf_product_thumbnail; ?>"/>
            </div>
        </div>
        <?=$form->field($model, 'cf_product_price')->textInput()?>
        <?=$form->field($model, 'cf_special_price')->textInput()?>
        <?=$form->field($model, 'price_start_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readOnly' => 'readOnly'],
    'clientOptions' => [
        'language '  => 'zh-CN',
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]]);?>
        <?=$form->field($model, 'price_end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readOnly' => 'readOnly', 'check-type' => 'compareDate'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
    ]]);?>
        <?=$form->field($model, 'cf_product_hot')->dropDownList($model->getTypeArray())?>
        <?=$form->field($model, 'cf_product_type')->dropDownList($model->productType)?>
       <?=$form->field($model, 'cf_product_kind')->dropDownList($model->productKind)?>
         <?=$form->field($model, 'cf_product_status')->dropDownList($model->getStatusArray())?>
        <?=$form->field($model, 'cf_texture')->textInput()?>
        <?=$form->field($model, 'cf_product_id')->hiddenInput()->label(false);?>
        <!-- <div class="ingredient">
            <span class="control-label">单品成分</span>
            <ul>
            <?php //foreach ($ingredientArray as $key => $ingredient) {?>
                <li>
                    <input type='checkbox' class="check" name="CoffeeProduct[ingredient][<?php //echo $key;?>][id]" value="<?php //echo $ingredient['id']?>">
                    <label><?php //echo $ingredient['ingredient_name']?></label>
                    <input type='text' class="ingredient-text" name="CoffeeProduct[ingredient][<?php //echo $key?>][sort]" value="">
                </li>
            <?php //}?>

            </ul>
            <div class="error"></div>
            <div class="clear"></div>
        </div> -->
    </div>

    <?=$this->render('_formula', ['equipTypeStockList' => $equipTypeStockList])?>

    <div class="submit-error"></div>
    <div class="form-group">
        <button type="submit" class="btn btn-success">保存</button>
    </div>
<?php ActiveForm::end();?>
<!--提示框-->
<?=$this->render('/coupon-send-task/_tip.php');?>
