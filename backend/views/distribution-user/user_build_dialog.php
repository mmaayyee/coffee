<?php
use yii\helpers\Html;
?>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content step_1">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <button type="button" class="btn" id="step_2">确认楼宇选择</button>
            </div>
            <div class="modal-body"></div>
        </div>
        <div class="modal-content step_2">
            <div class="modal-header">
                <button type="button" class="btn" id="submit">完成路线创建</button>
                <button type="button" class="btn back" id="step_1">返回上一步</button>
            </div>
            <div class="modal-body">
                <p>请选择运维人员</p>
                <?=Html::radioList('distribution_user', '', $userArr);?>
            </div>
        </div>
         <div class="modal-content step_3">
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="edit_distribution_build" data-userid="">修改运维人员的楼宇</button>
                <button type="button" class="btn btn-default"  data-dismiss="modal">退出</button>
            </div>
        </div>
    </div>
</div>