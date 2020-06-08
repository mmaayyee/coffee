function validateTimePeriod(begin, end) {

	if(!(begin instanceof jQuery)) {
		begin = $(begin);
	}
	if(!(end instanceof jQuery)) {
		end = $(end);
	}

	var beginString = new String(begin.val());
	var endString = new String(end.val());

	if(!(beginString == null || beginString == '') &&
		!(endString == null || endString == '')) {
		// alert(beginString instanceof String); //JavaScripy判断一个对象是否是String类型  
		// alert(typeof beginString); //typeof String 类型 返回的是 Object  

		// //转换为JavaScript日期类型  
		// var bArray = beginString.split(/[- :]/);  
		// var beginTime = new Date(bArray[0], bArray[1]-1, bArray[2],  
		// bArray[3], bArray[4]);  
		// var eArray = endString.split(/[- :]/);  
		// var endTime= new Date(eArray[0], eArray[1]-1, eArray[2], eArray[3],  
		// eArray[4]);  

		var beginTime = new Date(beginString);
		var endTime = new Date(endString);

		if(beginTime <= endTime) {
			return true;
		} else {
			return false;
		}
	}
	return true;
} 
 $(".btn-primary").click(function(){  
	//验证是否起始时间小于等于截至时间  
    var result = validateTimePeriod($("#startDate"), $("#endDate"));
    if(result != true) {
    	$("#endDate").next(".error").show().text('*结束时间不能小于开始时间！');
    	return false;
    } else {
    	$("#endDate").next(".error").hide();
    }
}); 
