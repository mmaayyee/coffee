<?php

use yii\widgets\LinkPager;

?>
<table class="table table-bordered table-striped" style="width:50%">
    <thead>
    <th>单品名称</th>
    <th>单品数量</th>
    </thead>
    <tbody>
    <?php // // 可能会有咖啡重名的问题
    foreach($userCoffee as $coffeeName => $coffeeNumber):
        foreach ($coffeeNumber as $name => $number):?>
        <tr>
            <td><?php echo $name;?></td>
            <td><?php echo $number;?></td>
        </tr>
    <?php endforeach;
        endforeach;?>
    </tbody>
</table>



