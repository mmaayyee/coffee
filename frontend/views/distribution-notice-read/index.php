<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\WxMember;
use yii\helpers\Url;
use backend\models\Manager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionNoticeReadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = "配送通知列表";
?>
<style>
	.text1{
		overflow: hidden;
		display: -webkit-box;
		text-overflow: ellipsis;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}
</style>
<div class="distribution-notice-read-index">
	<?php if(!$list){ ?>
	<div style="margin: 20% 0;text-align: center;">
		<div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
		<p style="font-size: 1.4rem">暂无数据</p>	
	</div>
	<?php }else{ ?>
	<div class="panel panel-default">
   		<div class="panel-heading">
      		<h3 class="panel-title" style="text-align:center;margin: 0 auto;">
					通知
      		</h3>
   		</div>
   		<div class="panel-body">   			
    		<?php foreach ($list as $key => $value) { ?>
				<?php if(isset($value->distributionNotice->sender)){  
					echo '<div class="row"><div class="col-xs-6">'.Manager::getUserName($value->distributionNotice->sender);
					}else{
						continue;
					} ?>
					</div>
					<div class="col-xs-6">
						<?php if($value->read_status==0){ ?>
							<p style="color:red;"><?php echo date("Y-m-d H:i", $value->distributionNotice->create_time) ?></p>
						<?php }else{ ?>
							<p><?php echo date("Y-m-d H:i", $value->distributionNotice->create_time) ?></p>	
						<?php } ?>	
					</div>
						
				<div class="col-xs-12" >											
					<a class="text1" href="<?php echo Url::to(['distribution-notice-read/detial','notice_id'=>$value->notice_id, "notice_read_id"=>$value->Id]) ?>"><?php echo $value->distributionNotice->content; ?></a>					
				</div>
			</div>
	        <hr/>	
			<?php } ?>	
		</div>		
	</div>
	<?php } ?>		
</div>	
