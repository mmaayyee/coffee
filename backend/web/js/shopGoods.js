/**
 * Created by wangxl on 17/12/25.
 */
$(function(){
    $("#update_goods").on('click',function(){
        var checkList = $("input[name='selection[]']:checked");
        if(checkList.length !== 1){
            alert('请选中一项进行操作');
        }

        console.log(checkList.attr('value'));

        //$("input[name='selection[]']:checked").each(function(){
        //    console.log(this.value);
        //});
    });

})