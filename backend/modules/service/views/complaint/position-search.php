<?php

use kartik\select2\Select2;
?>
<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>
<form class="form-inline">
	<div class="form-group">
	    <label class="sr-only" for="position-number-input">设备编号</label>
	    <input name="number" type="text" class="form-control" id="position-number-input" placeholder="请输入设备编号">
  	</div>
  	<div class="form-group" style="width:200px;">
		<label class="sr-only" for="position-name-input">点位名称</label>
	<?=Select2::widget([
    'model'         => $model,
    'name'          => 'building_id',
    'attribute'     => 'building_id',
    'data'          => $buildingList,
    'options'       => [
        'multiple'    => false,
        'placeholder' => '请输入点位名称',
        'value'       => $model->building_id,
    ],
    'pluginOptions' => [
        'width'      => '100%',
        'allowClear' => true,
    ]]);?>
	</div>

  <a id="position-search" href="javascript:;" class="btn btn-success btn-sm">搜索</a> &nbsp;
  <a target="_blank" href="/service/complaint/add-complaint" class="btn btn-primary btn-sm">新建客诉</a>
  <a target="_blank" href="/order-info/index" class="btn  btn-info btn-sm">订单管理</a>
</form>

<div style="min-height: 200px">
	<h4>设备信息</h4>
	<div class="container" id="equipment-info">
	</div>
</div>

<div style="min-height: 200px">
	<h4 style="float:left">消费记录</h4>
    <span style="float:right" id="show-all-consum-url">查看全部</span>
	<div>
		<table class="table table-bordered table-striped">
			<thead>
				<th>消费ID</th>
				<th>订单编号</th>
				<th>原始订单编号</th>
				<th>电话号码</th>
				<th>制作时间</th>
				<th>单品名称</th>
				<th>制作糖量</th>
				<th>领取方式</th>
				<th>付款金额</th>
				<th>兑换券名称</th>
				<th>结果</th>
				<th>处理状态</th>
				<th>操作</th>
			</thead>
			<tbody id="consum-list">
			</tbody>
		</table>
	</div>
</div>
<div style="min-height: 200px">
    <h4 style="float:left">客诉记录</h4>
    <span style="float:right" id="show-all-complaint-url">查看全部</span>
    <div>
        <table class="table table-bordered table-striped">
            <thead>
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
            <tbody id="complaint-list">
            </tbody>
        </table>
    </div>
</div>
<script>
	<!--确认退款-->
function confirmRefund(id){
 var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
	var isconfirm = confirm("确认要退咖啡吗");
	if(isconfirm){
		$.ajax({
			url:url +'erpapi/customer-service/refund-coffee.html?comsume_id=' +id,
			data:'',
			dataType: 'json',
			type:'get',
			success:function(data){
				if(data.msg == 'success'){
					alert("退还咖啡成功")
				}else{
					alert("退还咖啡失败")
				}

			},
			error:function(){
				alert("退款失败")
			}
		})
	}
}
</script>
<?php ob_start();?>
$('#position-search').click(function(){
	$.get('/service/complaint/position-search', $(this).parents('form').serialize(), function(info){
		if(info.length != 0){
			$('#equipment-info').html(createEquipInfoHtml(info));
			//拉去消费记录
			$.get('/service/complaint/position-lastest-info',{build_id:info.id}, function(lastestInfo){
				if(lastestInfo.consumList.length != 0){
					$('#consum-list').html(createConsumListHtml(lastestInfo.consumList));
                   	$('#show-all-consum-url').html('<a target="_blank" href="/index.php/service/complaint/build-consume-list?build_id=' + info.id + '">查看全部</a>');
				}else{
					$('#consum-list').html('<td colspan="12" style="text-align: center;font-size: 20px">暂无数据!</td>');
				}
				if (lastestInfo.complaintList) {
					$('#complaint-list').html(createComplaintListHtml(lastestInfo.complaintList));
					$('#show-all-complaint-url').html('<a target="_blank" href="/index.php/service/complaint/build-complaint-list?build_id=' + info.id + '">查看全部</a>');
				}else{
					$('#complaint-list').html('<td colspan="10" style="text-align: center;font-size: 20px">暂无数据~</td>');
				}
			}, 'json');
		}else{
			$('#equipment-info').html('<h2>暂无数据~</h2>');
		}
	}, 'json');
	return false;
});

function createEquipInfoHtml(info){
		return ['<div class="row">',
			 	'<div class="col-md-3">',
			 		'<p>设备编码: <a target="_blank" href="/service/complaint/equipments-info?code='+info.equipment.equipment_code+'">',	info.equipment.equipment_code, '	</a>','</p>',
			 		'<p>设备状态：',	info.equipment.status,	'</p>',
			 		'<p>地区：',		info.city,	'</p>',
			   '</div>',
			   '<div class="col-md-3">',
			 		'<p>楼宇：',	info.name,	'</p>',
			 		'<p>运营状态：', info.equipment.online, '</p>',
			 	    '<p>分公司：', info.equipment.branch, '</p>',
			   '</div>',
			   '<div class="col-md-3">',
					'<p>设备类型：', info.equipment.equip_type_id,'</p>',
					'<p>是否锁定：',  info.equipment.work_status, '</p>',
					'<p><a target="_blank" href="build-coffee?equipment_code='+info.equipment.equipment_code+'">饮品菜单</a>', '</p>',
				'</div>',
		   '</div>'].join('');
}

function createConsumListHtml(ConsumList){
 var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
	var html = [];
	for(id in ConsumList){
	var refundUrl = '';
	if (ConsumList[id].refund_coffee == 1) {
		refundUrl =  '<a  class="glyphicon glyphicon-share-alt"' +
			'href="javascript:void(0)" onclick="confirmRefund('+ConsumList[id].user_consume_id+')">', '</a>'
	}
		var row = [
			'<tr>',
    '<td>', '<a target="_blank" href="/index.php/user-consume/view?id=' + ConsumList[id].user_consume_id + '#/detail">',ConsumList[id].user_consume_id,'</a>', '</td>',
    '<td>', '<a target="_blank" href="/index.php/order-info/view?id=' + ConsumList[id].order_id + '#/detail">',ConsumList[id].order_code,'</a>', '</td>',
    '<td>', '<a target="_blank" href="/index.php/order-info/view?id=' + ConsumList[id].order_source_id + '#/detail">',ConsumList[id].order_source_code,'</a>', '</td>',
    '<td>', ConsumList[id].username, '</td>',
				'<td>', ConsumList[id].fetch_time, '</td>',
				'<td>', ConsumList[id].product_name,'</td>',
				'<td>', ConsumList[id].user_consume_sugar,'</td>',
				'<td>', ConsumList[id].consume_type,'</td>',
				'<td>', ConsumList[id].actual_fee,'</td>',
				'<td>', ConsumList[id].exchange_coupon_name,'</td>',
				'<td>', ConsumList[id].make_result,'</td>',
				'<td>', ConsumList[id].detail_status,'</td>',
				'<td>',  '<a target="_blank"  class="glyphicon glyphicon-pencil"' +
        'href="/index.php/service/complaint/add-complaint?user_consume_id='+ ConsumList[id].user_consume_id +
        '&order_code='+ ConsumList[id].order_code +
        '&build_id='+ ConsumList[id].build_id +
        '&user_id='+ ConsumList[id].user_id +
        '&org_id='+ ConsumList[id].org_id +
        '&nickname='+ ConsumList[id].nickname +
        '&register_mobile='+ ConsumList[id].username +
        '&pay_type='+ ConsumList[id].pay_type +
        '&pay_at='+ ConsumList[id].pay_at +
        '">','&nbsp;', '</a>','<a target="_blank" href="/index.php/quick-send-coupon/create?consume_id='+ ConsumList[id].user_consume_id +
        '&order_code='+ ConsumList[id].order_code+'&phone='+ ConsumList[id].username +
        '">','发券', '</a>',refundUrl, '</td>',
			'</tr>',
		];
		html.push(row.join(''));
	}
	return html.join('');
}

function createComplaintListHtml(complaintList){
var html = [];
for(id in complaintList){
var orderCode = complaintList[id].order_code;
	var orderCodeArray = orderCode.split(",");
	var urlStr = '';
orderCodeArray.forEach(function(item,index){
	if(item!=""){
		urlStr = urlStr + '<a target="_blank" href="/index.php/order-info/view?id=0&order_code='+ item + '">'+ item +  ',</a>'
	}
});
var row = [
'<tr>',
    '<td>', '<a target="_blank" href="/index.php/service/complaint/view?id=' + complaintList[id].complaint_id + '#/detail">',complaintList[id].complaint_code,'</a>', '</td>',
     '<td>',urlStr,'</td>',
    '<td>', complaintList[id].add_time, '</td>',
    '<td>', complaintList[id].manager_name,'</td>',
    '<td>', complaintList[id].org_city,'</td>',
    '<td>', complaintList[id].advisory_type_id,'</td>',
    '<td>', complaintList[id].question_type_id,'</td>',
    '<td>', complaintList[id].question_describe,	'</td>',
    '<td>', complaintList[id].process_status,	'</td>',
    '<td>',
        '<a target="_blank"  class="glyphicon glyphicon-pencil"  href="/index.php/service/complaint/add-complaint?complain_id=' + complaintList[id].complaint_id +'">', '</a>',
        '</td>',
    '</tr>'
];
html.push(row.join(''));
}
return html.join('');
}
<?php $this->registerJs(ob_get_clean());?>

