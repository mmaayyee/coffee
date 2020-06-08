#!/bin/sh
source /etc/profile
killall -9 php
nohup php /alidata/www/coffee/server.php &
/alidata/www/coffee/yii backend/clear-socket >> /alidata/www/coffee/backend/crontab.log