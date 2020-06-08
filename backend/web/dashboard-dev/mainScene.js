/* eslint-disable */
export default {
    create: function() {
        // websocket
        this.websocket = new WebSocket('wss://sockettest.coffee08.com/socket');
        this.websocket.onopen = (evt) => {
            window.console.log('websocket已连接');
            this.websocket.send('{"source":"server","action":"remote_debug_connection"}');
        };
        //WebSocket连接关闭回调方法
        this.websocket.onclose = (evt) => {
            window.console.log('close');
        };
        websocket.onmessage = (evt) => {
            var result = JSON.parse(evt.data);
            window.console.log('websocket.onmessage..',result);
        }
        // main
        this.mainLayer = game.add.image();
        game.world.addChild(this.mainLayer);
        let layer = this.mainLayer;
        let bg = game.add.image(0, 0, 'bg_bg');
        layer.addChild(bg);

        let p1 = game.add.image(10, 800, 'p1');
        p1.alpha = 0;
        layer.addChild(p1);

        // game.add.tween(p1).to({
        //     alpha: 1,
        //     y: 300
        // }, 1500, Phaser.Easing.Cubic.InOut)
        // .to({
        //     alpha: 0,
        //     y: 800
        // }, 1500, Phaser.Easing.Cubic.InOut).start().loop();

        // let banner = game.add.image(168, 100, 'qr-code');
        // banner.alpha = 0;
        // banner.scale.setTo(0.75);
        // layer.addChild(banner);

        // game.add.tween(banner).to({
        //     alpha: 1,
        //     y: 200
        // }, 1500, Phaser.Easing.Cubic.InOut).start();
    },
    update: function() {
        // window.console.log('update')
    }
};