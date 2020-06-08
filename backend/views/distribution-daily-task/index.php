<?php

use backend\models\Manager;
use backend\models\Organization;
use kartik\select2\Select2;
use yii\helpers\Html;
$this->params['breadcrumbs'][] = ['label' => '配送任务管理', 'url' => ['/distribution-task/index']];
$this->title                   = '日常任务';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/css/distribution-daily-task.css');
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/distribution-daily-task-list.js?v=201901031949', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php
$orgId = Manager::getManagerBranchID();
if ($orgId == 1) {
    $companyList  = Organization::getBranchArray(2);
    $companyArray = [];
    foreach ($companyList as $orgId => $company) {
        if ($orgId != 1) {
            $companyArray[$orgId] = $company;
        }
    }
    ?>
<form action="/distribution-daily-task/index" method="get">
    <div class="form-inline">
        <div class="form-group">
            <label>分公司</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
        'name'    => 'org_id',
        'value'   => $org_id,
        'data'    => $companyArray,
        'options' => ['multiple' => false, 'placeholder' => '请选择分公司'],
    ]);
    ?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
</form>
<?php }?>

<div class="distribution-daily-task-index">
    <h1><?=Html::encode($this->title)?></h1>
</div>

<script>
    var rootInfoData = <?php echo $model; ?>;
    var rootOrgId = '<?php echo $org_id; ?>';
    console.log("rootOrgId...",rootOrgId);
    var loc = location.href;
    var urlType = loc.indexOf("?")>0?loc.substr(loc.indexOf("?")+1):"";//从?号后面的内容
    console.log("urlType..",urlType);
</script>
<div id="taskList"></div>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelleadby="myModalLabel" id="myModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true"></span>
                </button>
                <h4 class="modal-title" id="myModalLabel">任务转交</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="save">确认</button>
            </div>
        </div>
    </div>
</div>
<script id="taskListTpl" type="text/html">
    <div>
        <table class="table table-bordered table-condensed">
            <thead>
                <tr class="text-center">
                    <th>运维人员</th>
                    {{# for(var key in d.date){  }}
                    <th>
                        <span>{{d.date[key]}}</span>
                        {{# if(d.date[key] == currentTime) { }}
                        <span class="all-issue" data-orgid="{{d.orgId}}" data-date="{{d.date[key]}}"><img src="/images/u1461.png" alt="" title="下发任务"></span>
                        {{#  } }}
                    </th>
                    {{#  } }}
                </tr>
            </thead>
            <tbody>
                {{# $.each(d.dailyTaskList,function(index,item){  }}
                <tr>
                    <td> <a href="#">{{item.userName}}</a></td>
                        {{# for(var key in d.date){  }}
                    <td class="list" data-orgid="{{d.orgId}}" data-date="{{d.date[key]}}" data-userid ="{{index}}" style="padding:15px">
                        {{#if(item[d.date[key]]){ }}
                        <p id="{{index}}-{{d.date[key]}}-top">楼宇数量：{{item[d.date[key]].buildNumber }}台 </p>
                        <div class="operation-btn">
                            <p>未下发</p>
                            <p class="details-btn"><span class="glyphicon glyphicon-tasks" title="任务详情"></span></p>
                            {{# if(key == 0) { }}
                            <p class="issue"></p>
                            {{#  } }}
                        </div>
                        {{# if(item[d.date[key]].materialList) { }}
                        <div class="material-list">
                            <p>物料：</p>
                            {{# $.each(item[d.date[key]].materialList,function(indx, value){ }}
                                <p class="num">{{value.name}}:{{value.number}}</p>
                            {{#   }) }}
                        </div>
                        {{# } }}
                        <div class="task-list" id="{{index}}-{{d.date[key]}}">
                            {{# $.each(item[d.date[key]].taskList,function(indx, value){ }}
                                <div id="{{index}}-{{d.date[key]}}-{{value.buildId}}">
                                    <p>{{value.buildName}}</p>
                                    <p class="color-red" data-taskType="{{value.type}}">{{value.typeName}}</p>
                                    <p class="icon icon-{{key}}" data-buildid="{{value.buildId}}">
                                      {{# if(key == 1&&urlType!=1|| key ==2&&urlType!=1) {  }}
                                      <span class="glyphicon glyphicon-arrow-left change-date-left" title="修改日期"></span>
                                      {{# } }}
                                      {{# if(key == 0&&urlType!=1) {  }}
                                      <span><img class="change-personnel" src="/images/u1554.png" alt="" title="修改人员"></span>
                                      {{# } }}
                                      {{# if(key == 0&&urlType!=1 || key == 1&&urlType!=1) { }}
                                      <span class="glyphicon glyphicon-arrow-right change-date-right" title="修改日期"></span>
                                      {{# } }}
                                    </p>
                                </div>
                            {{#  })  }}
                        </div>
                        {{# } }}
                    </td>
                    {{# } }}
                </tr>
               {{#  }) }}
            </tbody>
        </table>
    </div>
</script>
