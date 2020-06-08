<?php

?>
<table class="table table-bordered table-striped" style="width:50%">
    <thead>
    <th>楼宇名称</th>
    </thead>
    <tbody>
    <?php foreach ($buildNameList as $buildName): ?>
        <?php if ($buildName): ?>
        <tr>
            <td><?php echo $buildName; ?></td>
        </tr>
        <?php endif?>
    <?php endforeach;?>
    </tbody>
</table>



