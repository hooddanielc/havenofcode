#!/bin/bash -xe
if [ -n "$HOC_HTTP_PORT" ]
then
  echo "Listen $HOC_HTTP_PORT" >> /etc/httpd/conf/httpd.conf
fi
"$@"
