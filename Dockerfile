FROM alpine:3.20

RUN apk update && apk add --no-cache \
    bash wget make \
    mysql-client \
    php php-mysqli php-xml php-session \
    apache2 php-apache2

# Install PHP PEAR
RUN wget http://pear.php.net/go-pear.phar
RUN php go-pear.phar

# Install the DB package required by CDS Agenda
RUN pear install DB

# Copy the CDS Agenda source code
COPY ./cdsagenda-4.2.9 /cdsagenda
WORKDIR /cdsagenda

# Copy fake java needed by configure
COPY java /usr/bin/java

# Remove the default index.html
RUN rm -f /var/www/localhost/htdocs/index.html

ARG MYSQL_USER=root
ARG MYSQL_PASSWORD=example
ARG MYSQL_DBNAME=cdsagenda
ARG MYSQL_HOST=db
ARG HOST=http://localhost:9090

# Configure CDS Agenda
RUN ./configure \
        --with-htdocsdir=/var/www/localhost/htdocs \
        --with-cgibindir=/var/www/localhost/cgi-bin \
        --with-utildir=/usr/local/bin \
        --with-htdocsurl=$HOST \
        --with-cgibinurl=$HOST/cgi-bin \
        --with-dbhost=$MYSQL_HOST \
        --with-dbname=$MYSQL_DBNAME \
        --with-dbuser=$MYSQL_USER \
        --with-dbpass=$MYSQL_PASSWORD \
        --with-dbhostport=3306

# Install into /var/www/localhost/htdocs
RUN make install

# Expose php info at $HOST/info.php
COPY info.php /var/www/localhost/htdocs/info.php

# Configure php
COPY configure_php.sh /configure_php.sh
RUN /configure_php.sh

# Convince apache to run as non-root
RUN chgrp -R 0 /var/www && \
    chgrp -R 0 /var/log/apache2 && \
    chgrp -R 0 /run/apache2 && \
    chmod -R g=u /var/www && \
    chmod -R g=u /var/log/apache2 && \
    chmod -R g=u /run/apache2

RUN sed -i "s/Listen 80/Listen 9090/g" /etc/apache2/httpd.conf
RUN sed -i "s/User apache/User nobody/g" /etc/apache2/httpd.conf
RUN sed -i "s/Group apache/Group nogroup/g" /etc/apache2/httpd.conf
RUN sed -i "s/#ServerName.*/ServerName $HOST/g" /etc/apache2/httpd.conf

EXPOSE 9090

CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
