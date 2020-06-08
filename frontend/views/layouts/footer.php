<?php
    use yii\helpers\Url;
?>
<script src="/js/lib/iscroll.js"></script>
<script src="/js/model/model.js.js"></script>
<script type="text/javascript">
        $(function(){
            JS.init($('.jiesuan-box'));

            $.each(callbacks, function(k, f){
                f();
            });

            $('.nav div').each(function(idx) {
                if((navPageId || 0) == idx) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
    <style type="text/css">
        .bnav-a {
            position: fixed;
            z-index: 99;
            bottom: -.1rem;
            width: 100%;
            font-family: Helvetica;
        }
        .bnav-a .nav {
            position: relative;
            z-index: 999;
            display: table;
            width: 100%;
            background: #000;
        }
        .bnav-a .nav .active a {
            color: #f49531;
        }
        .bnav-a .nav a {
            display: inline-block;
            color: #666666;
        }
        .bnav-a .nav > div {
            display: table-cell;
            width: 25%;
            text-align: center;
            font-size: .80rem;
        }
        .bnav-a .nav p.b1 {
            height: 1.7rem;
            line-height: 1.7rem;
        }
        .bnav-a .nav p.b2 {
            height: .87rem;
            line-height: .87rem;
        }
        .bnav-a .nav .b1 a {
            display: inline-block;
            width: 1.406rem;
            height: 1.25rem;
            vertical-align: middle;
            background-size: 100% 100%;
        }
        .bnav-a .nav .i1 {
            background-image: url(/images/nav-b-icon1.png);
        }
        .bnav-a .nav .i2 {
            background-image: url(/images/nav-b-icon2.png);
        }
        .bnav-a .nav .i3 {
            background-image: url(/images/nav-b-icon3.png);
        }
        .bnav-a .nav .i4 {
            background-image: url(/images/nav-b-icon4.png);
        }
        .bnav-a .nav .active .i1 {
            background-image: url(/images/nav-b-icon1-active.png);
        }
        .bnav-a .nav .active .i2 {
            background-image: url(/images/nav-b-icon2-active.png);
        }
        .bnav-a .nav .active .i3 {
            background-image: url(/images/nav-b-icon3-active.png);
        }
        .bnav-a .nav .active .i4 {
            background-image: url(/images/nav-b-icon4-active.png);
        }
        .bnav-a .jiesuan-box {
            width: 100%;
            position: absolute;
            bottom: 2.37rem;
        }
        .bnav-a .jiesuan {
            position: relative;
            z-index: 888;
            display: table;
            width: 100%;
            height: 2.37rem;
        }
        .bnav-a .jiesuan p {
            line-height: 2.37rem;
            text-align: center;
            color: #ffffff;
        }
        .bnav-a .jiesuan a {
            color: #fff;
        }
        .bnav-a .jiesuan > div {
            display: table-cell;
        }
        .bnav-a .jiesuan > div:first-child {
            width: 70%;
            background: #7e4c44;
        }
        .bnav-a .jiesuan > div:last-child {
            width: 30%;
            background: #422411;
        }
        .bnav-a .jiesuan-box {
            display: none;
        }
        .bnav-a .jiesuan-box .gouwuche-icon {
            position: absolute;
            left: .50rem;
            top: -.50rem;
            width: 2.40rem;
            height: 2.40rem;
            background-image: url(/images/gouwuche.png);
            background-position: 0 0;
            background-size: 100% 100%;
            z-index: 999;
        }
        .bnav-a .jiesuan-box .gouwuche-icon i {
            position: absolute;
            left: 1.08rem;
            top: .55rem;
            width: 1rem;
            height: .8rem;
            line-height: .8rem;
            font-size: .5rem;
            text-align: center;
            display: block;
            border-radius: .4rem;
            color: #ffffff;
            background: #7e4c44;
            font-style: normal;
        }
        .bnav-a .qingdan {
            position: absolute;
            width: 100%;
            top: 0;
            color: #999999;
            font-size: 1rem;
            z-index: 777;
        }
        .bnav-a .bt {
            padding: 0 .62rem;
            background: #f3f3f3;
        }
        .bnav-a .bt p {
            line-height: 2.2rem;
        }
        .bnav-a .bt em {
            float: right;
        }
        .bnav-a .bt button {
            width: 1rem;
            margin: .4rem 0 0 .2rem;
            height: 1.2rem;
            display: inline-block;
            background-image: url("/images/shanchu-tong.png");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            vertical-align: top;
            border: 0;
            background-color: transparent;
        }
        .bnav-a .qingdan .item-kf {
            padding: 0 .62rem;
            height: 2rem;
            line-height: 2rem;
            width: 100%;
            box-sizing: border-box;
            background: #fff;
            border-bottom: 1px solid #f3f3f3;
        }
        .bnav-a dl {
            display: table;
            width: 100%
        }
        .bnav-a dt {
            display: table-cell;
        }
        .bnav-a dt:nth-child(1) {
            width: 58%;
        }
        .bnav-a dt:nth-child(3) {
            text-align: right;
            width: 30%;
            vertical-align: middle;
        }
        .bnav-a .item-kf span {
            width: 22%;
            text-align: center;
            display: inline-block;
        }
        .bnav-a .item-kf button {
            width: 1.8rem;
            height: 1.8rem;
            margin-bottom: .2rem;
            vertical-align: middle;
            background-color: transparent;
            background-size: 80% 80%;
            background-repeat: no-repeat;
            background-position: center center;
            display: inline-block;
            border: 0;
        }
        .bnav-a .item-kf button:nth-child(1) {
            background-image: url(/images/btn-jian.png);
        }
        .bnav-a .item-kf button:nth-child(3) {
            background-image: url(/images/btn-jia.png);
        }
        .iscroll-warp {
            position: relative;
            width: 100%;
            max-height: 8rem;
            overflow: hidden;
            background: #fff;
        }
    </style>
    <nav class="bnav-a">
        <div class="jiesuan-box">
            <div class="qingdan">
                <div class="bt"><p>购物车<em>清空<button class="btn-qingkong"></button></em></p></div>
                <div class="iscroll-warp">
                    <div class="items"></div>
                </div>
            </div>
            <div class="jiesuan">
                <div><p>共&yen;<span>0</span></p></div>
                <div><p><a href="<?php echo Url::to(["site/cart-index"]);?>">去结算</a></p></div>
            </div>
            <div class="gouwuche-icon"><i>0</i></div>
        </div>
        <div class="nav">
            <div>
                <p class="b1"><a href="<?php echo Url::to(["site/index"]);?>" class="i1"></a></p>
                <p class="b2"><a href="<?php echo Url::to(["site/index"]);?>">首页</a></p>
            </div>
            <div>
                <p class="b1"><a href="<?php echo Url::to(["site/product-list"]);?>" class="i2"></a></p>
                <p class="b2"><a href="<?php echo Url::to(["site/product-list"]);?>">单品</a></p>
            </div>
            <div>
                <p class="b1"><a href="<?php echo Url::to(["site/group-list"]);?>" class="i3"></a></p>
                <p class="b2"><a href="<?php echo Url::to(["site/group-list"]);?>">套餐</a></p>
            </div>
            <div>
                <p class="b1"><a href="<?php echo Url::to(["user/index"]);?>" class="i4"></a></p>
                <p class="b2"><a href="<?php echo Url::to(["user/index"]);?>">我</a></p>
            </div>
        </div>
    </nav>