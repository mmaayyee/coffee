/**
 * Created by wangxl on 17/6/30.
 */
$(function() {

    var buildId = $('#materialsafevalue-build_id').val();
    var url = $('#materialsafevalue-build_id').data('url');
    if(buildId){
        $.get(
            url,
            {'build_id': buildId},
            function (data) {
                if (data.length != 0) {
                    $('#equipment_id').val(data.equipment_id);
                    var materialTpl = $('#material_tpl').html();
                    console.log(materialTpl);
                    laytpl(materialTpl).render(data,function(html){
                        $('#material_id').html(html);
                    });
                    $('#material_id').validation();
                } else {
                    $('#equipment_id').val('');
                }
            },
            'json'
        );
    }

    $('#materialsafevalue-build_id').change(function() {
        var buildId = $('#materialsafevalue-build_id').val();
        var url = $('#materialsafevalue-build_id').data('url');
        $.get(
            url,
            {'build_id': buildId},
            function (data) {
                if (data.length != 0) {
                    $('#equipment_id').val(data.equipment_id);
                    var materialTpl = $('#material_tpl').html();
                    console.log(materialTpl);
                    laytpl(materialTpl).render(data,function(html){
                        $('#material_id').html(html);
                    });
                    $('#material_id').validation();
                } else {
                    $('#equipment_id').val('');
                }
            },
            'json'
        );
    });
})

function checkSubmit(){
    if($('form').valid()){
        $('form').submit();
    }
}
