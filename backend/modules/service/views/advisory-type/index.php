<?php
use yii\helpers\Html;
?>
<h3>咨询类型设置</h3>

<?php echo Html::a('添加', ['advisory-type/add']); ?>
<table class="table">
<tr>
	<th>ID</th>
	<th>名称</th>
	<th>状态</th>
	<th>更新时间</th>
	<th>操作</th>
</tr>
<?php foreach ($advisoryList as $advisory): ?>
	<tr>
		<td><?php echo $advisory['advisory_type_id']; ?></td>
		<td><?php echo $advisory['advisory_type_name']; ?></td>
		<td><?php echo $advisory['is_show'] == 1 ? '上线' : '<span style="color:red">下线</span>'; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $advisory['update_time']); ?></td>
		<td><?php echo Html::a('修改', ['advisory-type/update', 'id' => $advisory['advisory_type_id']]); ?></td>
	</tr>
<?php endforeach;?>
</table>