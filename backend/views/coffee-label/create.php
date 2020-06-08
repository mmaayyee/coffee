<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabel */

$this->title                   = '添加产品标签';
$this->params['breadcrumbs'][] = ['label' => '产品标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('@web/js/jquery-2.0.0.min.js');
$this->registerJsFile('@web/js/coffee-label.js');
?>
<script type="text/javascript">
    var url = '<?=Yii::$app->params['fcoffeeUrl']?>';
    var keys = '<?php echo ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>';
</script>
<div class="coffee-label-create">

<form action="" method="post" enctype="multipart/form-data">
    <h1><?=Html::encode($this->title)?></h1>
    <div class="form-group">
        <label class="control-label">标签名称</label>
        <input class="form-control" type="text" name="label_name">
        <label id="labelName_error" style="color:red"></label>
    </div>
    <div class="form-group">
        <label class="control-label">标签类型</label><br>
        <select name='access_status' class="form-control">
            <option value="1">默认</option>
            <option value="2">非默认</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label">标签状态</label><br>
        <select name='online_status' class="form-control">
            <option value="1">上线</option>
            <option value="2" selected>下线</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label">排序</label>
        <input class="form-control" type="text" name="sort" value="1">
        <label id="sort_error" style="color:red"></label>
    </div>
    <div class="form-group">
        <label class="control-label">选择单品</label><br>
        <label id="product_error" style="color:red"></label><br>
        <!--遍历单品列表-->
<?php
foreach ($productList as $product) {
    ?>
        <input id="checkbox1" type="checkbox" name="coffee_product_ids[]" value="<?=$product['id']?>"> <span style="margin-right: 10px "><?=$product['name']?></span>
         <label id="product_error"></label>
<?php }?>

    </div>

    <div class="form-group ">
        <label class="control-label">桌面图(选中前)</label>
        <input type="file">
        <input type="hidden" name="desk_img_url">
        <label id="img_error" style="color:red"></label>
        <img style="margin-top:20px;width: 100px;" src="">
    </div>
    <div class="form-group ">
        <label class="control-label">桌面图(选中后)</label>
        <input type="file">
        <input type="hidden" name="desk_selected_img_url">
        <label id="desk_selected_img_url_error" style="color:red"></label>
        <img style="margin-top:20px;width: 100px;" src="">
    </div>
    <div class="form-group">
        <label class="control-label">标签图</label>
        <input type="file">
        <input type="hidden" name="label_img_url">
        <label id="label_img_url_error" style="color:red"></label>
        <img style="margin-top:20px;width: 100px;" src="">
    </div>
    <div class="form-group">
        <input type="button" class='add btn btn-success'  value="保存">
    </div>
</form>

</div>
