<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use backend\models\LotteryWinningRecord;

/* @var $this yii\web\View */
/* @var $model backend\models\Activity */

$this->title = '中奖详情';
$this->params['breadcrumbs'][] = ['label' => '营销游戏管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-view">

    <?php if(isset($activityList['numberInoList'])){ ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <div>
        <table class="table table-bordered">
            <tr>
                <td><label for="">参与总数</label></td>
                <td><?php echo $activityList['numberInoList']['part_total_num'] ?></td>
            </tr>
            <tr>
                <td><label for="">中奖人数</label></td>
                <td><?php echo $activityList['numberInoList']['winning_num'] ?></td>
            </tr>
            <tr>
                <td><label for="">未中奖人数</label></td>
                <td><?php echo $activityList['numberInoList']['not_winning'] ?></td>
            </tr>
            <?php foreach ($activityList['numberInoList']['awards_num_list'] as $key => $value) { ?>
            <?php if( $key != '谢谢参与' ){ ?>
            <tr>
                <td><label for=""><?php echo $key; ?></label></td>
                <td><?php echo $value['awards_num']; ?></td>
            </tr>
            <?php } } ?>
        </table>
    </div>
    <?php } ?>
    
    <h3>中奖信息记录</h3>
    <?php echo $this->render('winning_search', ['model' => $searchModel, 'id'=>$model->activity_id]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '奖项名称',
                'format'=>'text',
                'value' => function ($model){
                    return $model->awards_name ? $model->awards_name : "";
                },
            ],
            [
                'label' => '奖品名称',
                'format'=>'text',
                'value' => function ($model){
                    return $model->prizes_name ? $model->prizes_name : "";
                },
            ],
            [
                'label' => '奖品类型',
                'format'=>'text',
                'value' => function ($model){
                    return $model->prizes_type==1 ? '优惠券套餐' : ($model->prizes_type == 2 ? "实物" : "");
                },
            ],
            [
                'label' => '用户名称',
                'format'=>'text',
                'value' => function ($model){
                    return $model->user_id ? LotteryWinningRecord::getUserNameById($model->user_id): "";
                },
            ],

            [
                'label' => '收货人名称',
                'format'=>'text',
                'value' => function ($model){
                    return $model->receiver_name ? $model->receiver_name: "";
                },
            ],
            [
                'label' => '收货人电话',
                'format'=>'text',
                'value' => function ($model){
                    return $model->user_phone ? $model->user_phone: "";
                },
            ],
            [
                'label' => '用户地址信息',
                'format'=>'text',
                'value' => function ($model){
                    return $model->user_addr_info ? $model->user_addr_info: "";
                },
            ],
            [
                'label' => '中奖时间',
                'format'=>'text',
                'value' => function ($model){
                    return $model->create_time ? date("Y-m-d H:i", $model->create_time) : "";
                },
            ],
            
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{ship} ',
                'buttons'  => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'ship' => function ($url, $model, $key) {
                        return ($model->is_ship==1 && $model->prizes_type == 2) ? Html::button('发货', ['record_id' => $model->winning_record_id, 'class'=>'active-ship']) : ($model->is_ship == 2 ? '已发货' : "");
                    },
                ],
            ],
        ],
    ]); ?>
</div>

<input type="hidden" class="_csrf" value="<?php echo Yii::$app->request->csrfToken; ?>">
</div>
<?php

$url = Url::to(["activity/ship"]);
$this->registerJs('
    $(".active-ship").click(function(){
        if(!confirm("确定发货？")){
            return false;
        } else {
            var record_id =   $(this).attr("record_id");
            var _csrf     =   $("._csrf").val();
            $.post("' . $url . '",{record_id: record_id, _csrf: _csrf},function(data){
                window.location.reload();
            });
        }
    })
    
')?>