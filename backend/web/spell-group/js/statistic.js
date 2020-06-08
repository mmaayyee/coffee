//页面初始化数据
var rankUrl="";//排名url
var detailUrl="";//单团url
init();
function init(){
	//总数
	var data=rootData.activity_num;
	$(".all-sum").html(data.sum);
	$(".old-sum").html(data.old_and_new);//老带新
	$(".new-sum").html(data.novice_group);//新手团
	$(".quan-sum").html(data.dream_on_wheels);//全民参与
	// //上线活动
	$(".on-all").html(data.online_activity);
	$(".on-old").html(data.online_old_and_new);//老带新
	$(".on-new").html(data.online_novice_group);//新手团
	$(".on-quan").html(data.online_dream_on_wheels);//全民参与
	// //下线活动
	$(".out-all").html(data.offline_activities);
	$(".out-old").html(data.offline_old_and_new);//老带新
	$(".out-new").html(data.offline_novice_group);//新手团
	$(".out-quan").html(data.offline_dream_on_wheels);//全民参与

	// 活动名称
	getName(rootData.name);
	// 排名
	rank(rootData.ranking);
	//单团
	detail(rootData.single);
}

// 排名搜索
$(".rank-wrap .search").on("click",function(){
	var  date=$("#rankTime").val();
	var  type=$("#rankType").val();
	rankUrl=rootCoffeeStieUrl+"/group-booking-api/get-ranking.html";
	rankquest(date,type,"")
})
//排名导出
$(".rank-wrap .export").on("click",function(){
	var  date=$("#rankTime").val();
	var  type=$("#rankType").val();
	rankUrl="get-ranking";
	rankquest(date,type,"1")
})
//排名请求数据
function rankquest(date,type,educe){
	console.log({date:date,type:type,educe:educe})
	$.ajax({
	  url: rankUrl,
	  data:{date:date,type:type,educe:educe},
	  type: 'get',
	  success:function(data){
	  	if(educe==""){
	  		rank(JSON.parse(data).data)
	  	}else{
	  		window.location.href="get-ranking?date="+date+"&&type="+type+"&&educe="+educe
	  	}
	  },
	  fail:function(){
	  	alert("网络异常，请稍后再试")
	  }
	})
}
//排名渲染
function rank(data){
	if(data.length>0){
		$(".rank-wrap .noData").hide();
		var rankStr=""
		for(var i=0; i<data.length; i++){
			rankStr+="<tr><td>"+data[i].main_title+"</td><td>"+parseInt(data[i].heat_sort+1)+"</td><td>"+data[i].heat+"</td><td>"+parseInt(data[i].frequency_sort+1)+"</td><td>"+data[i].frequency+"</td><td>"+parseInt(data[i].pull_the_new_sort+1)+"</td><td>"+data[i].pull_the_new+"</td></tr>";
		}
		$("#rank tbody").html(rankStr);
	}else{
		$("#rank tbody").html("");
		$(".rank-wrap .noData").show();
	}
}

//单团搜索
$(".detail-wrap .search").on("click",function(){
	detailUrl=rootCoffeeStieUrl+"/group-booking-api/get-single.html";
	detailData("")
})
//单团导出
$(".detail-wrap .export").on("click",function(){
	detailUrl="get-single";
	detailData("1")
})
function detailData(educe){
	var  b_time=$("#rankBeginTime").val();
	var  e_time=$("#rankEndTime").val();
	var  type=$("#detailType").val();
	var  group_id=$("#detailName").val();
	var  btime=new Date(b_time).getTime();
	var  etime=new Date(e_time).getTime();
	if(etime-btime<0){
		alert("上线结束时间要大于开始时间")
	}else{
		detailquest(b_time,e_time,type,group_id,educe)
	}
}
//单团请求数据
function detailquest(btime,etime,type,id,educe){
	console.log({begin_time:btime,end_time:etime,type:type,group_id:id,educe:educe})
	$.ajax({
	  url: detailUrl,
	  data:{begin_time:btime,end_time:etime,type:type,group_id:id,educe:educe},
	  type: 'get',
	  success:function(data){
	  	if(educe==""){
	  		detail(JSON.parse(data).data)
	  		console.log("data",data)
	  	}else{
	  		window.location.href="get-single?begin_time="+btime+"&&end_time="+etime+"&&type="+type+"&&group_id="+id+"&&educe="+educe;
	  	}
	  },
	  fail:function(){
	  	alert(data.message)
	  }
	})
}
//单团渲染
function detail(data){
	if(data.length>0){
		$(".detail-wrap .noData").hide();
		var detailStr=""
		for(var i=0; i<data.length; i++){
			detailStr+="<tr><td>"+data[i].main_title+"</td><td>"+data[i].Initiating_user_num+"</td><td>"+data[i].sponsor_dumpling_num+"</td><td>"+data[i].succeed_dumpling_num+"</td><td>"+data[i].pull_new_num+"</td><td>"+data[i].sales_volume+"</td><td>"+data[i].saleroom+"</td><td>"+data[i].date+"</td></tr>";
		}
		$("#detailData tbody").html(detailStr)
	}else{
		$("#detailData tbody").html("")
		$(".detail-wrap .noData").show();
	}
}

// 活动名称渲染
function getName(data){
	var activity_name=""
	for(var i=0; i<data.length; i++){
		activity_name+="<option value="+data[i].group_id+">"+data[i].main_title
+"</option>"
	}
	$("#detailName").append(activity_name)
}