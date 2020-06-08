var CIMG = (function(win){
    'use strict';

    var cvsd = null;
    var imgW = 640;
    var imgH = 330;
    var p = 100;
    var w = 0;
    var type = 1;

    var chuliImg = function(){
        type == 1 ? drawImg() :
        type == 2 ? drawImg2() : '';
    };
    var getImgDom = function(){
        return cvsd.siblings('img').get(0);
    };
    var px = function(r){
        return .05 * w * r;
    };
    var canvasWH = function(p, m){
        var img = getImgDom();
        var r = img.width / img.height;
        var cw = (w - px(m * 2)) * p / 100;
        var h = cw / r;
        return {width: cw, height: h};
    };
    var drawImg = function() {
        var cvs = cvsd.get(0);
        var wh = canvasWH(p, .69);
        var _w = wh.width;
        var _h = wh.height;
        $(cvs).attr('width', _w);
        $(cvs).attr('height', _h);
        var ctx = cvs.getContext('2d');
        ctx.drawImage(getImgDom(), 0, 0, _w, _h);

        try {
            var imgData = ctx.getImageData(_w*530/imgW, _h*200/imgH, _w*80/imgW, _w*80/imgW);
            for(var i = 0; i < imgData.data.length; i+=4) {
                imgData.data[i] = 0;
                imgData.data[i+1] = 0;
                imgData.data[i+2] = 0;
            }
            ctx.putImageData(imgData, _w*530/640, _h*200/330);
        } catch(e){}
    };
    var drawImg2 = function() {
        var cvs = cvsd.get(0);
        var wh = canvasWH(p, 0);
        var _w = wh.width;
        var _h = wh.height;
        $(cvs).attr('width', _w);
        $(cvs).attr('height', _h);
        var ctx = cvs.getContext('2d');
        ctx.drawImage(getImgDom(), 0, 0, _w, _h);

        try {
            var imgData = ctx.getImageData(0, 0, _w, _w);
            for(var i = 0; i < imgData.data.length; i+=4) {
                imgData.data[i+3] = imgData.data[i+3] >= 244 ? imgData.data[i+3] : 0;
            }
            ctx.putImageData(imgData, 0, 0);
        } catch(e){}
    };
    return {
        init: function($cvs){ cvsd = $cvs; w = $(win).width(); win['CIMG'] = this;},
        set per(p) { p = p;},
        set invokeType(t) { type = t; },
        invoke: function(){ chuliImg(); }
    };
})(window || {});