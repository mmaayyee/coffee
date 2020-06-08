var ZP = (function(){
    var isRotate = false;
    var isRotate2 = false;
    var isClick = true;
    var isGuolv = false;

    var $zp = null;
    var touchStartAngle = 0;
    var touchMoveAngle = 0;
    var touchRotateAngle = 0;
    var touchStepAngle = 0;
    var slideStartTime = 0;
    var C = 360;
    var division = 8;
    var baseRing = 2;
    var rotateDire = 1;

    var cx = 0;
    var cy = 0;

    var zpItemArr = [
        {id: 6, cont: '<dt><span>&yen;</span>10.00<i data-id="0"></i></dt><dt>拿铁咖啡</dt>', p: 10, n: '拿铁咖啡'},
        {id: 7, cont: '<dt><span>&yen;</span>11.00<i data-id="1"></i></dt><dt>摩卡(含糖)</dt>', p: 11, n: '摩卡'},
        {id: 8, cont: '<dt><span>&yen;</span>12.00<i data-id="2"></i></dt><dt>拿铁玛琪朵（含糖）</dt>', p: 12, n: '拿铁玛琪朵'},
        {id: 9, cont: '<dt><span>&yen;</span>13.00<i data-id="3"></i></dt><dt>美式咖啡(含糖)</dt>', p: 13, n: '美式咖啡'},
        {id: 10, cont: '<dt><span>&yen;</span>14.00<i data-id="4"></i></dt><dt>香草玛琪朵</dt>', p: 14, n: '香草玛琪朵'},
        {id: 11, cont: '<dt><span>&yen;</span>115.00<i data-id="5"></i></dt><dt>卡布奇诺</dt>', p: 115, n: '卡布奇诺'},
        {id: 12, cont: '<dt><span>&yen;</span>16.00<i data-id="6"></i></dt><dt>香草摩卡</dt>', p: 16, n: '香草摩卡'},
        {id: 13, cont: '<dt><span>&yen;</span>17.00<i data-id="7"></i></dt><dt>香草拿铁</dt>', p: 17, n: '香草拿铁'},
        {id: 14, cont: '<dt><span>&yen;</span>18.00<i data-id="8"></i></dt><dt>奶特咖啡</dt>', p: 18, n: '奶特咖啡'},
        {id: 15, cont: '<dt><span>&yen;</span>19.00<i data-id="9"></i></dt><dt>香草咖啡</dt>', p: 19, n: '香草咖啡'},
        {id: 16, cont: '<dt><span>&yen;</span>20.00<i data-id="10"></i></dt><dt>牛奶咖啡</dt>', p: 20, n: '牛奶咖啡'},
        {id: 17, cont: '<dt><span>&yen;</span>2.00<i data-id="11"></i></dt><dt>香草卡布奇诺</dt>', p: 21, n: '香草卡布奇诺'},
        {id: 18, cont: '<dt><span>&yen;</span>22.00<i data-id="12"></i></dt><dt>热巧克力</dt>', p: 22, n: '热巧克力'},
        {id: 19, cont: '<dt><span>&yen;</span>23.00<i data-id="13"></i></dt><dt>泡沫巧克力（含糖）</dt>', p: 23, n: '泡沫巧克力'},
        {id: 20, cont: '<dt><span>&yen;</span>24.00<i data-id="14"></i></dt><dt>牛奶巧克力</dt>', p: 24, n: '牛奶巧克力'},
        {id: 21, cont: '<dt><span>&yen;</span>25.00<i data-id="15"></i></dt><dt>香草泡沫奶</dt>', p: 25, n: '香草泡沫奶'},
    ];
    var itemObj = {};
    var caclIdxPoint = 0;
    var arrPoint = 4;

    var addItemHandler;

    var init = function($e){
        $zp = $e;

        $(window).resize(initZpPos);
        $zp.parent().find('.rotate-btn').on('tap', function(e){
            if(isRotate || isRotate2) return;
            zpColorMgr.allColor();
            isRotate2 = true;
            rotateDire = [-1, 1][Math.floor(Math.random() * 2)];
            randomRotate();
        });
        $zp.parent().find('.additem-btn').on('tap', function(e){
            if(isRotate || isRotate2) return;
            addItemHandler();
        });
        $zp.parent().on('touchmove', function(e){
            e.preventDefault();
        });
        $zp.on('touchstart', function(e){
            if(isRotate || isRotate2) return;
            isClick = true;

            slideStartTime = new Date().getTime();
            var touch = e.originalEvent.touches[0];
            touchStartAngle = Math.atan2(touch.pageY-cy, touch.pageX-cx) * 180 / Math.PI;
        });
        $zp.on('touchmove', function(e) {
            if(isRotate || isRotate2) return;
            isClick = false;

            zpColorMgr.allColor();
            var touch = e.originalEvent.touches[0];
            touchMoveAngle = Math.atan2(touch.pageY-cy, touch.pageX-cx) * 180 / Math.PI;
            touchStepAngle = touchMoveAngle - touchStartAngle;
            rotateDire = touchStepAngle >= 0 ? 1 : -1;

            var dr = touchRotateAngle + touchStepAngle;
            angleHandler(dr);
            $zp.rotate(dr);
        });
        $zp.on('touchend', function(e) {
            if(isRotate || isRotate2) return;
            if(isClick) {
                return;
            } else {
                isClick = true;
            }
            isRotate = true;

            var slideEndTime = new Date().getTime();
            if((slideEndTime - slideStartTime < 300) && Math.abs(touchStepAngle) > 10) {
                rotateDire = touchStepAngle >= 0 ? 1 : -1;
                randomRotate(rotateDire);
            } else {
                var rotateTo = touchRotateAngle + Math.round(touchStepAngle / (C / division)) * (C / division);
                $zp.rotate({
                    duration: 500,
                    animateTo: rotateTo,
                    step : angleHandler,
                    callback:function(){
                        touchRotateAngle = rotateTo;
                        isRotate = false;
                        viewItemInfo(getItemIdxByStop());
                    }
                });
            }
        });
        initZpPos();
        rotateDl();
        createZpItem();
    };
    var createZpItem = function() {
        var earr = $zp.find('dl');
        var arr = zpItemArr.slice(0, division);

        earr.each(function(index, item) {
            $(item).html(arr[index]['cont']);
        });
        itemObj = arr[0];
        $zp.addClass('bg');
        zpColorMgr.oneColor(0);
    };
    var zpColorMgr = (function() {
        this.flag = false;
        this.allColor = function() {
            if(this.flag) return;
            this.flag = true;
            $zp.find('dl').css('color', '#000');
        };
        this.oneColor = function(idx) {
            this.flag = false;
            $zp.find('dl').eq(idx).css('color', '#c90000');
        };
        return this;
    }).call({});
    var rotateDl = function() {
        $zp.find('dl').each(function(index){
            $(this).css({'-webkit-transform':'rotate(' + (-(C / division) * index) + 'deg)', '-moz-transform':'rotate(' + (-(C / division) * index) + 'deg)'});
        });
    };
    var initZpPos = function() {
        cx = $zp.offset().left + ($zp.width() / 2);
        cy = $zp.offset().top + ($zp.height() / 2);
    };
    var getRandomAngle = function(){
        return Math.ceil(Math.random() * division) * (C / division);
    };
    var getItemIdxByStop = function(){
        var n = Math.abs(Math.floor(touchRotateAngle / C));
        var a = (touchRotateAngle + (C * (n + 1))) % C; //角度转正
        var idx = Math.floor(a / (C / division));
        return idx;
    };
    var viewItemInfo = function(idx){
        zpColorMgr.oneColor(idx);
        $zp.find('dl').each(function(index, dom){
            if(idx == index) {
                var pos = $(dom).find('i').data('id');
                itemObj = zpItemArr[pos];
            }
        });
    };
    var angleHandler = function(curAngle) {
        var rotateAngle = Math.round(curAngle) % 360;
        var eIdx = Math.round(rotateAngle / 45);
        eIdx = Math.abs(eIdx) == 8 ? 0 : eIdx;

        if(eIdx != caclIdxPoint) {
            var e1Idx = (eIdx + 4) % 8;

            if(rotateDire > 0) {
                arrPoint = arrPoint >= zpItemArr.length - 1 ? 0 : arrPoint + 1;
            } else {
                arrPoint = arrPoint <= 0 ? zpItemArr.length - 1 : arrPoint - 1;
            }
            $zp.find('dl').eq(e1Idx).html(zpItemArr[arrPoint].cont);
            isGuolv && qiguaidechuli($zp.find('dl').eq(e1Idx));
        }
        caclIdxPoint = eIdx;
    };
    var qiguaidechuli = function(dom) {
        var $dt = dom.find('dt').eq(1);
        var t = $dt.text();
        var p = t.split('（').length == 2 ? t.indexOf('（') : t.indexOf('(');
        (p >= 0) && $dt.html(t.substring(0, p) + '<br>' + t.substring(p));
    };
    var shuzichuli = function(n) {
        return String(n).indexOf('.') == -1 ? n + '.00' : n;
    };
    var randomRotate = function(){
        var addAngle = getRandomAngle() + C * baseRing;
        addAngle = addAngle * rotateDire;

        $zp.rotate({
            angle: touchRotateAngle,
            animateTo: touchRotateAngle + addAngle,
            duration: 3000,
            step : angleHandler,
            callback: function(){
                isRotate = false;
                isRotate2 = false;
                touchRotateAngle = touchRotateAngle + addAngle;
                viewItemInfo(getItemIdxByStop());
            }
        });
    };
    return {
        init: function(je, darr){ zpItemArr = darr || zpItemArr; init(je);},
        getItem: function(){return itemObj;},
        itemGuolv: function(b) {
            isGuolv = b;
            if(!b || !isGuolv) return;
            $zp.find('dl').each(function(idx, dom) {
                qiguaidechuli($(dom));
            });
        },
        addItemHandler: function(fun){ addItemHandler = fun || function(){};}
    };
})();