<?php
use yii\helpers\Html;
?>
<h3>问题类型设置</h3>

<?php echo Html::a('添加', ['question-type/add']); ?>
<table class="table">
<tr>
	<th>ID</th>
	<th>名称</th>
	<th>状态</th>
	<th>所属咨询类型</th>
	<th>更新时间</th>
	<th>操作</th>
</tr>
<?php foreach ($questionTypeList as $questionType): ?>
	<tr>
		<td><?php echo $questionType['question_type_id']; ?></td>
		<td><?php echo $questionType['question_type_name']; ?></td>
		<td><?php echo $questionType['is_show'] == 1 ? '上线' : '<span style="color:red">下线</span>'; ?></td>
		<td><?php echo $questionType['advisory_type_name'] ?></td>
		<td><?php echo date('Y-m-d H:i:s', $questionType['update_time']); ?></td>
		<td><?php echo Html::a('修改', ['question-type/update', 'id' => $questionType['question_type_id']]); ?></td>
	</tr>
<?php endforeach;?>
</table>