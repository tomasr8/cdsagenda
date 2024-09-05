#!/bin/bash

container_id=$(docker container list | awk '$2 == "cdsagenda-web" {print $1}')
docker exec $container_id cat /var/log/apache2/error.log
