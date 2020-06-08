<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '黑白名单管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-tag-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=!Yii::$app->user->can('添加黑白名单') ? '' : Html::a('输入添加', ['create?add_type=1'], ['class' => 'btn btn-success'])?>
        <?=!Yii::$app->user->can('添加黑白名单') ? '' : Html::a('导入添加', ['create?add_type=2'], ['class' => 'btn btn-success'])?>
        <?=!Yii::$app->user->can('编辑黑白名单') ? '' : Html::button('批量添加备注', ['type' => 'button', 'class' => 'btn btn-success', 'onclick' => 'showRemark()'])?>
        <?=!Yii::$app->user->can('删除黑白名单') ? '' : Html::button('批量移除名单', ['type' => 'button', 'class' => 'btn btn-success', 'onclick' => 'batchRemoveList()'])?>
    </p>
    <div id="remark" style="display: none">
        <div class="form-group">
            <label>备注</label>
            <textarea id="remark-content" class="form-control" name="black_white_list_remarks" maxlength="100"></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-success" type="button" onClick="batchAddRemark()">保存</button>
        </div>
    </div>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class'           => 'yii\grid\CheckboxColumn',
            'name'            => 'userID',
            'cssClass'        => 'userid',
            'checkboxOptions' => function ($model, $key, $index, $column) {
                return ['value' => $model->user_id];
            },
        ],
        [
            'label' => '手机号',
            'value' => function ($model) {
                return $model->username;
            },
        ],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return $model->buildname;
            },
        ],
        [
            'label' => '备注',
            'value' => function ($model) {
                return $model->black_white_list_remarks;
            },
        ],
        [
            'label'     => '名单属性',
            'attribute' => 'market_type',
            'value'     => function ($model) {
                return $model->getMarketType();
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'buttons'  => [
                'update' => function ($url, $model) {
                    return !Yii::$app->user->can('编辑黑白名单') ? '' : Html::a('备注', '/black-and-white-list/update?id=' . $model->user_id);
                },
                'delete' => function ($url, $model) {
                    return !Yii::$app->user->can('删除黑白名单') ? '' : Html::a('移出', '/black-and-white-list/delete?id=' . $model->user_id, ['onclick' => 'return confirm("确定要移出吗？")']);
                },
            ],
        ],
    ],
]);?>

</div>

<script type="text/javascript">
    /**
     * 是否显示备注文本框
     * @author  zgw
     * @version 2017-09-06
     */
    function showRemark()
    {
        var userID = [];
        $("input[name='userID[]']:checked").each(function(){
            userID.push($(this).val());
        });
        if (userID.length > 0) {
            $("#remark").toggle();
        } else {
            alert('请选择要添加备注的名单。');
        }
    }

    /**
     * 批量添加备注
     * @author  zgw
     * @version 2017-09-06
     */
    function batchAddRemark(obj)
    {
        var userID = [];
        $("input[name='userID[]']:checked").each(function(){
            userID.push($(this).val());
        });
        var remakContent = $("#remark-content").val();
        if (!remakContent) {
            alert('请添加备注');
            return false;
        }
        $.post(
            '/black-and-white-list/batch-update',
            {
                user_id:userID,
                black_white_list_remarks:remakContent
            },
            function(data){
                if (data) {
                    location.reload();
                } else {
                    alert('添加备注失败');
                }
            }
        );
    }
    /**
     * 批量移除名单
     * @author  zgw
     * @version 2017-09-06
     */
    function batchRemoveList(obj)
    {
        var userID = [];
        $("input[name='userID[]']:checked").each(function(){
            userID.push($(this).val());
        });
        if (userID.length > 0) {
            if (confirm("确定要移出吗？")) {
                $.post(
                    '/black-and-white-list/batch-delete',
                    {userID:userID},
                    function(data){
                        if (data) {
                            location.reload();
                        } else {
                            alert('移除失败');
                        }
                    }
                );
            }
        } else {
            alert('请选择要删除的名单。');
        }
    }
</script>
