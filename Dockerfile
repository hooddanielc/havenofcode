FROM dhoodlum/arch-base-devel

RUN pacman --noconfirm -S apache
RUN pacman --noconfirm -S zsh
RUN pacman --noconfirm -S php
RUN pacman --noconfirm -S sudo
RUN pacman --noconfirm -S wget
RUN pacman --noconfirm -S git
RUN pacman --noconfirm -S postgresql

RUN apachectl restart

# add and use developer user
RUN useradd -m -G wheel -s /bin/zsh developer
USER developer
RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
USER root

# set default make flags
RUN echo MAKEFLAGS=\"-j$(grep -c ^processor /proc/cpuinfo)\" >> /etc/makepkg.conf

# setup sudo for developer
RUN echo "developer ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

# configure apache
RUN pacman --noconfirm -S php-apache
RUN pacman --noconfirm -S nano
RUN mkdir -p /srv/http
RUN echo "ServerName www.havenofcode.com:80" >> /etc/httpd/conf/httpd.conf
RUN sed -i.bak '/ServerAdmin/d' /etc/httpd/conf/httpd.conf
RUN echo ServerAdmin hood.danielc@gmail.com >> /etc/httpd/conf/httpd.conf
RUN sed -i.bak '/mod_mpm_event/d' /etc/httpd/conf/httpd.conf
RUN echo "LoadModule mpm_prefork_module modules/mod_mpm_prefork.so" >> /etc/httpd/conf/httpd.conf
RUN echo "LoadModule php7_module modules/libphp7.so" >> /etc/httpd/conf/httpd.conf
RUN echo "AddHandler php7-script php" >> /etc/httpd/conf/httpd.conf
RUN apachectl restart
RUN sed -i.bak '/mysqli.so/d' /etc/php/php.ini
RUN echo 'extension=mysqli.so' >> /etc/php/php.ini

# install phppgadmin
RUN pacman --noconfirm -S phppgadmin
RUN echo "extension=pgsql.so" >> /etc/php/php.ini
RUN echo "open_basedir = /srv/http/:/home/:/tmp/:/usr/share/pear/:/usr/share/webapps/:/etc/webapps" >> /etc/php/php.ini
ADD ./config/phppgadmin.conf /etc/httpd/conf/extra/phppgadmin.conf
RUN echo 'Include conf/extra/phppgadmin.conf' >> /etc/httpd/conf/httpd.conf
RUN sed -i.bak '/?>/d' /etc/webapps/phppgadmin/config.inc.php
RUN sed -i.bak '/host/d' /etc/webapps/phppgadmin/config.inc.php
RUN echo "\$conf['servers'][0]['host'] = \$_ENV['HOC_POSTGRES_HOST'];" >> /etc/webapps/phppgadmin/config.inc.php
RUN echo "?>" >> /etc/webapps/phppgadmin/config.inc.php
RUN apachectl restart

# install phpmyadmin
RUN pacman --noconfirm -S phpmyadmin
ADD ./config/phpmyadmin.conf /etc/httpd/conf/extra/phpmyadmin.conf
RUN echo 'Include conf/extra/phpmyadmin.conf' >> /etc/httpd/conf/httpd.conf
RUN sed -i.bak '/host/d' /etc/webapps/phpmyadmin/config.inc.php
RUN echo "\$cfg['Servers'][\$i]['host'] = \$_ENV['HOC_MYSQL_HOST'];" >> /etc/webapps/phpmyadmin/config.inc.php

# allow environment variables
RUN sed -i.bak "/variables_order\s\=\s/d" /etc/php/php.ini
RUN echo "variables_order = EGPCS" >> /etc/php/php.ini

# install the source code
ADD . /srv/http/
ADD ./config/havenofcode.conf /etc/httpd/conf/extra/havenofcode.conf
RUN echo 'Include conf/extra/havenofcode.conf' >> /etc/httpd/conf/httpd.conf

# mysql creds
ENV HOC_MYSQL_HOST xxx
ENV HOC_MYSQL_USER xxx
ENV HOC_MYSQL_PASS xxx
ENV HOC_MYSQL_NAME xxx

# git app id
ENV HOC_GITHUB_CLIENT xxx
ENV HOC_GITHUB_SECRET xxx

EXPOSE 80

CMD apachectl start -DFOREGROUND
