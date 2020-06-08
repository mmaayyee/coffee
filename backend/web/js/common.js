// 防止重复提交
$().ready(function(){
    $('form').on('afterValidate', function (event, messages, errorAttributes) {
        if (errorAttributes.length>0) {
            $(':submit').removeAttr('disabled');
        }
    });
    $('form button[type=submit], form input[type=submit]').click(function(){
        $(this).attr('disabled','disabled');
        $(this).parents('form').submit();
        return false;
    })
})

/*
 * JSON数组去重
 * @param: [array] json Array
 * @param: [string] 唯一的key名，根据此键名进行去重
 */
function uniqueArray(array, key){
    var result = [array[0]];
    for(var i = 1; i < array.length; i++){
        var item = array[i];
        var repeat = false;
        for (var j = 0; j < result.length; j++) {
            if (item[key] == result[j][key]) {
                repeat = true;
                break;
            }
        }
        if (!repeat) {
            result.push(item);
        }
    }
    return result;
}