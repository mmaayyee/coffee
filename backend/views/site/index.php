<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ERP后台系统</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/changeLayout.css">
    <script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
    <script>
        if(self!=top){
            parent.window.location.reload(true);
        }
    </script>
</head>
<body>
<!-- 公共头部 -->
    <div class="header">
        <a href="/site/welcome" target="myframe" onclick="logoClick()"><img src="images/logo.png" alt="logo"></a>
        <p id="mob-menu">菜单</p>
        <!-- 系统菜单 -->
        <div id="Intelligent_title">
        </div>
        <div>
            <!-- 退出 -->
            <div>
                <a href="<?php echo Url::to('/site/logout'); ?>"><input type="button" value="退出" id="btnExit"></a><span class="index-username">( <?=Yii::$app->user->identity->username?> )</span>
            </div>
        </div>
    </div>
<!-- 公共头部 end-->
    <div class="wrap">
        <!-- 主体 -->
        <div id="Intelligent">
            <div id="Intelligent_cont">
                <!-- 系统菜单对应子菜单 -->
                <div id="Intelligent_left">
                    <div id="Intelligent_nav">
                    </div>
                    <p class="versition">版本2.0.0<br>&copy;咖啡零点吧 <?=date('Y')?></p>
                </div>
                <!-- 主体右侧  height:2000px; -->
                <iframe src="<?php echo Url::to('site/welcome'); ?>" id="myframe"  name="myframe" style="float:left; width:1100px; min-height: 800px; margin-left:10px; border:none; " target="_self">
                </iframe>
                <div class="clear"></div>
            </div>
        </div>
        <div class="iframe-loading">
            <div class="load5"><div class="loader"></div></div>
            <p class="loading-txt">页面加载中，请稍候...</p>
        </div>
    </div>
    <script>
        var viewWidth = document.documentElement.clientWidth;
        var mobMenuSlideFlag = false;//默认没打开
        // 判断移动端
        var isMobile = /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent);
        if(isMobile) {
            var link = document.createElement('link');
            link.type = 'text/css';
            link.rel = 'stylesheet';
            link.href = 'css/change-layout-mobile.css';
            var head = document.getElementsByTagName('head')[0];
            head.appendChild(link);
        }
    	showNav();
        navClick();
        var currentiframeHeight = 0;
        function iframeResize(){
            var myFrame = document.getElementById("myframe");
            if(!myFrame.contentWindow.document.body) return false;
            var height = myFrame.contentWindow.document.body.clientHeight;
            // console.log(currentiframeHeight,"   ...   ",height);
            if(Math.abs(height-currentiframeHeight)<10) return false;
            $("#myframe").height(0);
            $("#myframe").height(height+20);
            currentiframeHeight = height+20;
        }
        window.setInterval(iframeResize,1000);
        // $("#myframe").contents().find("body").resize(iframeResize);
    	function showNav(){
    		 $.ajax({
                url: "/api/get-menu",
                data: "",
                type: 'post',
                success: function (data) {
                	var $data=JSON.parse(data);//导航数据
                	// console.log($data);
                	var $Intelligent_item="";//系统菜单
                	var $nav_item="";//导航菜单
                	for(var i=0; i<$data.length; i++){
                		if(i==0){
                			$Intelligent_item+='<span id="Intelligent_operation" class="active">'+$data[i].label+'</span>';//系统菜单
                			$nav_item+='<nav class="navbar-inverse navbar-fixed-top navbar active"><div  class=" w2-collapse navbar-collapse"><ul id="w3" class="navbar-nav navbar-right nav">';//导航菜单

                		}else{
                			$Intelligent_item+='<span id="Intelligent_operation">'+$data[i].label+'</span>';
                			$nav_item+='<nav class="navbar-inverse navbar-fixed-top navbar"><div class=" w2-collapse navbar-collapse"><ul id="w3" class="navbar-nav navbar-right nav">';
                		}
                		for(var j=0; j<$data[i].items.length; j++){
                			$nav_item+='<li class="dropdown"><span class="dropdown-toggle" data-toggle="dropdown">'+$data[i].items[j].label+'<b class="caret"></b></span><ul  class="dropdown-menu">';

            					for(var y=0; y<$data[i].items[j].items.length; y++){

            						$nav_item+='<li><a href="'+$data[i].items[j].items[y].url +'" target="myframe" onclick="navClick()" tabindex="-1">'+$data[i].items[j].items[y].label+'</a></li>';
            					}
                            $nav_item+='<div class="clear"></div></ul></li>';
                		}
                		$nav_item+="</ul><div class='clear'></div></div></nav>";
                        $("#Intelligent_nav").html($nav_item);
                	}
                	$("#Intelligent_title").html($Intelligent_item);
                	operateNav();
                },
                error:function(){

                }
            })
    	}
        function navClick(){
            // console.log("click");
            var loadingHeight = $("#myframe").height()>600?$("#myframe").height():600;
            $(".iframe-loading").css("height",loadingHeight);
            $(".iframe-loading").show();
            if(isMobile) {
                console.log("nav out")
                $("#Intelligent_title").hide();
                mobMenuSlideFlag = false;
                $("#Intelligent_left").removeClass("navslidein").addClass("navslideout");
            }
            $("#myframe").on("load",function(){
              $(".iframe-loading").hide();
              $(this).height(0); //用于每次刷新时控制IFRAME高度初始化
              var myFrame = document.getElementById("myframe");
              var height = myFrame.contentWindow.document.body.clientHeight;
              $(this).height(height+20);
              var title = $(this).contents().attr("title");
              console.log("title",title)
              $(document).attr("title","ERP后台系统-"+title);
              $("#myframe").off("load");
            });
        }
    	var preMainNavId = 0;
    	function operateNav(){
    		// 点击系统菜单，切换对应菜单
	        $("#Intelligent_title span").on("click",function(obj){
                $("#myframe").attr('src','/site/welcome');
	            var index=$(this).index();
	            $(this).addClass("active").siblings().removeClass("active");
	            $("#Intelligent_nav nav").eq(index).addClass("active").siblings().removeClass("active");
                closeLeftNav(preMainNavId);
                preMainNavId = index;
	        })
	        // 点击菜单切换
	        $(".w2-collapse .dropdown-toggle").on("click",function(e){
	        	$(this).toggleClass("active").parent().siblings().find(".dropdown-toggle").removeClass("active");
	            $(this).parent().find(".dropdown-menu").slideToggle();
                $(this).parent().siblings().find(".dropdown-menu").slideUp();
	        })
	        $(".dropdown-menu li").on("click",function(){
	            $(this).addClass("active").siblings().removeClass("active");
	            $(this).parents(".dropdown").siblings().find(".dropdown-menu li").removeClass("active");

	        })
	       // 根据页面高度自动获取导航高度
	        function navHeight(){
	        	var winHeight=$(window).height();
	        	var winTopHeight=$(".header").height();
	        	var winFooterHeight=$(".header").height();
	        	$("#Intelligent_nav nav").css({"height":winHeight-winTopHeight-winFooterHeight,"overflow":"hidden"});
                 $("#myframe").css("min-height",winHeight-winTopHeight-winFooterHeight-150);//70为版本的高度
                 $(".iframe-loading").css("height",$("#myframe").height());
	        }
            //自动获取iframe宽度
            function iframeWidth(){
                var winWidth=$(window).width();
                var winNavWidth=$("#Intelligent_left").width();
                $("#myframe").css("width",winWidth-winNavWidth-30);
                $(".iframe-loading").css("width",winWidth-winNavWidth-30);
            }
            iframeWidth();
            navHeight();

	        $(window).on("resize",function(){
	        	navHeight();
                iframeWidth();
	        })
    	}
        function closeLeftNav(id){
            // console.log(id)
            $(".w2-collapse").eq(id).find(".dropdown-toggle").removeClass("active");
            $(".w2-collapse").eq(id).find(".dropdown-menu").slideUp();
            $(".w2-collapse").eq(id).find(".dropdown-menu li").removeClass("active");
        }
        function logoClick(){
            closeLeftNav(preMainNavId);
        }
        // 移动端显示或隐藏菜单
        $("#mob-menu").on("click",function(){
            $("#Intelligent_title").slideToggle();
            if(mobMenuSlideFlag) {
                mobMenuSlideFlag = false;
                $("#Intelligent_left").removeClass("navslidein").addClass("navslideout");
            } else {
                mobMenuSlideFlag = true;
                $("#Intelligent_left").removeClass("navslideout").addClass("navslidein");
            }
        })
    </script>
</body>
</html>
