<?php

/* @var $this yii\web\View */
/* @var $model app\models\Equipments */

// $this->title                   = $model->building;
$this->title                   = '远程开门';
$this->params['breadcrumbs'][] = ['label' => '设备管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .center-block {
        margin-top: 5%;
        width: 50%;
    }

    .page-header {
        border: none;
    }

    h2 {
        margin: 0;
        border: 1px solid #ccc;
        padding: 3% 0;
        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;
        border-bottom: none;
    }

    h1 {
        margin: 3% 0;
    }

    .borders {
        border: 1px solid #ccc;
    }

    .borders button {
        margin: 3% 0 5%;
        display: inline-block;
        width: 25%;
        color: #fff;
    }

    .borders button:active {
        outline: none;
        border: none;
    }

    .borders button:first-child {
        margin-right: 3%;
    }

    .grays {
        background-color: gray;
    }

    .warn {
        background-color: #f0ad4e;
        border-color: #f0ad4e;
    }
</style>
<div class="center-block text-center">
    <h2 class="text-center">远程开门</h2>
    <div class="borders">
        <div class="page-header text-center">
            <h1 class="message text-primary"></h1>
        </div>


        <div class="form-inline text-center">
            <button type="button" class="btn btn-lg opendoor" value='opendoor' onclick='opendoor()' disabled="true">开门</button>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/jquery-2.0.0.min.js"></script>
<script>
    var wsServer = '<?=$socket;?>';
    if (!wsServer) {
        errorMessage();
    }
    var websocket = new WebSocket(wsServer);
    websocket.onopen = function(evt) {
        console.log("Connected to WebSocket server.");
        websocket.send('{"source":"brower","action":"connect","socketServer":"<?php echo $socketServer; ?>","data":{"equipcode":"<?=$model->equip_code;?>"}}');
    };
    websocket.onclose = function(evt) {
        console.log("Disconnected");
    };
    websocket.onmessage = function(evt) {
        var result = JSON.parse($.trim(evt.data));
        if (result && result.code) {
            if (result.code == "fail") {
                return errorMessage();
            }
        }
        if (result && result.data && result.data.open) {
            if (result.data.open == "true") {
                $(".message").html("开门成功!");
                return;
            } else {
                $(".message").html("开门失败!");
                return;
            };
        };
        if (result && result.action) {
            switch (result.action) {
                case 'connect':
                    if (result.code == "fail") {
                        return errorMessage();
                    } else {
                        $(".opendoor").removeAttr("disabled");
                        $(".message").html("设备连接成功!");
                        $(".opendoor").addClass("warn");
                        $(".opendoor").removeClass("grays");
                    }
                    break;
            }
        }
    };
    websocket.onerror = function(evt, e) {
        console.log('Error occured: ' + evt.data);
        errorMessage();
        return;
    };

    websocket.send('{"source":"brower","action":"connect","data":{"equipcode":"<?=$model->equip_code;?>"}}');

    function opendoor() {
        websocket.send('{"source":"brower","action":"open","data":{"equipcode":"<?=$model->equip_code;?>"}}');
    }
    function errorMessage(){
        $(".message").html("设备未连接!");
        $(".opendoor").removeClass("warn");
        $(".opendoor").addClass("grays");
        $(".opendoor").attr("disabled", "disabled");
    }
</script>