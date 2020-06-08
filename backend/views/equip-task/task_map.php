<?php
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    var center = new qq.maps.LatLng('.$lat.', '.$lng.');
    var map = new qq.maps.Map(
        document.getElementById("map"),
        {
            center: center,
            zoom: 18
        }
    );
    var marker = new qq.maps.Marker({
        position: center,
        map: map
    });
');
?>
<p><a href="javascript:history.go(-1)" class="btn btn-success">返回上一页</a></p>
<div id="map" style="width: 100%;height: 500px;"></div>