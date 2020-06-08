$(function(){
	$(".delete-btn").on("click",function(){
		//console.log("1");
		var  materialNumber = Number($(this).next("input").val());
		if(materialNumber>0){
			materialNumber--;
		}
		$(this).next("input").val(materialNumber);
	});
	$(".add-btn").on("click",function(){
		var  materialNumber = Number($(this).prev("input").val());
		if(materialNumber<99999){
			materialNumber++;
		}
		$(this).prev("input").val(materialNumber);
	});
	$("#saveForm").on("click", function(){
		console.log("提交");
	});
});
