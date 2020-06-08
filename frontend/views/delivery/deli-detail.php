<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
  <title>制作订单</title>
  <script type="text/javascript" src="/js/vconsole.min.js"></script>
  <script type="text/javascript" src="/delivery/js/rem.js"></script>
  <link rel="stylesheet" type="text/css" href="/delivery/css/normalize.css">
  <link rel="stylesheet" type="text/css" href="/delivery/css/main.css?v=201810271110">
</head>
<body>
  <div class="container">
    <div class="order-content" id="orderContent"><p>数据加载中</p></div>
    <div class="complete-btn" id="completeOrder">制作完成</div>
  </div>
  <script id="orderContentTpl" type="text/html">
      <div class="order-item-make">
        <table>
          <tbody>
            <tr>
              <td>订单编号:</td>
              <td>{{ d.delivery_order_code }}</td>
            </tr>
            <tr>
              <td>顺 序 号:</td>
              <td>{{ d.sequence_number }}</td>
            </tr>
            <!-- <tr>
              <td>订单金额:</td>
              <td>{{ d.actual_fee }}</td>
            </tr> -->
            <tr>
              <td>收 货 人:</td>
              <td>{{ d.receiver }}</td>
            </tr>
            <tr>
              <td>收货电话:</td>
              <td>{{ d.phone }}</td>
            </tr>
            <tr>
              <td>配送地址:</td>
              <td>{{ d.userAddress }}<span class="location-icon" onclick="showMap(0)"></span></td>
            </tr>
            <tr>
              <td>下单时间:</td>
              <td>{{ getTimeFromStamp(d.create_time) }}</td>
            </tr>
            <tr>
              <td>接单时间:</td>
              <td>{{ getTimeFromStamp(d.accept_time) }}</td>
            </tr>
            <tr>
              <td>预期送达:</td>
              <td>{{ getTimeFromStamp(d.expect_service_time) }}</td>
            </tr>
            <tr>
              <td></td>
              <td><span class="change-time" onclick="changeTime('changeTime')">异常处理:更改预期送达时间</span></td>
            </tr>
          </tbody>
        </table>
        <div class="refresh-btn">刷新</div>
      </div>
      <div class="order-content" style="display: none" id="orderRefreshing"><p>订单数据刷新中</p></div>
      <table class="making-order">
        <tbody>
          {{# $.each(d.prodctAccessList,function(index,item){ }}
          <tr>
            <td>{{ item.product_name }}</td>
            <td>{{ item.product_sugar }}</td>
            <td class="making-order-code">{{ item.redeem_code }}</td>
          </tr>
          {{# }) }}
        </tbody>
      </table>
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
  <script type="text/javascript" src="/delivery/js/common.js?v=201811271747"></script>
  <script type="text/javascript" src="/delivery/js/deli-detail.js?v=201903230943"></script>
</body>
</html>
