FROM mysql:8.0-bookworm

COPY tabcreate.sql /docker-entrypoint-initdb.d/000-tabcreate.sql
COPY usercreate.sql /docker-entrypoint-initdb.d/001-usercreate.sql
COPY tabfill.sql /docker-entrypoint-initdb.d/002-tabfill.sql

