<?php
use yii\helpers\Html;
?>
<h3>解决方案列表</h3>

<?php echo Html::a('添加', ['solution/add']); ?>
<table class="table">
<tr>
	<th>ID</th>
	<th>名称</th>
	<th>状态</th>
	<th>更新时间</th>
	<th>操作</th>
</tr>
<?php foreach ($solutionList as $solution): ?>
	<tr>
		<td><?php echo $solution['solution_id']; ?></td>
		<td><?php echo $solution['solution_name']; ?></td>
		<td><?php echo $solution['is_show'] == 1 ? '上线' : '<span style="color:red">下线</span>'; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $solution['update_time']); ?></td>
		<td><?php echo Html::a('修改', ['solution/update', 'id' => $solution['solution_id']]); ?></td>
	</tr>
<?php endforeach;?>
</table>