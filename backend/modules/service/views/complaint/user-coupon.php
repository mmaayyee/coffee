<?php

use yii\widgets\LinkPager;
?>
<table class="table table-bordered table-striped" style="width:100%">
    <thead>
    <th>优惠券名称</th>
    <th>优惠券数量</th>
    <th>优惠券有效期</th>
    <th>可用商品</th>
    </thead>
    <tbody>
    <?php
    foreach($userCouponList as $key => $coffeeNumber):?>
        <tr>
            <td style="width:100px"><?php echo $coffeeNumber['coupon_name'];?></td>
            <td style="width:100px"><?php echo $coffeeNumber['coupon_number'];?></td>
            <td style="width:180px"><?php echo $coffeeNumber['coupon_time'];?></td>
            <td ><?php echo $coffeeNumber['product_name'];?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>



