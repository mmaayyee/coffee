<?php

use common\models\WxMember;
use backend\models\EquipDelivery;
use backend\models\Organization;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipDelivery */

$this->title                   = $model->Id;
$this->params['breadcrumbs'][] = ['label' => '销售投放管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
	.container{
		position: relative;
	}
	.btn2{
		width:20%;
	}
	.btn-danger{
		margin-left: 10%;
	}
	.field-equipdelivery-grounds_refusal{
		display: none;
	}
	.modal-body {
		padding-top: 0px;
	}
	.modal-header{
		padding-bottom: 0px;
		border-bottom:none;
	}
    td {
        word-break:break-word;
    }
    th{
        width:10%;
    }
</style>
<div class="equip-delivery-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'build_id',
            'value'     => \common\models\Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] ? \common\models\Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] : '暂无',
        ],
        [
            'attribute' => 'org_id',
            'value'     => $model->build->org_id ? Organization::getOrgNameByID($model->build->org_id) : '暂无',
        ],
        [
            'attribute' => 'equip_type_id',
            'value'     => \backend\models\ScmEquipType::getEquipTypeDetail('model', ['id' => $model->equip_type_id])['model'] ? \backend\models\ScmEquipType::getEquipTypeDetail('model', ['id' => $model->equip_type_id])['model'] : '暂无',
        ],
        [
            'attribute' => 'delivery_time',
            'value'     => !empty($model->delivery_time) ? date('Y-m-d', $model->delivery_time) : '暂无',
        ],
        [
            'attribute' => 'sales_person',
            'value'     => $model->sales_person,
        ],
        [
            'attribute' => 'delivery_status',
            'value'     => $model->equipDeliveryStatusArray($model->delivery_status)[$model->delivery_status],
        ],
        'reason',
        'remark',
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
        ],
        'voice_type',
        [
            'attribute' => 'is_ammeter',
            'value'     => $model->getIsNeedArr()[$model->is_ammeter],
        ],
        [
            'attribute' => 'is_lightbox',
            'value'     => isset($model::getLightBoxArr()[$model->is_lightbox]) ? $model::getLightBoxArr()[$model->is_lightbox] : '',
        ],
        'special_require',
        [
            'attribute' => 'update_time',
            'value'     => !empty($model->update_time) ? date('Y-m-d H:i:s', $model->update_time) : '暂无',
        ],
        [
            'attribute' => 'delivery_result',
            'value'     => $model->deliveryResultArray($model->delivery_result)[$model->delivery_result],
        ],

        'grounds_refusal',
    ],
]);?>

    <?php if (!empty($sign)) {?>
        <!-- form表单     -->
        <?php $form = ActiveForm::begin(['action' => ['equip-delivery/refuse-des', 'id' => $model->Id], 'method' => 'post']);?>
		<?=$form->field($model, 'build_id')->hiddenInput(['value' => $model->build_id])->label(false);?>
		<input type="hidden" class="build_id" value="<?php echo $model->Id; ?>">
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		            </div>
		            <div class="modal-body">
		        		<?=$form->field($model, 'grounds_refusal')->textarea(['rows' => 6, 'maxlength' => 200, 'placeholder' => '最多输入200字符!']);?>
		        		<div id = "prompt" style="color:red;"></div>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <?=Html::button('确定', ['class' => 'btn btn-primary btn2','onclick' => 'checkSubmit()']);?>
		            </div>
		        </div>
		    </div>
		</div>
        <?php ActiveForm::end();?>
        <!-- form表单     -->
        <?php $form = ActiveForm::begin(['action' => ['equip-delivery/check-success', 'id' => $model->Id, 'build_id' => $model->build_id], 'method' => 'get']);?>
        <div class="form-group add-send">
            <h4>发送信息人：</h4>
        </div>
        <div class="form-group " style="text-align: center;">
        <?=Html::submitButton('通过', ['class' => 'btn btn-success btn-refuse-click btn2']);?>
        <?=Html::button('拒绝', ['class' => 'btn btn-danger btn2']);?>
        <?php ActiveForm::end();?>
		</div>
    <?php }
;?>
</div>

<script  src='/js/jquery-1.8.3.min.js'></script>
<script type="text/javascript">
var positions = <?php echo json_encode(WxMember::$position); ?>;
    $(function() {
        var buildId   =   $("#equipdelivery-build_id").val();
        $.get("<?php echo Url::to(['equip-delivery/get-sender']); ?>" , {'buildId': buildId},function(data){
            var td = "";
            $(".add-send tr").empty();
            for (var i in data) {
                td += "<tr><td>"+positions[data[i]['position']]+"：</td><td>"+data[i]['name']+"</td></tr>";
            }
            $(".add-send").append(td);
        },
        'json'
        );
    });
    //拒绝按钮时检测是否有值，
    $(".btn-danger").click(function() {
		$('#myModal').modal();
    	$(".field-equipdelivery-grounds_refusal").show();

    });

    function checkSubmit(){
        var groundsRefusal = $("#equipdelivery-grounds_refusal").val();
        if(!groundsRefusal){
            $("#prompt").text('驳回理由不可为空');
            return false;
        }else{
            $("#prompt").text('');
            $('#w1').submit();
            $('.modal-footer .btn2').attr('disabled', true);
        }
    }
</script>
<?php if (empty($sign)) {
    ?>
<?php if (isset($deliveryReadModel)) {
        ?>
<div class="equip-delivery-read-index">

    <h1><?=Html::encode("相关人员阅读情况");?></h1>
    <?=GridView::widget([
            'dataProvider' => $dataProvider,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => '职位名称',
                    'value'     => function ($model) {
                        return WxMember::$position[WxMember::getMemberDetail('*', array('userId' => $model->userId))['position']];
                    },
                ],

                [
                    'attribute' => 'read_type',
                    'value'     => function ($deliveryReadModel) {
                        return $deliveryReadModel->read_type ? "投放单类型" : "预投放类型";
                    },
                ],

                [
                    'attribute' => 'userId',
                    'value'     => function ($deliveryReadModel) {
                        return $deliveryReadModel->member ? $deliveryReadModel->member->name : '';
                    },
                ],
                [
                    'attribute' => 'read_status',
                    'value'     => function ($deliveryReadModel) {
                        return !empty($deliveryReadModel->read_status) ? '已阅读' : '未阅读';
                    },
                ],
                [
                    'attribute' => 'read_time',
                    'value'     => function ($deliveryReadModel) {
                        return !empty($deliveryReadModel->read_time) ? date("Y-m-d H:i", $deliveryReadModel->read_time) : '';
                    },
                ],
                'read_feedback',
            ],
        ]);?>

    <p>
        <?php if ($model->delivery_status == EquipDelivery::PENDING && empty($sign)) :?>
            <?php echo  !\Yii::$app->user->can('审核销售投放') ? '' : Html::a('去审核', ['/equip-delivery/view', 'id' => $model->Id, 'sign' => 'check'], ['class' => 'btn btn-primary']);?>
        <?php endif;?>
    </p>

</div>
<?php }}?>
