#!/bin/sh

ini_path=$(php -i | grep /.+/php.ini -oE)

# Allow <? ?> tags in PHP
sed -i "s/short_open_tag = .*/short_open_tag = On/" $ini_path

sed -i "s/;error_log = php_errors.log/error_log = STDERR/" $ini_path
