<?php
use backend\models\DistributionSparePackets;
use yii\helpers\Html;
$this->registerJs('
    $("#chooseStockSave").click(function(){
        if (!$("#stock-id").val()) {
            $("#stock-id").next().html("请选择分库");
            $("#stock-id").parent().addClass("has-error");
            return false;
        } else {
            $("#stock-id").next().html("");
            $("#stock-id").parent().removeClass("has-error");
        }
    })
')
?>
<div class="modal fade bs-example-modal-sm" id="choose-stock" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="/scm-warehouse-out/send-out-bill">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">选择分库</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>选择分库</label>
                        <select class="form-control" name="stock_id" id="stock-id"></select>
                        <div class="help-block"></div>
                        <?=Html::hiddenInput('date', '', ['id' => 'date'])?>
                        <?=Html::hiddenInput('author', '', ['id' => 'author'])?>
                        <?=Html::hiddenInput('_csrf', Yii::$app->getRequest()->getCsrfToken())?>
                    </div>
                    <div class="recevie-material">
                        <dl>
                            <dt>任务所需物料</dt>
                            <dd id="task-material"></dd>
                        </dl>
                        <dl>
                            <dt>备用物料</dt>
                            <dd id="spare-material"><?php echo DistributionSparePackets::getSparePacktesContent(); ?></dd>
                        </dl>
                        <dl>
                            <dt>配送员手中的剩余物料</dt>
                            <dd id="surplus-material"></dd>
                        </dl>
                        <dl>
                            <dt>需要领取物料</dt>
                            <dd id="receive-material"></dd>
                        </dl>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" id="chooseStockSave" class="btn btn-primary spare-packets">确定</button>
                </div>
            </form>
        </div>
    </div>
</div>