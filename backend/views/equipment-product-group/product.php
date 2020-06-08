<p>
    <a class="btn btn-success" href="/equipment-product-group/index">返回上一页</a>
</p>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>单品名称</th>
            <th>单品价格(元)</th>
            <th>单品图片</th>
            <th>是否使用优惠券</th>
            <th>是否选糖</th>
            <th>半糖时间(s)</th>
            <th>全糖时间(s)</th>
            <th>单品排序</th>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($productList as $productInfo): ?>

                <tr data-key="">
                <td><?=yii\bootstrap\Html::a($productInfo['group_coffee_name'], '/coffee-product/view?id=' . $productInfo['product_id'])?></td>
                <td><?=$productInfo['group_coffee_price']?></td>
                <td><img src="<?=$productInfo['group_coffee_cover']?>" height="100" width="100"></td>
                <td><?=$productInfo['is_use_coupon'] == 1 ? '是' : '否';?></td>
                <td><?=$productInfo['cf_choose_sugar'] == 1 ? '是' : '否';?></td>
                <td><?=$productInfo['half_sugar']?></td>
                <td><?=$productInfo['full_sugar']?></td>
                <td><?=$productInfo['group_coffee_sort']?></td>
            </tr>
            <?php endforeach;?>

        </tbody>
</table>