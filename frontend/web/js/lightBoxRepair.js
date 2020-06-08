$(function(){
    var address = $("#address").html(),
        geocoder,map,marker = null;
    map = new qq.maps.Map(document.getElementById('allmap'),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,
        disableDefaultUI: true
    });

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    geocoder.getLocation(address);

    //点击任务打卡
    $(".btn-lg").click(function(){
        var id = $(this).data('id');
        if ($(this).data("type") < 9){
            $.get(
                "change-process-result",
                {
                    id:id, 
                    process_result:$(this).data('type')
                },
                function(data){
                    if (data == 0) {
                        window.location.href = '/light-box-repair/detail?id='+id
                    }
                }
            );
        }
    });
});