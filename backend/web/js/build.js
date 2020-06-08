$(function(){
    // 默认
    $('#city_china').cxSelect({
        selects: ['province', 'city', 'area'],
    });
    var geocoder,map,marker = null;
    map = new qq.maps.Map(document.getElementById('allmap'),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,        
        disableDefaultUI: true
    });
    
    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            if (marker)
                marker.setMap(null);
            map.setCenter(result.detail.location);
            map.zoomTo(15);
            marker = new qq.maps.Marker({
                map:map,
                draggable: true,
                position: result.detail.location
            });
            $('#lng').val(result.detail.location.lng);
            $('#lat').val(result.detail.location.lat);
            qq.maps.event.addListener(marker, 'dragend', function() {
                var dragend_lng=marker.getPosition().lng.toFixed(8);
                var dragend_lat=marker.getPosition().lat.toFixed(8);
                $('#lng').val(dragend_lng);
                $('#lat').val(dragend_lat); 
                // 经纬度解析地址
                // var latLng1 = new qq.maps.LatLng(dragend_lat,dragend_lng);
                // geocoder.getAddress(latLng1);
                // geocoder.setComplete(function(result) {
                //  console.log(result.detail.address);
                // })  
            }); 
        }        
    });  
    if ($('#lat').val() && $('#lng').val()) {
        var newCenter = new qq.maps.LatLng($('#lat').val(), $('#lng').val());
        map.setCenter(newCenter);
        map.zoomTo(15);
        marker = new qq.maps.Marker({
            position: newCenter,
            draggable: true,
            map: map
        });
        qq.maps.event.addListener(marker, 'dragend', function() {
            var dragend_lng=marker.getPosition().lng.toFixed(8);
            var dragend_lat=marker.getPosition().lat.toFixed(8);
            $('#lng').val(dragend_lng);
            $('#lat').val(dragend_lat); 
            // 经纬度解析地址
            // var latLng1 = new qq.maps.LatLng(dragend_lat,dragend_lng);
            // geocoder.getAddress(latLng1);
            // geocoder.setComplete(function(result) {
            //  console.log(result.detail.address);
            // })  
        }); 
    }
    $("#lng").bind("blur",function(){   
        var latlng2 = new qq.maps.LatLng(parseFloat($('#lat').val()),parseFloat($('#lng').val()));
            map.setCenter(latlng2);    
            map.setZoom(15);
            marker.setPosition(latlng2);
    });
    $("#lat").bind("blur",function(){
        var latlng3=new qq.maps.LatLng(parseFloat($('#lat').val()),parseFloat($('#lng').val()));            
        map.setCenter(latlng3);    
        map.setZoom(15);
        marker.setPosition(latlng3);
    });      
    $("#building-address").blur(function(){
        // 将地址解析结果显示在地图上,并调整地图视野
        var buildAddress = '', 
            province = $('#building-province').val(), 
            city = $('#building-city').val(), 
            area = $('#building-area').val(), 
            address = $('#building-address').val();
            // name = $('#building-name').val();
        buildAddress += province ? province : ''; 
        buildAddress += city ? city : ''; 
        buildAddress += area ? area : ''; 
        buildAddress += address ? address : ''; 
        // buildAddress += name ? name : ''; 
        geocoder.getLocation(buildAddress);
    });
});
