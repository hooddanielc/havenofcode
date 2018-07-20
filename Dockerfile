FROM dhoodlum/arch-base-devel

RUN pacman --noconfirm -Syyu

RUN pacman --noconfirm -S apache \
  zsh \
  php \
  sudo \
  wget \
  git \
  postgresql \
  php-apache \
  nano \
  phppgadmin \
  phpmyadmin

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

# add configuration
ADD ./config/httpd/httpd.conf /etc/httpd/conf/httpd.conf
ADD ./config/httpd/extra/havenofcode.conf /etc/httpd/conf/extra/havenofcode.conf
ADD ./config/httpd/extra/phpmyadmin.conf /etc/httpd/conf/extra/phpmyadmin.conf
ADD ./config/httpd/extra/phppgadmin.conf /etc/httpd/conf/extra/phppgadmin.conf
ADD ./config/php/php.ini /etc/php/php.ini
ADD ./config/webapps/phpmyadmin/config.inc.php /etc/webapps/phpmyadmin/config.inc.php
ADD ./config/webapps/phppgadmin/config.inc.php /etc/webapps/phppgadmin/config.inc.php

# install the source code
RUN mkdir -p /srv/http
ADD . /srv/http/

# environment
ENV HOC_MYSQL_HOST xxx
ENV HOC_MYSQL_USER xxx
ENV HOC_MYSQL_PASS xxx
ENV HOC_MYSQL_NAME xxx
ENV HOC_GITHUB_CLIENT xxx
ENV HOC_GITHUB_SECRET xxx
ENV HOC_HTTP_PORT 8081

EXPOSE 8081
EXPOSE 80

ENTRYPOINT ["/srv/http/entrypoint.sh"]
CMD ["apachectl", "start", "-DFOREGROUND"]
