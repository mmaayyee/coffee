var JS = (function(){
    var $js = null;
    var isUp = true;
    var isHide = false;
    var isAjaxSend = false;
    var iden = '';
    var zongjiage = 0;
    var gouwushu = 0;

    var getUrl = '/site/get-cart.html';
    var reduceUrl = '/site/reduce-cart.html';
    var raiseUrl = '/site/raise-cart.html';
    var clearUrl = '/site/clear-cart.html';
    var addUrl = '/site/add-cart.html';
    var itemStorage = [];
    var prodType = 0;

    var iS = null;

    var init = function($e) {
        $js = $e;
        $js.find('.gouwuche-icon').on('tap', function(){
            viewGouwucheCont('viewitem');
        });
        $js.find('.jiesuan div:first-child').on('tap', function(){
            viewGouwucheCont('viewitem');
        });
        $js.find('.bt em').on('touchstart', function(evt){
            evt.preventDefault();
        });
        $js.find('.bt em').on('tap', function(evt){
            setGouwushu(0); //置数后动画
            itemStorage.length = 0;
            clearItemByAjax();
        });
        $js.find('.qingdan').on('tap', 'dt button:first-child', function(evt){
            var _self = $(this);
            var num = Number(_self.siblings('span').text());
            if(num <= 0)
                return;

            var e = _self.siblings('span');
            reduceItemByAjax(e.data('id'));
            e.text(num - 1);
            var new_p = Number(_self.parents('.item-kf').find('em').text());
            zongjiage -= new_p;
            setZongjia(zongjiage);
            setGouwushu(gouwushu - 1);
        });
        $js.find('.qingdan').on('tap', 'dt button:last-child', function(evt){
            var _self = $(this);
            var num = Number(_self.siblings('span').text());
            if(num >= 9999)
                return;

            var e = _self.siblings('span');
            raiseItemByAjax(e.data('id'));
            e.text(num + 1);
            var new_p = Number(_self.parents('.item-kf').find('em').text());
            zongjiage += new_p;
            setZongjia(zongjiage);
            setGouwushu(gouwushu + 1);
        });
        add3Party();
        getItemByAjax();
    };
    var add3Party = function() {
        try {
            iS = new IScroll('.iscroll-warp', {scrollbars: true});
        } catch(e) {
            $('.iscroll-warp').css('overflow-y', 'scroll');
        }
    }
    var isShowGouwuche = function(f) {
        if(gouwushu <= 0) {
            viewGouwucheCont('clearitem');
        } else {
            !isHide && $js.show();
        }
    };
    var setGouwushu = function(num) {
        gouwushu = num < 0 ? 0 : num;
        $js.find('i').text(gouwushu);
        isShowGouwuche();
    };
    var viewGouwucheCont = function(f) {
        iden = f;
        var ele = $js.find('.qingdan');
        var h = ele.height();

        if(isUp) {
            isUp = false;
            ele.animate({top: -h}, 200, '', function(){showQingdanCallback();});
        } else {
            isUp = true;
            ele.animate({top: h}, 200, '', function(){hideQingdanCallback();});
        }
    };
    var showQingdanCallback = function() {};
    var hideQingdanCallback = function() {
        if(iden == 'viewitem') {};
        if(iden == 'clearitem') {
            $js.find('.item-kf').remove();
            $js.hide();
            itemStorage.length = 0;
            setZongjia(0);
        };
    };
    var setZongjia = function(p) {
        zongjiage = p < 0 ? 0 : p;
        showZongjiage();
    };
    var showZongjiage = function() {
        $js.find('.jiesuan span').text(zongjiage.toFixed(2));
    };
    var isHaveItemInStorage = function(sid) {
        var p = -1;
        $.each(itemStorage, function(idx, v) {
            if(sid == v.sid && prodType == v.tid) {
                p = idx;
                return false;
            }
        });
        return p;
    };
    var getAddItemObjFromAjaxDataObj = function(data, obj) {
        var _obj = {};
        $.each(data, function(idx, v) {
            var b = compare.apply(obj, [v]);
            if(b) {
                _obj = v;
                return false;
            }
        });

        function compare(obj) {
            return this.sourceId == obj.source && this.type == obj.type;
        };

        return _obj;
    };
    var addCoffee = function(n, p, sl, id) {
        n = arguments[0] ? n : '';
        p = arguments[1] ? p : 0;
        sl = arguments[2] ? sl : 0;

        setGouwushu(gouwushu + Number(sl));
        setZongjia(zongjiage + (Number(p) * Number(sl)));

        if(!isUp)
            viewGouwucheCont('viewitem');

        var html = '<div class="item-kf">'+
            '<dl>'+
            '<dt>' + n + '</dt>'+
            '<dt>&yen;<em>' + p + '</em></dt>'+
            '<dt><button></button><span data-id="' + id + '">' + sl + '</span><button></button></dt>'+
            '</dl>'+
            '</div>';
        $js.find('.qingdan .items').append(html);
        iS && iS.refresh();
    };
    var getItemByAjax = function() {
        $.ajax({
            type: 'post',
            url: getUrl,
            dataType: 'json',
            success: function(data) {
                if(data) {
                    $.each(data.list,function(idx ,value){
                        itemStorage.push({sid: value.source, id: value.id, tid: value.type});
                        addCoffee(value.name, value.price, value.number, value.id);
                        isShowGouwuche();
                    });
                }
            }
        });
    };
    var reduceItemByAjax = function(id, callback) {
        $.ajax({
            type: 'post',
            url: reduceUrl,
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                callback && callback();
            }
        });
    };
    var raiseItemByAjax = function(id, callback) {
        $.ajax({
            type: 'post',
            url: raiseUrl,
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                callback && callback();
            }
        });
    };
    var clearItemByAjax = function() {
        $.get(clearUrl, function(data){
            if(data){ }
        });
    }
    var addItemByAjax = function(n, p, sl, sid) {
        var pos = isHaveItemInStorage(sid);
        if(pos != -1) {
            $js.find('.item-kf').eq(pos).find('button:last-child').trigger('tap', [pos]);
            return;
        }

        if(isAjaxSend) return;
        isAjaxSend = true;

        $.ajax({
            type: 'post',
            url: addUrl,
            dataType: 'json',
            data: {id: sid, t: prodType},
            success: function(data) {
                var addobj = getAddItemObjFromAjaxDataObj(data.list, {sourceId: sid, type: prodType});
                var id = addobj.id;
                var tid = addobj.type;
                itemStorage.push({sid: sid, id: id, tid: tid});
                addCoffee(n, p, sl, id);
            },
            complete: function() {
                isAjaxSend = false;
            }
        });
    };
    return {
        init: function(je) {init(je);},
        setAjaxUrl: function(geturl, reduceurl,raiseurl,clearurl,addurl) {
            getUrl = geturl;
            reduceUrl = reduceurl;
            raiseUrl = raiseurl;
            clearUrl = clearurl;
            addUrl = addurl;
        },
        isView: function(b) {isHide = !b;},
        setProdType: function(pt) {prodType = pt;},
        addCoffee: function(n, p, sl, sid) {addItemByAjax(n, p, sl, sid);}
    };
})();