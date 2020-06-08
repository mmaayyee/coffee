$(function(){
    $('.day-num').click(function(){
        var data = $('#day-num').serialize();
        console.log(data);
        editTaskSetting(data);
    })
    $('.refuel_cycle').click(function(){
        var data = $('#refuel_cycle').serialize();
        console.log(data);
        editTaskSetting(data);
    })
    $('.cleaning_cycle').click(function(){
        var data = $('#cleaning_cycle').serialize();
        console.log(data);
        editTaskSetting(data);
    })
})

function editTaskSetting(data){
    $.post(
        '/distribution-daily-task/setting',
        data,
        function(res) {
            if (res == 0) {
                $('.close').trigger('click');
            } else {
                alert('数据更新失败');
            }
        }
    );
}