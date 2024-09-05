FROM mariadb:11-ubi9

COPY tabcreate.sql /docker-entrypoint-initdb.d/000-tabcreate.sql

USER root
RUN chgrp -R 0 /var/lib/mysql && \
    chmod -R g=u /var/lib/mysql && \
    chmod -R 777 /var/lib/mysql
