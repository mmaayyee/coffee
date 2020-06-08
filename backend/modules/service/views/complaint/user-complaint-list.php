<?php

use yii\widgets\LinkPager;

?>


<span>共计 <?php echo $pager->totalCount; ?> 条数据</span>
<table class="table table-bordered table-striped" style="width:100%">
    <thead>
    <th>序号</th>
    <th>客诉编号</th>
    <th>订单编号</th>
    <th>来电时间</th>
    <th>工号</th>
    <th>所在城市</th>
    <th>咨询类型</th>
    <th>问题类型</th>
    <th>问题描述</th>
    <th>进度</th>
    <th>编辑</th>
    </thead>
    <tbody>
    <?php $base = (Yii::$app->request->get('page', 1) - 1) * $pager->pageSize;?>
    <?php foreach ($complaintList as $index => $complaint): ?>
    <?php $codeArr = explode(',', $complaint['order_code']);?>
        <tr>
            <td><?php echo $base + $index + 1; ?></td>
            <td><a target="_blank" href="/service/complaint/view?id=<?=$complaint['complaint_id']?>#/detail"><?=$complaint['complaint_code']?></a></td>
            <td>
                <?php foreach ($codeArr as $code): ?>
                    <?PHP if (!empty($code)): ?>
                         <a target="_blank" href="/index.php/order-info/view?id=0&order_code=<?=$code?>"><?=$code?>,</a>
                    <?php endif;?>
            <?php endforeach;?>
            </td>
            <td><?php echo $complaint['add_time']; ?></td>
            <td><?php echo $complaint['manager_name']; ?></td>
            <td><?php echo $complaint['org_city']; ?></td>
            <td><?php echo $complaint['advisory_type_id']; ?></td>
            <td><?php echo $complaint['question_type_id']; ?></td>
            <td><?php echo $complaint['question_describe']; ?></td>
            <td><?php echo $complaint['process_status']; ?></td>
            <td>
                <a target="_blank" href="/index.php/service/complaint/add-complaint?complain_id=<?=$complaint['complaint_id']?>"><span  class="glyphicon glyphicon-pencil"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?=LinkPager::widget(['pagination' => $pager]);?>

