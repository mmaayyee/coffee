<div class="modal fade" id="cleaning" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">修改清洗周期</h4>
            </div>
            <div class="modal-body">
                <form action="" id="cleaning_cycle">
                <?php if ($equipTypeIdNameArr) { foreach($equipTypeIdNameArr as $k=>$v) {?>
                <div class="form-group form-inline">
                    <label><?php echo $v->model; ?></label>
                    <input type="hidden" value="<?php echo $v->id; ?>" name="data[<?php echo $k; ?>][equip_type_id]" />
                    <input type="text" class="form-control" name="data[<?php echo $k; ?>][cleaning_cycle]" value="<?php if($taskSetting && isset($taskSetting[$v->id])) { echo $taskSetting[$v->id]['cleaning_cycle'];} ?>"/> 天
                </div>
                <?php }} ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary cleaning_cycle">确定</button>
            </div>
        </div>
    </div>
</div>