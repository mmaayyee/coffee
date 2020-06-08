<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '节假日管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('/js/fullYear/bootstrap-3.3.5/css/bootstrap.min.css', ['position' => View::POS_HEAD]);
$this->registerCssFile('/js/fullYear/css/bootstrap-year-calendar.min.css', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/js/fullYear/js/jquery-2.1.1.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/js/fullYear/js/bootstrap-datepicker.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/js/fullYear/js/bootstrap-year-calendar.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/js/fullYear/js/bootstrap-popover.js', ['position' => View::POS_HEAD]);

?>
<style type="text/css">
    .selected {
        background-color: #34A522;
        color: white;
        border-radius: 30px;
    }
</style>
<div class="holiday-index">
    <div id="calendar"></div>
</div><br/>
<div class="form-group">
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::hiddenInput('exist_date', '') ?>
    <div class="form-group text-center">
        <?= Html::submitButton('更新', ['class' => 'btn btn-primary','id' => 'holiday_button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    //DOC-[http://www.bootstrap-year-calendar.com/#Documentation/Events]
    //格式化日趋
    Date.prototype.format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "h+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ?
                    (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            }
        }
        return fmt;
    }

    var holiday = '<?php echo $holiday;?>';
    var holidayObj = JSON.parse(holiday);
    var list = [];
    $.each(holidayObj, function (i, item) {
        list.push(item.date_day);
    });

    $('#calendar').calendar({
        startYear: 2018,
        enableRangeSelection: true,
        renderDay: function (e) {
            //判断存在的日期选中
            if ($.inArray(e.date.format("yyyy-MM-dd"), list) > -1) {
                $(e.element).attr('data-date', e.date.format("yyyy-MM-dd"));
                $(e.element).parent().addClass('selected');
            }
        }
    });

    $('#calendar').clickDay(function (e) {
        var date = new Date(e.date);
        console.log(date);
        if ($(e.element).children('div').attr('data-date') == undefined) {
            $(e.element).children('div').attr('data-date', date.format("yyyy-MM-dd"));
            e.element.addClass('selected');
        } else {
            $(e.element).children('div').removeAttr('data-date');
            e.element.removeClass('selected');
        }
    });

    $("#holiday_button").click(function () {
        $(this).attr('disabled', true);

        var checkedDate = '';
        $('.day div').each(function (i) {
            if ($(this).attr('data-date') != undefined) {
                checkedDate += $(this).attr('data-date') + ',';
            }
        })
        $('[name=exist_date]').val(checkedDate.substr(0, checkedDate.length - 1));
        $('form').submit();
        return false;
    });


</script>