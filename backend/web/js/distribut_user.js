$(function(){
    $(".field-distributionuser-leader_id").hide();
    if ($("#distributionuser-is_leader").val() == 2) {
        $(".field-distributionuser-leader_id").show();
    } else {
        $("#distributionuser-leader_id").val('');
        $(".field-distributionuser-leader_id").hide();
    }
    $("#distributionuser-is_leader").change(function(){
        if ($(this).val() == 2) {
            $(".field-distributionuser-leader_id").show();
        } else {
            $("#distributionuser-leader_id").val('');
            $(".field-distributionuser-leader_id").hide();
        }
    })
})