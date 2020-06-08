<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabel */
$this->title                   = '编辑产品标签';
$this->params['breadcrumbs'][] = ['label' => '产品标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $data['id'], 'url' => ['view', 'id' => $data['id']]];
$this->params['breadcrumbs'][] = '编辑';
$this->registerJsFile('@web/js/jquery-2.0.0.min.js');
$this->registerJsFile('@web/js/coffee-label.js');
?>
<script type="text/javascript">
    var url = '<?=Yii::$app->params['fcoffeeUrl']?>';
    var keys = '<?php echo ".html?key=coffee08&secret=" . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>';
</script>
<div class="coffee-label-update">
<form action="" method="post" enctype="multipart/form-data">
    <h1><?=Html::encode($this->title)?></h1>
    <input type="hidden" name="id" value="<?=$data['id']?>">
    <div class="form-group">
        <label class="control-label">标签名称</label>
        <input class="form-control" type="text" name="label_name" value="<?=$data['label_name']?>">
        <label id="labelName_error" style="color:red"></label>
    </div>
    <div class="form-group">
        <label class="control-label">标签类型</label><br>
        <select name='access_status' class="form-control">
            <option value="1" <?php if ($data['access_status'] == 1) {echo 'selected';}?>>默认</option>
            <option value="2" <?php if ($data['access_status'] == 2) {echo 'selected';}?>>非默认</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label">标签状态</label><br>
        <select name='online_status' class="form-control">
            <option value="1" <?php if ($data['online_status'] == 1) {echo 'selected';}?>>上线</option>
            <option value="2" <?php if ($data['online_status'] == 2) {echo 'selected';}?>>下线</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label">排序</label>
        <input class="form-control" type="text" name="sort" value="<?=$data['sort']?>">
        <label id="sort_error" style="color:red"></label>
    </div>
    <div class="form-group">
        <label class="control-label">选择单品</label><br>
        <!--遍历单品列表-->
<?php
foreach ($productList as $product) {
    ?>
        <input type="checkbox" name="coffee_product_ids[]" value="<?=$product['id']?>" <?php if (in_array($product['id'], $data['coffeeProductList'])) {echo 'checked';}?> > <span style="margin-right: 10px "><?=$product['name']?></span>
<?php }?>
    </div>

    <div class="form-group">
        <label class="control-label">桌面图(选中前)</label>
        <input type="file">
        <input type="hidden" name="desk_img_url" value="<?=$data['desk_img_url']?>">
        <label id="desk_img_url_error" style="color:red"></label>
        <img style="margin-top:20px;width: 100px;" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['desk_img_url']?>">
    </div>
     <div class="form-group ">
        <label class="control-label">桌面图(选中后)</label>
        <input type="file">
        <input type="hidden" name="desk_selected_img_url" value="<?=$data['desk_selected_img_url']?>">
        <label id="desk_selected_img_url_error" style="color:red"></label>
        <img style="margin-top:20px;width: 100px;" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['desk_selected_img_url']?>">
    </div>
    <div class="form-group">
        <label class="control-label">标签图</label>
        <input type="file">
        <input type="hidden" name="label_img_url" value="<?=$data['label_img_url']?>">
        <img style="margin-top:20px;width: 100px;" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['label_img_url']?>">
    </div>
    <div class="form-group">
        <input type="button" class='add btn btn-success' value="保存">
    </div>
</form>
</div>
