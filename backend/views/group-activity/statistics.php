<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '拼团活动管理';
$this->params['breadcrumbs'][] = ['label' => '数据统计', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('//netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
$this->registerCssFile('/spell-group/css/foundation.min.css');
$this->registerCssFile('/spell-group/css/foundation-datepicker.css');
$this->registerCssFile('/spell-group/css/statistic.css?v=1.7');

?>
<div class="statistic-wrap">
  <!-- 总数 -->
  <div class="total-wrap statistic">
      <div class="left title">总数:</div>
      <table id="total" class="left tabData" border="1">
        <thead>
            <tr><th></th><th>全部</th><th>老带新</th><th>新手团</th><th>全民参与</th></tr>
        </thead>
        <tr>
            <td>总数</td><td class="all-sum"></td><td class="old-sum"></td><td class="new-sum"></td><td class="quan-sum"></td>
        </tr>
        <tr>
          <td>上线活动</td><td class="on-all"></td><td class="on-old"></td><td class="on-new"></td><td class="on-quan"></td>
        </tr>
        <tr>
         <td>下线活动</td><td class="out-all"></td><td class="out-old"></td><td class="out-new"></td><td class="out-quan"></td>
        </tr>
      </table>
      <div class="clearfix"></div>
  </div>
  <!-- 排名 -->
  <div class="rank-wrap">
      <div class="border">
          <span class="left title">排名:</span>
          <div class="left"><p><label>选择时间：</label><input type="text" id="rankTime"></p></div>
          <div class="left mgin">
              <span>活动类别：</span>
              <select name="" id="rankType">
                <option value="">请选择</option>
                <option value="">全部活动</option>
                <option value="2">老带新</option>
                <option value="1">新手团</option>
                <option value="3">全民参与</option>
              </select>
          </div>
          <button class="btns search mgin">搜索</button>
          <button class="btns mgin export">导出排名</button>
          <div class="clearfix"></div>
      </div>
      <table id="rank" class="left tabData tabmar" border="1">
        <thead>
            <tr><th rowspan="2">活动名称</th><th colspan="2">热度</th><th colspan="2">成团率</th><th colspan="2">拉新用户</th></tr>
            <tr><th>排名</th><th>数量</th><th>排名</th><th>数量</th><th>排名</th><th>数量</th></tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <div class="clearfix"></div>
      <p class="noData">暂无数据</p>
  </div>
  <div class="clearfix"></div>
  <!-- 单团详细数据 -->
  <div class="detail-wrap">
      <div class="border">
      <span class="left title">单团详细数据:</span>
          <div class="">
              <span>活动类型：</span>
              <select name="" id="detailType">
                <option value="">请选择</option>
                <option value="">全部活动</option>
                <option value="2">老带新</option>
                <option value="1">新手团</option>
                <option value="3">全民参与</option>
              </select>
              <span class="mgin">活动名称：</span>
              <select name="" id="detailName">
                <option value="">请选择</option>
              </select>
          </div>
          <div class="mgin timeline"><p><label>开始时间：</label><input type="text" id="rankBeginTime"></select><label class="mgin">结束时间：</label><input type="text" id="rankEndTime"><button class="btns search mgin">搜索</button><button class="btns mgin export">导出</button></p></div>
          <div class="clearfix"></div>
          
      </div>
      <table id="detailData" class="detailData tabmar" border="1">
        <thead>
            <tr><th>活动名称</th><th>发起用户数</th><th>发起总团数</th><th>成功总团数</th><th>拉新用户数</th><th>销量(杯数)</th><th>销售额</th><th>时间</th></tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <p class="noData">暂无数据</p>
  </div>
</div>
<script src="/js/jquery-2.0.0.min.js"></script>
<script src="/spell-group/js/foundation-datepicker.js"></script>
<script src="/spell-group/js/foundation-datepicker.zh-CN.js"></script>
<script>
  var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
  var rootData = JSON.parse('<?php echo $data ?>').data;
  console.log(rootData)
  $("#rankTime,#rankBeginTime,#rankEndTime").fdatepicker({
    format: 'yyyy-mm-dd',
  });
</script>
<script src="/spell-group/js/statistic.js?v=1.9"></script>

