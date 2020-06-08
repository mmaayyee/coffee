//获取当前时间
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
    return currentdate;
}
var currentTime = getNowFormatDate();
$(function(){
    var changeDateUrl = '/distribution-daily-task/change-distribution-task-date';
    var getgetDistributionUseUrl = '/distribution-daily-task/get-distribution-user-list';

    init(rootInfoData);
    $("#save").on("click", function(){
        changeDistributionUser(this);
        $('#myModal').modal('hide')
    });
    function init(data,type) {
        console.log("data...",data);
        var taskListTpl = $("#taskListTpl").html();
        laytpl(taskListTpl).render(data, function(html){
            $("#taskList").html(html);
            if(type=="change"){
                $("#"+nowId).show();
                $("#"+changeId).show();
                $("#"+blockId).css("background","#dedede");
                var navOffset = (self==top)?-10:50;
                $("html,body",window.parent.document).scrollTop($("#"+changeId+"-top").offset().top+navOffset);
            }
            $(".details-btn").on("click", function(){
                $(this).parents(".list").find(".task-list").toggle();
            });

            //批量下发任务
            $(".all-issue").on("click", function(){
                batchAssignDistributionDailyTask(this);
            });

            //下发任务
            $(".issue").on("click", function(){
                assignDistributionDailyTask(this);
            });

            // 更换日期
            $(".change-date-right").on("click", function(){
                var  changeDate = $(this).parents(".list").next().data("date");
                changeDistributionTaskDate(this, changeDate);
                // $(this).parents(".list").next().find(".task-list").css({"display": "block"});
                console.log("change-date-right")

            });
            $(".change-date-left").on("click", function(){
                var changeDate = $(this).parents(".list").prev().data("date");

                changeDistributionTaskDate(this, changeDate);
                // $(this).parents(".list").prev().find(".task-list").css({"display": "block"});
            });

            // 更换运维人员
            $(".change-personnel").on("click", function(){
                getDistributionUserList(this);
            });
        })
        var ishide=$("body").hasClass("all-issue");
        console.log("ishide",ishide)
    }
    //批量下发日常任务
    function batchAssignDistributionDailyTask(obj){
        var orgId = $(obj).data("orgid");
        console.log("orgId...",orgId);
        var date = $(obj).data("date");
        console.log("批量下发日常任务date",date);
        $.ajax({
            url: '/distribution-daily-task/batch-assign-daily-task',
            type: "get",
            data: {date: date,orgId:orgId},
            success: function(res){
                console.log("batch-assign-daily-task res..",res);
                if(res == 1){
                    window.location.href = '/index.php/distribution-daily-task/index?1';
                }else{
                    alert('下发失败')
                }

            },
        });
    }
    var nowId,changeId,blockId;
    // 更换日期
    function changeDistributionTaskDate(obj,changeDate) {
        var date = $(obj).parents(".list").data("date");
        var orgId = $(obj).parents(".list").data("orgid");
        var userId = $(obj).parents(".list").data("userid");
        var buildId = $(obj).parent().data("buildid");
        console.log("更换日期",orgId,userId,date,changeDate,buildId);
        $.ajax({
            url: "/distribution-daily-task/change-distribution-task-date",
            type: "get",
            data: {org_id: orgId, userid: userId, before_date: date, after_date: changeDate, build_id: buildId},
            success: function(res){
                //console.log(res);
                if(res == 1){
                    $.ajax({
                        url:'/distribution-daily-task/ajax-daily-task-data',
                        type: 'get',
                        data: {org_id: orgId},
                        datatype: JSON,
                        success: function(res){
                            var result = JSON.parse(res);
                            console.log("ajax-daily-task-data..",result);
                            nowId = userId+"-"+date;
                            changeId = userId+"-"+changeDate;
                            blockId = userId+"-"+changeDate+"-"+buildId;
                            console.log("nowId..",nowId," changeId..",changeId);
                            init(result,"change");
                        }
                    });
                }else{
                    alert('更改日期失败');
                }
            },
        });
    }
    //获取运维人员列表
    function getDistributionUserList(obj) {
        var date = $(obj).parents(".list").data("date");
        var orgId = $(obj).parents(".list").data("orgid");
        var userId = $(obj).parents(".list").data("userid");
        var buildId = $(obj).parents("p").data("buildid");
        var userlistHtml = "";
        $.ajax({
            url: "/distribution-daily-task/get-distribution-user-list",
            type: "get",
            data: {org_id: orgId, date: date, userid: userId},
            success: function(res){
                var userList = JSON.parse(res);
                if (userList.distributionUserList.length == 0) {
                    alert('没有可转交的运维人员');
                    return false;
                }
                userlistHtml += '<select class="form-control" data-orgid="'+ orgId +'" data-date="'+ date +'" data-build="'+ buildId +'">';
                $.each(userList.distributionUserList, function(index,item){
                    userlistHtml += '<option value="'+ item.userid +'">'+ item.name +'</option>';
                });
                userlistHtml += '</select>';
                $("#myModal").modal();
                $("#myModal .modal-body").html(userlistHtml);
            },
        });
    }

    //修改运维人员
    function changeDistributionUser(obj){
        var date = $("#myModal").find("select").data("date");
        var orgId = $("#myModal").find("select").data("orgid");
        var buildId = $("#myModal").find("select").data("build");
        var userId = $("#myModal").find("select").val();
        //console.log("修改运维人员",orgId,userId,date,buildId);
        $.ajax({
            url: '/distribution-daily-task/save-distribution-user',
            type: "get",
            data: {org_id: orgId, date: date, build_id: buildId, userid: userId},
            success: function(res){
                if(res == 1){
                    alert('更改人员成功');
                    window.location.reload();
                }else{
                    alert('更改人员失败');
                }
            }
        });
    }
});
