var Tip = (function() {
    var $tip = null;
    var align = 'center';
    var t = null;
    var btnObj = {};

    var init = function($e) {
        $tip = $e;
    };
    var fobidengBodyMove = function(e) {
        e.preventDefault();
    };
    var setAlign = function() {
        $tip.find('.tip-cont').css('text-align', align);
    };
    var hide = function() {
        $tip.css('display', 'none');
        $('body').off('touchmove', fobidengBodyMove);
    };
    var openWin = function(hcont) {
        setAlign();
        $tip.css('display', 'table');
        $tip.find('.tip-txt').html(hcont);
        $('body').on('touchmove', fobidengBodyMove);
    };
    var open2closeLater = function(hcont, ms) {
        if(t != null) return;
        openWin(hcont);
        t = window.setTimeout(function(){
            hide();
            window.clearTimeout(t);
            t = null;
        }, ms);
    };
    var closeNow = function() {
        hide();
    };
    var addBtn = function(id, h, cb, arg) {
        var btn = $(h);
        btn.addClass('gn');
        $tip.find('.tip-btn').append(btn);
        if(cb) {
            btn.on('tap', function(evt) {
                cb(arg);
            });
        }
        btn.hide();

        btnObj[id] = btn;
    };
    var addBtnTapEvent = function(id, handler) {
        var btn = getBtn(id);
        btn.on('tap', function(evt){
            handler && handler();
        });
    };
    var getBtn = function(id){
        return btnObj[id];
    };
    return {
        View: {
            showBtn: function(id){
                getBtn(id) && getBtn(id).show();
            },
            addBtn: function(id, h, cb, arg) { addBtn(id, h, cb, arg); },
            addBtnTapEvent: function(id, handler) { addBtnTapEvent(id, handler); }
        },
        init: function($e) { init($e); },
        setAlign: function(a) { align = a; },
        openWin: function(txt) { openWin(txt);},
        closeNow: function() { closeNow(); },
        open2closeLater: function(txt, ms) { open2closeLater(txt, ms); }
    };
})();