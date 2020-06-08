<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
  <title>首页</title>
  <script type="text/javascript" src="/js/vconsole.min.js"></script>
  <script type="text/javascript" src="/delivery/js/rem.js"></script>
  <link rel="stylesheet" type="text/css" href="/delivery/css/normalize.css">
  <link rel="stylesheet" type="text/css" href="/delivery/css/main.css?v=201810271110">
</head>
<body>
  <div class="container">
    <div class="nav" id="navTop"></div>
    <div class="order-content" id="orderList"><p>数据加载中</p></div>
    <div class="bottom-refresh" id="refreshData">刷 新</div>
  </div>
  <div class="order-names-layer" style="display: none">
    <div class="order-names">
      <p class="order-names-title">订单商品列表</p>
      <div id="orderNamesList" class="order-names-list"></div>
      <div id="closeOrderName" class="close-order-name">关闭</div>
    </div>
  </div>
  <script id="orderListTpl" type="text/html">
    {{# $.each(d,function(index,item){ }}
      <div class="order-item">
        <table>
          <tbody>
            <tr>
              <td>订单编号:</td>
              <td>{{ item.delivery_order_code }}</td>
            </tr>
            <tr>
              <td>订单杯数:</td>
              <td>{{ item.order_cups }}杯<span class="order-detail-btn" onclick="showOrderNames('{{index}}')">详情&gt;</span></td>
            </tr>
            <tr>
              <td>订单金额:</td>
              <td>{{ item.actual_fee }}</td>
            </tr>
            <tr>
              <td>收 货 人:</td>
              <td>{{ item.receiver }}</td>
            </tr>
            <tr>
              <td>收货电话:</td>
              <td>{{ item.phone }}</td>
            </tr>
            <tr>
              <td>配送地址:</td>
              <td>{{ item.area+item.address }}<span class="location-icon" onclick="showMap('{{index}}')"></span></td>
            </tr>
            <tr>
              <td>下单时间:</td>
              <td>{{ getTimeFromStamp(item.create_time) }}</td>
            </tr>
            <tr>
              <td>接单时间:</td>
              <td>{{ getTimeFromStamp(item.accept_time) }}</td>
            </tr>
            <tr>
              <td>预期送达:</td>
              <td>{{ getTimeFromStamp(item.expect_service_time) }}</td>
            </tr>
            <tr>
              <td></td>
              <td><span class="change-time" onclick="changeTime('{{item.delivery_order_id}}')">异常处理:更改预期送达时间</span></td>
            </tr>
          </tbody>
        </table>
        <div class="order-status {{# if(item.delivery_order_status=='3'){ }}order-status2{{# } else if(item.delivery_order_status=='4'){ }}order-status3{{# } else if(item.delivery_order_status=='5'){ }}order-status4{{# } }}">{{# if(item.delivery_order_status=='3'){ }}未制作{{# } else if(item.delivery_order_status=='4'){ }}未完成{{# } else if(item.delivery_order_status=='5') { }}配送中{{# } }}</div>
        <div class="doing-btn" onclick="{{# if(item.delivery_order_status=='3'){ }}makeCoffee('{{ item.delivery_order_id }}'){{# } else if(item.delivery_order_status=='4'){ }}makeCoffee('{{ item.delivery_order_id }}'){{# } else if(item.delivery_order_status=='5'){ }}callUser('{{ item.phone }}'){{# } }}">{{# if(item.delivery_order_status=='3'){ }}制 作{{# } else if(item.delivery_order_status=='4'){ }}制 作{{# } else if(item.delivery_order_status=='5'){ }}打电话{{# } }}</div>
        <div class="doing-btn doing-btn-2" style="display:{{# if(item.delivery_order_status=='5'){ }}block;{{# } else{ }}none;{{# } }}" onclick="confirmOrder('{{ item.delivery_order_id }}')">确认送达</div>
      </div>
    {{# }) }}
  </script>
  <script type="text/javascript">
    var signPackageStr = '<?php echo $signPackage;?>';
    var signPackage = JSON.parse(signPackageStr);
    // console.log("signPackageStr..",signPackageStr);
  </script>
  <script type="text/javascript" src="/js/fastclick.js"></script>
  <script type="text/javascript" src="/js/lib/jquery-1.11.1.min.js"></script>
  <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="/js/laytpl.js"></script>
  <script type="text/javascript" src="/delivery/js/common.js?v=201810271405"></script>
  <script type="text/javascript" src="/delivery/js/doing-order.js?v=201810271040"></script>
</body>
</html>
