<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '添加商品活动';
$this->params['breadcrumbs'][] = ['label' => '拼团活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/spell-group/css/bootstrap.min.css');
$this->registerCssFile('/spell-group/css/index.css');
$this->registerJSFile('/spell-group/js/jquery.min.js');
$this->registerJSFile('/spell-group/js/bootstrap.min.js');
$this->registerJSFile('/spell-group/js/index.js');
?>

<div>
  <h4>拼团上线排序</h4>
  <div class = "sort-container"> 

    <!-- Nav tabs -->
    <ul class="nav nav-tabs sort-nav" srole="tablist" style="width:600px">
      <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab" class="tab-old">老带新团</a></li>
      <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" class="tab-new">新手团</a></li>
      <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab" class="tab-all">全民参与</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content sort-main">
      <div role="tabpanel" class="tab-pane active" id="home">
          <div class="content">
            <table>
              <thead>
                <tr>
                  <th>活动名称</th>
                  <th>序列号</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody class="box">
              </tbody>
            </table>
          </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="profile">
        <div class="content">
            <table>
              <thead>
                <tr>
                  <th>活动名称</th>
                  <th>序列号</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody class="box">
              </tbody>
            </table>
          </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="messages">
        <div class="content">
            <table>
              <thead>
                <tr>
                  <th>活动名称</th>
                  <th>序列号</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody class="box">
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <div>(若上线序列号与本页序列号不一致,请点击保存！)</div>
    <button class="cancel">取消</button>
    <button class="save">保存</button>
  </div>
</div>
<script>
  var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
