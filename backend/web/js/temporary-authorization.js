$("#search").on("click",function(){
    $("#temporaryAuthorizationForm").attr("action","index");
    $("#temporaryAuthorizationForm").submit();
})
$("#export").click(function(){
	if(!confirm('确认导出临时开门记录？')){
		return false;
	}
    $("#temporaryAuthorizationForm").attr("action","export");
    $("#temporaryAuthorizationForm").submit();
});