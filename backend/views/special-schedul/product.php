<p>
    <a class="btn btn-success" href="/special-schedul/index">返回上一页</a>
</p>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>单品名称</th>
            <th>单品价格(元)</th>
            <th>活动价格(元)</th>
            <th>单品图片</th>
            <th>活动类型</th>
            <th>活动名称</th>
        </tr>
    </thead>
        <tbody>
            <?php foreach ($productList as $productInfo): ?>
                <tr data-key="">
                <td><?=yii\bootstrap\Html::a($productInfo['product_name'], '/coffee-product/view?id=' . $productInfo['product_id'])?></td>
                <td><?=$productInfo['product_price']?></td>
                <td><?=$productInfo['gifts_price']?></td>
               <td><img src="<?=$productInfo['product_cover']?>" height="100" width="100"></td>
                <td><?=$productInfo['special_schedul_type']?></td>
                <td><?=$productInfo['special_price_name']?></td>
            </tr>
            <?php endforeach;?>

        </tbody>
</table>