<?php

?>
<table class="table table-bordered table-striped" style="width:50%">
    <thead>
    <th>单品名称</th>
    <th>单品价格（元）</th>
    <th>状态</th>
    </thead>
    <tbody>
    <?php if (!empty($buildProductList)): ?>
    <?php foreach ($buildProductList as $key => $product): ?>
            <tr>
                <td><?php echo $product['group_coffee_name']; ?></td>
                <td><?php echo $product['group_coffee_price']; ?></td>
                <td><?php echo $product['online']; ?></td>
            </tr>
            <?php endforeach;?>
            <?php else: ?>
                <tr>
                <td colspan="3" style="text-align: center;">暂无数据~</td>
            </tr>
     <?php endif?>


    </tbody>
</table>



