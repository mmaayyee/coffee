$(function(){
	grindTypeFun()
	$('#grindsearch-grind_type').change(function(){
		grind_type = $(this).val()
		grindTypeFun();
	})
	function grindTypeFun(){
		if(grind_type == 1){
			$('.buildSearch').hide()
			$('.orgClass').show()
		}else if(grind_type == 2){
			$('.buildSearch').show()
			$('.orgClass').hide()
		}else{
			$('.buildSearch').hide()
			$('.orgClass').hide()
		}
	}
})