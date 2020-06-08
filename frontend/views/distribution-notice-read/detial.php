<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\WxMember;
use yii\helpers\Url;
use backend\models\Manager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionNoticeReadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = "配送通知详情";
?>

<div>
	<div>
		<label>发件人：</label>
		<?php echo Manager::getUserName($noticeList['sender']); ?>
	</div>
	<div>
		<label>时间：</label>
		<?php echo date("Y-m-d H:i", $noticeList['create_time']) ?>
	</div>
	<div>
		<label>内容：</label>
		<?php echo $noticeList['content'] ?>
	</div>
	
	<?php if($noticeReadList['read_status']==0){ ?>
		<!-- form表单     -->
	    <?php $form = ActiveForm::begin(['action' => ['distribution-notice-read/notice-read-success','notice_id'=>$noticeList['Id']], 'method'=>'get']); ?>
	    
	    <div class="form-group add-send">
	       	<label>反馈内容：</label>
	       	<textarea class="form-control" name="read_feedback" cols="50" rows="7" placeholder='最多不超过为100字' maxLength="100"></textarea>
	    </div>

	    <?= Html::submitButton( '确认并反馈' , ['class' => 'btn btn-block btn-success btn-refuse-click' ]) ?>
	    <?php ActiveForm::end(); ?>
	<?php }else{ ?>
		<div class="form-group add-send">
	        <label>反馈内容：</label>
	        <?php echo $noticeReadList['read_feedback'] ?>
	    </div>
	<?php } ?>


</div>

