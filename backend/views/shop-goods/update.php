<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 18/2/23
 * Time: 下午7:10
 */
$this->title = '修改商品';
$this->params['breadcrumbs'][] = ['label' => '周边商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-goods">
    <div>
    </div>
    <?php echo $this->render('_form', ['model' => $model, 'goods_id' => $goods_id])?>

</div>
