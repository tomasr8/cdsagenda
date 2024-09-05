#!/bin/sh

ini_path=$(php -i | grep /.+/php.ini -oE)

# Allow <? ?> tags in PHP
sed -i "s/short_open_tag = .*/short_open_tag = On/" $ini_path
