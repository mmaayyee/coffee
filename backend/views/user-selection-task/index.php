<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\UserSelectionTask;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSelectionTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '筛选用户任务管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-selection-task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (Yii::$app->user->can('用户筛选任务添加')){ ?>
    <p>
       <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
       <?php /*if (Yii::$app->user->can('用户筛选号码导出')) { ?>
        <?=Html::a('用户号码txt导出', "javascript:void(0);", ['class' => 'btn btn-success gridview'])*/ ?>
        <?php /*}*/ ?>
    </p>
    <?php } ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            /*[
                'class' => 'yii\grid\CheckboxColumn',
                'name'  => 'mobile_file_path',
                'checkboxOptions' => function ($model) {
                    return ["value" => $model->selection_task_name.'@@'.$model->mobile_file_path];
                },
            ],*/
            [
                'label' => '用户筛选任务名称',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->selection_task_name;
                },
            ],
            [
                'label' => '执行状态',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->selection_task_status ? UserSelectionTask::getTaskStatusList()[$model->selection_task_status] : '未执行';
                },
            ],
            [
                'label' => '执行结果',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->selection_task_result ? UserSelectionTask::getTaskResultList()[$model->selection_task_result] : "未处理";
                },
            ],
            [
                'label' => '号码数量',
                'format'=>'text',
                'value' => function ($model){
                    return $model->mobile_num;
                },
            ],

            [
                'label' => '添加时间',
                'format'=>'text',
                'value' => function ($model){ 
                    return $model->create_time ? date("Y-m-d H:i:s", $model->create_time) : "";
                },
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {export}',
                 'buttons' => [
                    'view'   => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('用户筛选任务查看') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "/user-selection-task/view?id=". $model->selection_task_id);
                    },
                    'update' => function ($url, $model, $key) {
                        return !\Yii::$app->user->can('用户筛选任务编辑') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', "/user-selection-task/update?id=". $model->selection_task_id,['title' => '编辑']);
                    },
                    'export' => function ($url, $model, $key) {
                        if(\Yii::$app->user->can('用户筛选号码导出') && $model->mobile_num > 0){
                            return Html::a('<span class="glyphicon glyphicon-log-out"></span>', "/user-selection-task/export?id=". $model->selection_task_id, ['title' => 'excel导出']);
                        }else{
                            return '';
                        }
                        
                    }


                ],
            ],
        ],
    ]); ?>
</div>
<?php
$url = '/user-selection-task/export-user-moblie';
$js = <<<eof
        $('.gridview').on('click', function () {
            var keys;
            keys = $('input[type=checkbox]:checked').map(function(index,elem) {
                if($(elem).attr('name') != 'mobile_file_path_all'){
                       return $(elem).val();
                }
            }).get().join(',');
            if (keys.length == 0) {
                alert('请选择要导出的任务！');
            }else{
                $.ajax({
                    type: 'POST',
                    url:  '{$url}',
                    data: {keys: keys},
                    dataType: 'json',
                    success: function(data){
                        //{'name':'','content':''}
                        if (data != 'n') {
                            for(var i=0;i<data.length;i++){
                                download(data[i]['name'],data[i]['content']);
                            }
                        }else{
                            alert('没有可导出的文件！');
                        }
                    },
                    error: function(data){
                        alert('导出失败');
                    }
                });
            }
        });
        //文件下载
        function download(filename,fileContent){
            aLink = document.createElement('a');
            evt = document.createEvent("MouseEvents");
            evt.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            aLink.download = filename;
            aLink.href = URL.createObjectURL(new Blob([fileContent], {type: 'text'}));
            aLink.dispatchEvent(evt);
        }
eof;
$this->registerJs($js);
?>