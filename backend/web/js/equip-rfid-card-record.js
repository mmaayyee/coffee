$("#search").on("click",function(){
    $("#equipRfidCardRecordForm").attr("action","index");
    $("#equipRfidCardRecordForm").submit();
})
$("#export").click(function(){
	if(!confirm('确认导出开门记录？')){
		return false;
	}
    $("#equipRfidCardRecordForm").attr("action","export");
    $("#equipRfidCardRecordForm").submit();
});