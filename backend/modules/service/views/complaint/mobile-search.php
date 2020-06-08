
<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>
<div class="col-xs-2">
<input type="text"  name="mobile" id="mobile-input"  class="form-control" placeholder="请输入手机号码" />
</div>
<a id="search" href="javascript:;" class="btn btn-success btn-sm">搜索</a> &nbsp;
<a target="_blank"  href="/service/complaint/add-complaint" class="btn btn-primary btn-sm">新建客诉</a>
<a target="_blank" href="/order-info/index" class="btn  btn-info btn-sm">订单管理</a>
<div style="min-height: 200px">
	<h4>用户信息</h4>
	<div class="container" id="user-info">

	</div>
</div>
<div style="min-height: 200px">
	<h4 style="float:left">消费记录</h4>
	<span style="float:right" id="show-all-consum-url"></span>
	<div>
		<table class="table table-bordered table-striped">
			<thead>
				<th>消费ID</th>
				<th>订单编号</th>
				<th>原始订单编号</th>
				<th>制作时间</th>
				<th>单品名称</th>
				<th>制作糖量</th>
				<th>领取方式</th>
				<th>付款金额</th>
				<th>兑换券名称</th>
				<th>点位名称</th>
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
	<h4 style="float:left">订单记录</h4>
	<span style="float:right" id="show-all-order-url">查看全部</span>
	<div>
		<table class="table table-bordered table-striped">
			<thead>
				<th>订单编号</th>
				<th>创建时间</th>
				<th>支付方式</th>
				<th>付款金额</th>
				<th>优惠券名称</th>
				<th>订单杯数</th>
				<th>已消费杯数</th>
				<th>订单状态</th>
				<th>订单来源</th>
				<th>操作</th>
			</thead>
			<tbody id="order-list">
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

$('#search').click(function(){
    var mobile = $('#mobile-input').val();
    if(!(/^1[3456789]\d{9}$/.test(mobile))){
        alert("手机号码有误，请重填");
        return false;
    }
    $.get('/service/complaint/mobile-search', {mobile:mobile}, function(data){
    	if(data.userInfo.length != 0){
    		$('#user-info').html(createUserInfoHtml(data.userInfo));
    		$.get('/service/complaint/latest-info', {user_id: data.userInfo.id}, function(info){
    		if (info.orderList.length !=0) {
				$('#order-list').html(createOrderListHtml(info.orderList,data.userInfo));
				$('#show-all-order-url').html('<a target="_blank" href="/index.php/service/complaint/user-order-list?user_id=' + data.userInfo.id +
                '&nickname='+ data.userInfo.nickname +
                '&register_mobile='+ data.userInfo.username +
                '">查看全部</a>');
    		}else{
				$('#order-list').html('<td colspan="10" style="text-align: center;font-size: 20px">暂无数据!</td>');
        	}
	        if (info.consumList.length !=0) {
					$('#consum-list').html(createConsumListHtml(info.consumList,data.userInfo));
					$('#show-all-consum-url').html('<a target="_blank" href="/index.php/service/complaint/user-consume-list?user_id=' + data.userInfo.id +
	                '&nickname='+ data.userInfo.nickname +
	                '&register_mobile='+ data.userInfo.username +
	                '">查看全部</a>');
	    	}else{
					$('#consum-list').html('<td colspan="13" style="text-align: center;font-size: 20px">暂无数据!</td>');
	        }
			if (info.complaintList.length !=0) {
					$('#complaint-list').html(createComplaintListHtml(info.complaintList));
					$('#show-all-complaint-url').html('<a target="_blank" href="/index.php/service/complaint/user-complaint-list?user_id=' + data.userInfo.id +
					'">查看全部</a>');
	    	}else{
					$('#complaint-list').html('<td colspan="10" style="text-align: center;font-size: 20px">暂无数据!</td>');
	        }
    		}, 'json');
    	}else{
			$('#user-info').html('<h2>暂无数据~</h2>');
		}
    },'json');
});

function createUserInfoHtml(userInfo){
	return ['<div class="row">',
			 	'<div class="col-md-3">',
			 		'<p>来电号码：',	userInfo.mobile, 	'</p>',
			 		'<p>注册号码：',	userInfo.mobile,	'</p>',
			 		'<p>注册时间：',	userInfo.created_at,	'</p>',
			 		'<p>注册城市：',	userInfo.register_city,	'</p>',
			 		'<p>注册楼宇：',	userInfo.building,	'</p>',
			   '</div>',
			   '<div class="col-md-2">',
			 		'<p>咖豆数量：',	userInfo.total_surplus_beans,	'</p>',
			 		'<p><a target="_blank" href="user-coffee?user_id='+userInfo.id+'">咖啡列表</a></p>',
                    '<p><a target="_blank" href="user-coupon?user_id='+userInfo.id+'">优惠券列表</a></p>',
			   '</div>',
			   '<div class="col-md-3">',
			 		'<p>用户类型：',	userInfo.userType, 	'</p>',
			 		'<p>公司名称：',	userInfo.companyName,	'</p>',
			 		'<p><a target="_blank" href="building?cid='+userInfo.companyId+'">合作点位列表</a></p>',
			 		'<p>合作方式：',	userInfo.cooperateType,	'</p>',
			 		'<p>价格：',	userInfo.fixPrice,	'</p>',
			 		'<p>平台补贴：',	userInfo.coffeeAllowance,	'</p>',
			 		'<p>公司补贴：',	userInfo.companyAllowance,	'</p>',
			   '</div>',
			   '<div class="col-md-2">',
					'<img width="80px" height="80px" src="', userInfo.head_avatar, '" />',
					'<p>昵称：', userInfo.nickname,'</p>',
					'<p>ID：',  userInfo.id, '</p>',
				'</div>',
		   '</div>'].join('');
}

function createOrderListHtml(orderList,userInfo){
	var html = [];
	for(id in orderList){
		var row = [
			'<tr>',
				'<td>', '<a target="_blank" href="/index.php/order-info/view?id=' + orderList[id].order_id + '#/detail">',orderList[id].order_code,'</a>', '</td>',
				'<td>', orderList[id].created_at, '</td>',
				'<td>', orderList[id].pay_type_name, '</td>',
				'<td>', orderList[id].actual_fee, '</td>',
				'<td>', orderList[id].coupon_names,'</td>',
				'<td>', orderList[id].order_cups,'</td>',
				'<td>', orderList[id].consumed_number,'</td>',
				'<td>', orderList[id].order_status_name,'</td>',
				'<td>', orderList[id].source_type_name,'</td>',
    '<td><a target="_blank" class="glyphicon glyphicon-pencil" href="/index.php/service/complaint/add-complaint?order_code='+ orderList[id].order_code +
        '&user_id='+ orderList[id].user_id +
        '&nickname='+ userInfo.nickname +
        '&register_mobile='+ userInfo.username +
        '&pay_type='+ orderList[id].pay_type +
        '&pay_at='+ orderList[id].pay_at +
        '">', '</a>',
    '</td>',
			'</tr>',
		];
		html.push(row.join(''));
	}
	return  html.join('');
}

function createConsumListHtml(ConsumList,userInfo){
	var html = [];
	for(id in ConsumList){
	var refundUrl = '';
	if (ConsumList[id].refund_coffee == 1) {
		refundUrl =  '<a class="glyphicon glyphicon-share-alt" href="javascript:void(0)" onclick="confirmRefund('+ConsumList[id].user_consume_id+')">', '</a>'
	}
var row = [
			'<tr>',
				'<td>', '<a target="_blank" href="/index.php/user-consume/view?id=' + ConsumList[id].user_consume_id + '#/detail">',ConsumList[id].user_consume_id,'</a>', '</td>',
				'<td>', '<a target="_blank" href="/index.php/order-info/view?id=' + ConsumList[id].order_id + '#/detail">',ConsumList[id].order_code,'</a>', '</td>',
                '<td>', '<a target="_blank" href="/index.php/order-info/view?id=' + ConsumList[id].order_source_id + '#/detail">',ConsumList[id].order_source_code,'</a>', '</td>',
				'<td>', ConsumList[id].fetch_time, '</td>',
				'<td>', ConsumList[id].product_name,'</td>',
				'<td>', ConsumList[id].user_consume_sugar,'</td>',
				'<td>', ConsumList[id].consume_type,'</td>',
				'<td>', ConsumList[id].actual_fee,'</td>',
				'<td>', ConsumList[id].exchange_coupon_name,'</td>',
				'<td>', ConsumList[id].building_name,	'</td>',
				'<td>', ConsumList[id].make_result,	'</td>',
				'<td>', ConsumList[id].detail_status,	'</td>',
				'<td>',
        '<a target="_blank"  class="glyphicon glyphicon-pencil" href="/index.php/service/complaint/add-complaint?user_consume_id='+ ConsumList[id].user_consume_id +
             '&order_code='+ ConsumList[id].order_code +
             '&build_id='+ ConsumList[id].build_id +
             '&user_id='+ ConsumList[id].user_id +
             '&org_id='+ ConsumList[id].org_id +
             '&nickname='+ userInfo.nickname +
             '&register_mobile='+ userInfo.username +
             '&pay_type='+ ConsumList[id].pay_type +
             '&pay_at='+ ConsumList[id].pay_at +
        '">', '</a>',
        '<a target="_blank" href="/index.php/quick-send-coupon/create?consume_id='+ ConsumList[id].user_consume_id +'&order_code='+ ConsumList[id].order_code +
             '&phone='+ userInfo.username +'">',"&nbsp;",'发券','</a>',"&nbsp;",refundUrl,
             '</td>',
			'</tr>'
		];
		html.push(row.join(''));
	}
	return html.join('');
};


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
    '<td>', '<a target="_blank" href="/service/complaint/view?id=' + complaintList[id].complaint_id + '#/detail">',complaintList[id].complaint_code,'</a>', '</td>',
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
};

<?php $this->registerJs(ob_get_clean());?>
