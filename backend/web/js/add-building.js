$(function(){
	var buildingData = null;
	var buildingArr = [];
	var addtpl = document.getElementById("building_add_template").innerHTML;
    var deltpl = document.getElementById("building_del_template").innerHTML;
	var getSearchResults=function(page){
			var pageSize = 5;//每页展示的条数
			var buildingName = $("input[name='buildingName']").val();
			var buildingType = $("select[name='buildingType']").val();
			var branch = $("select[name='branch']").val();
            var agent = $("select[name='agent']").val();
			var equipmentType = $("select[name='equipmentType']").val();
            if (agent) {
                branch = agent;
            }
			$.ajax({
				 type: "POST",
	             url: "/coupon-send-task/get-build-list",
				 data: { "name":buildingName,"build_type":buildingType,"org_id":branch,"equipmentType":equipmentType,"page":page,"pageSize":pageSize,"build_status":3, "equipmentOnline":0},
	             dataType: "json",
	             success: function(data){
					if (data) {
						if (data.totalCount >0) {
						    $(".block-a .no-data").hide();
                            $(".block-a .searchResult").show();
    						buildingData = data.buildArr;
							$(".searchResult tbody").html("");
							laytpl(addtpl).render(buildingData,function(html){
								$(".searchResult tbody").html(html);
							});
							 initButStatus();
							var counts=data.totalCount;
							paging(counts, page, pageSize);
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
    $("#searchResult").on("click",function(){
        getSearchResults(1);
        $("#addAll").removeAttr("disabled");
    });
	//批量添加
	$("#batchAdd").on("click",function(){
		var html = null;
		$(".searchResult tbody tr").each(function(){
            if ($(this).find("button").attr("disabled")!="disabled") {
                var tr = "<tr>"+$(this).html()+"</tr>";
                html += tr;
            }
		});
		$(".addPreview table").find("tbody").append(html);
		$(this).parents(".searchResult table").find("button").attr("disabled",true);
		$(".addPreview tbody").find("button").removeClass("add").addClass("delete");
		$(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        $(".addPreview tbody").find("input[type=hidden]").prop("disabled",false).attr("name","buildingIdArr[]");
		$(".allDelete,.addPreview .btn-success").removeAttr("disabled");
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
            $(".addPreview tbody").find("input[type=hidden]").prop("disabled",false).attr("name","buildingIdArr[]");
			deleteItem();
			$(".allDelete,.addPreview .btn-success").removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
				$("#batchAdd").attr("disabled",true);
			}else{
				$("#batchAdd").removeAttr("disabled");
			}
		});
	}
	//批量删除
	$(".allDelete").on("click",function(){
		$(this).parents(".addPreview").find("tbody").html("");
		$(".searchResult table").find("button").removeAttr("disabled");
		$(this).attr("disabled",true);
		$(".addPreview .btn-success").attr("disabled",true);
		$("#addAll").removeAttr("disabled");
	});
	function deleteItem(){
		$(".delete").on("click",function(){
			$(this).parent().parent().remove();
			buttonStatus(this);
			$("#addAll").removeAttr("disabled");
		});
	}
	function buttonStatus(obj){
		if($(".addPreview tbody").find("tr").length>0){
			var val=$(obj).prev().val();
			$(".searchResult input[value="+val+"]").next().removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
			    $("#batchAdd").attr("disabled",true);
			}else{
                $("#batchAdd").attr("disabled",false);
			}
		}else{
			$(".searchResult table").find("button").removeAttr("disabled");
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
		if($(".searchResult .add").length == $(".searchResult .add:disabled").length){
				$("#batchAdd").attr("disabled",true);
		}else{
			$("#batchAdd").removeAttr("disabled");
		}
    }
    //全部添加
    $("#addAll").on("click", function() {
        var _this = $(this);
        var buildingName = $("input[name='buildingName']").val();
        var buildingType = $("select[name='buildingType']").val();
        var branch = $("select[name='branch']").val();
        var equipmentType = $("select[name='equipmentType']").val();
        $.ajax({
            type: "post",
            url: "/coupon-send-task/get-all-building-by-condition",
            data: { "name":buildingName,"build_type":buildingType,"org_id":branch,"equipmentType":equipmentType},
            dataType: "json",
            success: function(data){
                if (data) {
                    $(".addPreview tbody").html("");
                    buildingArr = buildingArr.concat(data);
                    buildingArr = uniqueArray(buildingArr, "id");
                    _this.attr("disabled",true);
                    initAddPreview(buildingArr);
                }
            },
        });
    });
    if(editBuildingList.length > 0){
        buildingArr = editBuildingList;
        initAddPreview(buildingArr)
    }
    function initAddPreview(buildingDate){
        laytpl(deltpl).render(buildingDate, function(html){
            $(".addPreview tbody").html(html);
        });
        $(".addPreview tbody").find("button").removeClass("add").addClass("delete")
        $(".addPreview .allDelete").removeAttr("disabled");
        $(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        $(".searchResult table").find("button").attr("disabled",true);
        deleteItem();
    }
})
