<?php

use backend\models\Activity;
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ActivityApi;
use backend\models\Organization;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = '营销游戏详情';
$this->params['breadcrumbs'][] = ['label' => '营销游戏', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('
    $("img").click(function(){
        var url = $(this).attr("src");
        window.open(url,"ex","width=400,height=400,left=500,top=200");
    });
');
?>
<style type="text/css">
	img{
	    cursor: pointer;
	    width:50px;
	    height:50px;
	}
</style>
<div class="building-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [

        [
            'attribute' => 'activity_name',
            'value'     => $model->activity_name,
        ],
        [
            'attribute' => 'start_time',
            'value'     => $model->start_time ? date("Y-m-d H:i:s", $model->start_time) : "",
        ],
        [
            'attribute' => 'end_time',
            'value'     => $model->end_time ? date("Y-m-d H:i:s", $model->end_time) : "",
        ],
        [
            'attribute' => 'activity_sort',
            'value'     => $model->activity_sort,
        ],
        [
            'attribute' => 'status',
            'value'     => $model->status ? Activity::activityStatusList()[$model->status] : "",
        ],
        [
            'attribute' => 'activity_type_id',
            'value'     => $model->activity_type_id ? ActivityApi::getActivityTypeList(2, 1)[$model->activity_type_id] : "",
        ],
        [
            'attribute' => 'activity_desc',
            'format'	=> 'html',
            'value'     => $model->activity_desc,
        ],

        [
            'attribute' => 'person_day_frequency',
            'value'     => $model->person_day_frequency,
        ],
        [
            'attribute' => 'max_frequency',
            'value'     => $model->max_frequency,
        ],
        [
            'attribute' => 'awards_num',
            'value'     => $model->awards_num ? Activity::getAwardsNumList()[$model->awards_num] : "",
        ],

        [
            'attribute' => 'background_music',
            'value'     => $model->background_music ? Yii::$app->params['fcoffeeUrl']. $model->background_music : "",
        ],
        [
            'attribute' => 'background_photo',
            'format'	=> 'raw',
            'value'     => $model->background_photo ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->background_photo.'" alt="">' : "",
        ],
        [
            'attribute' => 'activity_tips',
            'format'	=> 'raw',
            'value'     => $model->activity_tips ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->activity_tips.'" alt="">' : "",
        ],
        [
            'attribute' => 'title_photo',
            'format'	=> 'raw',
            'value'     => $model->title_photo ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->title_photo.'" alt="">' : "",
        ],
        [
            'attribute' => 'activity_background',
            'format'	=> 'raw',
            'value'     => $model->activity_background ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->activity_background.'" alt="">' : "",
        ],
        [
            'attribute' => 'light_one_backgroup',
            'format'	=> 'raw',
            'value'     => $model->light_one_backgroup ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->light_one_backgroup.'" alt="">' : "",
        ],
        [
            'attribute' => 'light_two_backgroup',
            'format'	=> 'raw',
            'value'     => $model->light_two_backgroup ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->light_two_backgroup.'" alt="">' : "",
        ],

        [
            'attribute' => 'lottery_button',
            'format'	=> 'raw',
            'value'     => $model->lottery_button ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->lottery_button.'" alt="">' : "",
        ],
        [
            'attribute' => 'click_effect',
            'format'	=> 'raw',
            'value'     => $model->click_effect ? '<img src="'. Yii::$app->params['fcoffeeUrl']. $model->click_effect.'" alt="">' : "",
        ],
        [
            'attribute' => 'created_at',
            'value'     => $model->created_at ? date("Y-m-d H:i:s", $model->created_at) : "",
        ],
    ],
]);?>

	<?php if($gridList){ ?>
	<div>
		<h3>方格信息</h3>
		<table class="table table-striped table-bordered">
		    <tr>
		        <th>方格名称</th>
		        <th>缩略图</th>
		        <th>奖项名称</th>
		    </tr>
			<?php foreach ($gridList as $key => $value) { ?>
			<tr>
				<td><label for=""><?php echo $value['grid_name'] ?></label></td>
				<td><img src="<?php echo (isset($value['grid_photo']) && $value['grid_photo']) ? Yii::$app->params['fcoffeeUrl'] . $value['grid_photo'] : "" ?>" alt=""></td>
				<td><?php echo $value['awards_name'] ?></td>
			</tr>
			<?php  } ?>
		</table>

	</div>
	<?php } ?>

	<?php if($awardSetList){ ?>
	<div>
		<h3>奖项设置</h3>
		<table class="table table-striped table-bordered">
			<tr>
				<td><label for="">奖项等级</label></td>
				<td><label for="">奖品类型</label></td>
				<td><label for="">优惠券信息</label></td>
				<td><label for="">奖品名称</label></td>
				<td><label for="">中奖概率</label></td>
				<td><label for="">奖品总数</label></td>
				<td><label for="">单人中奖次数</label></td>
			</tr>

			<?php foreach ($awardSetList as $key => $value) { ?>
			<tr>
				<td><?php echo $value['awards_name'] ?></td>
				<td><?php echo $value['prizes_type']==1 ? '优惠券套餐' : ($value['prizes_type'] == 2 ? '实物': '') ?></td>
				<td>
					<label for="">优惠券套餐名称：</label><?php echo isset($value['prizes_content_list']['coupon_group_name']) ?  $value['prizes_content_list']['coupon_group_name'] : "" ?>
					<table class="table table-striped">
						<tr>
							<td><label for="">优惠券名称</label></td>
							<td><label for="">优惠券数量</label></td>
						</tr>
						<?php if(isset($value['prizes_content_list']['coupons'])){ foreach ($value['prizes_content_list']['coupons'] as $couponList) { ?>
							<tr>
								<td><?php echo $couponList['name'] ?></td>
								<td><?php echo $couponList['number'] ?></td>
							</tr>
						<?php } } ?>
					</table>

				</td>
				<td><?php echo $value['prizes_name'] ?></td>
				<td><?php echo $value['probability']."%" ?></td>
				<td><?php echo $value['prizes_num'] ?></td>
				<td><?php echo $value['people_prizes_num'] ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php } ?>

</div>
<script type="text/javascript">

</script>