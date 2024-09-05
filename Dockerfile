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

COPY ./cdsagenda-4.2.9 /cdsagenda
COPY java /usr/bin/java
WORKDIR /cdsagenda

ARG MYSQL_USER=root
ARG MYSQL_PASSWORD=example
ARG MYSQL_DBNAME=cdsagenda
ARG MYSQL_HOST=db
ARG PORT=9090

# Remove the default index.html
RUN rm -f /var/www/localhost/htdocs/index.html
# Configure CDS Agenda
RUN ./configure \
        --with-htdocsdir=/var/www/localhost/htdocs \
        --with-cgibindir=/var/www/localhost/cgi-bin \
        --with-utildir=/usr/local/bin \
        --with-htdocsurl=http://localhost:$PORT \
        --with-cgibinurl=http://localhost:$PORT/cgi-bin \
        --with-dbhost=$MYSQL_HOST \
        --with-dbname=$MYSQL_DBNAME \
        --with-dbuser=$MYSQL_USER \
        --with-dbpass=$MYSQL_PASSWORD \
        --with-dbhostport=3306
# Install into /var/www/localhost/htdocs
RUN make install

COPY info.php /var/www/localhost/htdocs/info.php
COPY configure_php.sh /configure_php.sh
RUN /configure_php.sh

RUN chgrp -R 0 /var/www && \
    chmod -R g=u /var/www

RUN chgrp -R 0 /var/log/apache2 && \
    chmod -R g=u /var/log/apache2

RUN chgrp -R 0 /run/apache2 && \
    chmod -R g=u /run/apache2

RUN sed -i "s/Listen 80/Listen 9090/g" /etc/apache2/httpd.conf
RUN sed -i "s/User apache/User nobody/g" /etc/apache2/httpd.conf
RUN sed -i "s/Group apache/Group nogroup/g" /etc/apache2/httpd.conf
# RUN sed -i "s/#ServerName.*/ServerName localhost/" /etc/apache2/httpd.conf

EXPOSE 9090

CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
