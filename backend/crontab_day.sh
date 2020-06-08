#!/bin/sh
source /etc/profile
/alidata/www/coffee/yii backend/clear >> /alidata/www/coffee/backend/crontab.log
/alidata/www/coffee/yii interest/giftback >> /alidata/www/coffee/backend/crontab.log
/alidata/www/coffee/yii interest/back-activity >> /alidata/www/coffee/backend/crontab.log
/alidata/www/coffee/yii interest/interest-calc >> /alidata/www/coffee/backend/crontab.log