$(function(){
	var buildingData = null;
	var getSearchResults=function(page){
			var pageSize = 20;//每页展示的条数
			var buildingName = $("input[name='buildingName']").val();
			var buildingType = $("select[name='buildingType']").val();
			var branch = $("select[name='branch']").val();
			var agent = $("select[name='agent']").val();
			var partner = $("select[name='partner']").val();
			var equipmentType = $("select[name='equipmentType']").val();
			var programID = $("#programID").val();
			var selectType= $("#selectType").val();
			$.ajax({
				 type: "POST",
	             url: "/light-program-assoc/get-search-build",
				 data: {"selectType":selectType, "programID":programID, "buildingName":buildingName,"buildingType":buildingType,"branch":branch,"agent":agent,"partner":partner,"equipmentType":equipmentType,"page":page,"pageSize":pageSize},
	             dataType: "json",
	             success: function(data){
					if(data){
						if(data.totalCount > 0){
						    $(".block-a .no-data").hide();
                            $(".block-a .searchResult").show();
						    buildingData = data.buildArr;
						    var gettpl = document.getElementById("building_template").innerHTML;
							$(".searchResult tbody").html("");
							laytpl(gettpl).render(buildingData,function(html){
								$(".searchResult tbody").html(html);
							});
							 initButStatus();
							var counts=data.totalCount;
							paging(counts,page,pageSize);
							$(".searchResult .SortId").each(function(index, value) {
							    var serialNumber = (parseInt(page) - 1) * parseInt(pageSize) + parseInt(index) + 1;
							    $(this).attr("data-text", serialNumber);
							})
						} else {
						    $(".block-a .searchResult").hide();
                            $(".block-a .no-data").show();
					    }
	                }
			    }
			});
	}
	//楼宇搜索结果分页
	function paging(counts,page,pageSize){
		var pagecount= counts % pageSize == 0 ? counts / pageSize:counts/pageSize+1;
		var laypages = laypage({
		    cont: $(".searchResult .pages"),
		    pages: pagecount, //通过后台拿到的总页数
            curr: page,
            hash: true,
            first: false,
            last: false, //将尾页显示为总页数。若不显示，设置false即可
            prev: '&laquo;', //若不显示，设置false即可
            next: '&raquo;', //若不显示，设置false即
		    jump: function(obj,first){
		    	if(!first){
		    		getSearchResults(obj.curr);
		    		window.location.hash = "#searchResult";
		    	}
		    }
		})
	}
	//搜索
    getSearchResults(1);
    $(".search").on("click",function(){
        getSearchResults(1);
    });
	//批量添加
	$(".allAdd").on("click",function(){
		var html = null;
		$(".searchResult tbody tr").each(function(){
            if ($(this).find("button").attr("disabled")!="disabled") {
                var tr = "<tr>"+$(this).html()+"</tr>";
                html += tr;
            }
		});
		$(".addPreview table").find("tbody").append(html);
		$(this).parents(".searchResult").find("button").attr("disabled",true);
		$(".addPreview tbody").find("button").removeClass("add").addClass("delete");
		$(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
		$(".allDelete,.btn-success").removeAttr("disabled");
		deleteItem();
	});
	function addBuilding(){
		$(".add").on("click",function(){
			var buildingItem = $(this).parent().parent("tr").html();
			var html="<tr>"+buildingItem+"</tr>";
			$(".addPreview table").find("tbody").append(html);
			$(this).attr("disabled",true);
			$(".addPreview tbody").find("button").removeClass("add").addClass("delete");
			$(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
			deleteItem();
			$(".allDelete,.btn-success").removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
				$(".allAdd").attr("disabled",true);
			}else{
				$(".allAdd").removeAttr("disabled");
			}
		});
	}
	//批量删除
	$(".allDelete").on("click",function(){
		$(this).parents(".addPreview").find("tbody").html("");
		$(".searchResult").find("button").removeAttr("disabled");
		$(this).attr("disabled",true);
		$(".btn-success").attr("disabled",true);
	});
	function deleteItem(){
		$(".delete").on("click",function(){
			$(this).parent().parent().remove();
			buttonStatus(this);
		});
	}
	function buttonStatus(obj){
		if($(".addPreview tbody").find("tr").length>0){
			var val=$(obj).prev().val();
			$(".searchResult input[value="+val+"]").next().removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
			    $(".allAdd").attr("disabled",true);
			}else{
                $(".allAdd").attr("disabled",false);
			}
		}else{
			$(".searchResult").find("button").removeAttr("disabled");
			$(".addPreview").find("button").attr("disabled",true);
		}
	}
    function initButStatus(){
		addBuilding();
		$(".addPreview .delete").each(function(){
			var val=$(this).prev().val();
			if($(".searchResult input[value="+val+"]")){
				$(".searchResult input[value="+val+"]").next().attr("disabled",true);
			}
		});
		if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
				$(".allAdd").attr("disabled",true);
		}else{
			$(".allAdd").removeAttr("disabled");
		}
    }
})
