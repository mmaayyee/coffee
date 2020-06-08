<?php

$this->registerJsFile("/js/nine_lottery_activity.js?v=3.2", ["depends" => ["backend\assets\AppAsset"]]);
use backend\models\Activity;
?>
<div class="form-inline frequency">
    <?=$form->field($model, 'person_day_frequency')->textInput(['maxlength' => '6', 'check-type' => 'required nonnegativeInteger'])?>
    <?=$form->field($model, 'max_frequency')->textInput(['maxlength' => '6', 'check-type' => 'required nonnegativeInteger mostTimes'])?>
    <span class="size-tip">（*0代表无限参与次数）</span>
</div>

<div class="lottery-activity scratchable_latex">
    <input type="hidden" id="csrf" value="<?php echo Yii::$app->request->csrfToken; ?>">
    <div class="form-group field-activity-awards_num">
        <label class="control-label" for="activity-awards_num">奖项数目</label>
        <select id="activity-awards_num" class="form-control" name="Activity[awards_num]" onchange="prizesNumChange($(this))" check-type="required">

            <?php foreach (Activity::getAwardsNumList() as $key => $value) {?>
                <?php if ($key == $model->awards_num) {?>
                    <option value="<?php echo $key; ?>" selected='selected'><?php echo $value ?></option>
                <?php } else {?>
                    <option value="<?php echo $key; ?>" ><?php echo $value ?></option>
                <?php }?>
            <?php }?>

        </select>
    </div>

    <div class="form-group field-activity-background_music">
        <label class="control-label" for="activity-background_music">背景音乐<span class="size-tip">（格式：MP3）</span></label>
        <input type="file" id="activity-background_music" name="background_music" check-type="required musicFormat" musicFormat-message="">
        <div>
            <?php echo (isset($model->background_music) && $model->background_music) ? Yii::$app->params['fcoffeeUrl'] . $model->background_music : "" ?>
        </div>
    </div>
    <div class="form-group field-activity-background_photo">
        <label class="control-label" for="activity-background_photo">背景图片<span class="size-tip">（尺寸：750&times;1334px）</span></label>
        <input type="file" id="activity-background_photo" name="background_photo" check-type="required">
        <!-- background_photo -->
        <div class="imgdiv"><img src="<?php echo (isset($model->background_photo) && $model->background_photo) ? Yii::$app->params['fcoffeeUrl'] . $model->background_photo : "" ?>" id="activity-background_photo_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-activity_tips">
        <label class="control-label" for="activity-activity_tips">活动锦囊<span class="size-tip">（尺寸：120&times;120px）</span></label>
        <input type="file" id="activity-activity_tips" name="activity_tips" check-type="required">
        <!-- activity_tips -->
        <div class="imgdiv"><img src="<?php echo (isset($model->activity_tips) && $model->activity_tips) ? Yii::$app->params['fcoffeeUrl'] . $model->activity_tips : "" ?>" id="activity-activity_tips_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-title_photo">
        <label class="control-label" for="activity-title_photo">标题图片<span class="size-tip">（尺寸：556&times;321px）</span></label>
        <input type="file" id="activity-title_photo" name="title_photo" check-type="required">
        <!-- title_photo -->
        <div class="imgdiv"><img src="<?php echo (isset($model->title_photo) && $model->title_photo) ? Yii::$app->params['fcoffeeUrl'] . $model->title_photo : "" ?>" id="activity-title_photo_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-activity_background">
        <label class="control-label" for="activity-activity_background">活动背景图<span class="size-tip">（尺寸：534&times;534px）</span></label>
        <input type="file" id="activity-activity_background" name="activity_background" check-type="required">
        <!-- activity_background -->
        <div class="imgdiv"><img src="<?php echo (isset($model->activity_background) && $model->activity_background) ? Yii::$app->params['fcoffeeUrl'] . $model->activity_background : "" ?>" id="activity-activity_background_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-light_one_backgroup">
        <label class="control-label" for="activity-light_one_backgroup">灯泡背景1<span class="size-tip">（尺寸：28&times;28px）</span></label>
        <input type="file" id="activity-light_one_backgroup" name="light_one_backgroup" check-type="required">
        <!-- light_one_backgroup -->
        <div class="imgdiv"><img src="<?php echo (isset($model->light_one_backgroup) && $model->light_one_backgroup) ? Yii::$app->params['fcoffeeUrl'] . $model->light_one_backgroup : "" ?>" id="activity-light_one_backgroup_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-light_two_backgroup">
        <label class="control-label" for="activity-light_two_backgroup">灯泡背景2<span class="size-tip">（尺寸：28&times;28px）</span></label>
        <input type="file" id="activity-light_two_backgroup" name="light_two_backgroup" check-type="required">
        <!-- light_two_backgroup -->
        <div class="imgdiv"><img src="<?php echo (isset($model->light_two_backgroup) && $model->light_two_backgroup) ? Yii::$app->params['fcoffeeUrl'] . $model->light_two_backgroup : "" ?>" id="activity-light_two_backgroup_img" width="120" height="120" /></div>
    </div>
    <div class="form-group field-activity-lottery_button">
        <label class="control-label" for="activity-lottery_button">抽奖按钮<span class="size-tip">（尺寸：134&times;134px）</span></label>
        <input type="file" id="activity-lottery_button" name="lottery_button" check-type="required">
        <!-- lottery_button -->
        <div class="imgdiv"><img src="<?php echo (isset($model->lottery_button) && $model->lottery_button) ? Yii::$app->params['fcoffeeUrl'] . $model->lottery_button : "" ?>" id="activity-lottery_button_img" width="120" height="120" /></div>
        <div class="help-block"></div>
    </div>
    <div class="form-group field-activity-click_effect">
        <label class="control-label" for="activity-click_effect">点选效果<span class="size-tip">（尺寸：134&times;134px）</span></label>
        <input type="file" id="activity-click_effect" name="click_effect" check-type="required">
        <!-- click_effect -->
        <div class="imgdiv"><img src="<?php echo (isset($model->click_effect) && $model->click_effect) ? Yii::$app->params['fcoffeeUrl'] . $model->click_effect : "" ?>" id="activity-click_effect_img" width="120" height="120" /></div>
        <div class="help-block"></div>
    </div>
    <div class="award"></div>
</div>
<script id="awardListTpl" type="text/html">
    <div class="grid-list">
     <h5><strong>方格顺序：从左上角开始，按照顺时针方向依次排序</strong></h5>
    {{# $.each(d.gridList, function(index, item){  }}
        <div class="form-inline">
            <input type="hidden" name="Activity[grid_code][]" value="{{item.grid_code}}">
            <label>{{ item.grid_name }}<span class="size-tip">（尺寸：134&times;134px）</span></label>
            <div class="form-group">
                <!-- grid_photo -->
                <input type="file" id="activity-grid_photo_{{index}}" name="lottery[{{index}}]" check-type="required">
                <div class="imgdiv"><img src="{{item.grid_photo}}" id="activity-grid_photo_{{index}}_img" width="120" height="120" /></div>
            </div>
            <select class="form-control" name="Activity[awards_id][]" check-type="required">
                {{# $.each(d.awardsList, function(key, value){ }}
                    {{# if(item.awards_id == value.awards_id){ }}
                    <option value="{{value.awards_id}}" selected="selected">{{value.awards_name}}</option>
                    {{# }else{ }}
                    <option value="{{value.awards_id}}" >{{value.awards_name}}</option>
                    {{# } }}
                {{#  }) }}
            </select>
        </div>
    {{# }) }}
    </div>
    <div class="award-setting">
    {{# $.each(d.winSetting, function(index, item){  }}
        <h5><strong>奖项设置</strong></h5>
        <div class="form-inline">
            {{# $.each(d.awardsList, function(key, value){ }}
                {{# if (value.awards_id == item.awards_id) {   }}
                    <div class="form-group">
                        <label for="">奖项等级</label>
                        <input class="form-control" type="text" readonly="readonly"  value="{{value.awards_name}}"/>
                        <input type="hidden" name="ActivitySetting[awards_id][]" value="{{item.awards_id}}">
                    </div>
                {{# } }}
            {{#  }) }}
            <div class="form-group">
                <label for="">奖品类型</label>
                <select class="form-control" id="prizesType" name="ActivitySetting[prizes_type][]" onchange="prizesTypeChange(this)" check-type="required">
                    <option value="">请选择</option>
                    {{# if (item.prizes_type == 1){  }}
                        <option  selected="selected" value="1">优惠套餐</option>
                        <option value="2">实物奖励</option>
                    {{# }else if (item.prizes_type == 2) { }}
                         <option value="1">优惠套餐</option>
                        <option selected="selected" value="2">实物奖励</option>
                    {{# }else{  }}
                        <option value="1">优惠套餐</option>
                        <option value="2">实物奖励</option>
                    {{# } }}
                </select>
            </div>
            {{# if (item.prizes_type == 1){  }}
                <div class="form-group prizes-content" style="visibility:visible">
                    <select class="form-control" name="ActivitySetting[prizes_content][]" check-type="required">
                        {{# $.each(d.couponGroupList, function(key, value){ }}
                            {{# if(item.prizes_content == value.coupon_group_id ){ }}
                                <option selected="selected" value="{{value.coupon_group_id}}">{{value.coupon_group_name}}</option>
                            {{# }else{  }}
                                <option value="{{value.coupon_group_id}}">{{value.coupon_group_name}}</option>
                            {{# }  }}
                        {{# }); }}
                    </select>
                </div>
            {{# }else{  }}
                <div class="form-group prizes-content">
                    <select class="form-control" name="ActivitySetting[prizes_content][]">
                        {{# $.each(d.couponGroupList, function(key, value){ }}
                            <option value="{{value.coupon_group_id}}">{{value.coupon_group_name}}</option>
                        {{# }); }}
                    </select>
                </div>
            {{# } }}
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label for="">奖品名称</label>
                <input class="form-control" type="text" name="ActivitySetting[prizes_name][]" value="{{item.prizes_name}}" check-type="required" maxlength="20">
            </div>
            <div class="form-group">
                <label for="">中奖概率</label>
                <input class="form-control" type="text" name="ActivitySetting[probability][]" value="{{item.probability}}" range="0.00001~100" check-type="required number plus" maxlength="7">
            </div>
            <div class="form-group">
                <label for="">奖品总量</label>
                <input class="form-control" type="text" name="ActivitySetting[prizes_num][]" value="{{item.prizes_num}}" check-type="required nonnegativeInteger" maxlength="6">
            </div>
            <div class="form-group">
                <label for="">单人中奖次数</label>
                <input class="form-control" type="text" name="ActivitySetting[people_prizes_num][]" value="{{item.people_prizes_num}}" check-type="required ints" maxlength="6">
            </div>
        </div>
    {{# }) }}
    </div>
</script>