<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/14
 * Time: 下午5:49
 */
$this->title = '添加商品';
$this->params['breadcrumbs'][] = ['label' => '周边商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-goods">

    <div>

    </div>
    <?php echo $this->render('_form', ['model' => $model, 'goods_id' => 0])?>

</div>
