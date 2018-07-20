#!/bin/bash -xe
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

docker run --rm -ti \
  --memory 128MB \
  --memory-reservation 128MB \
  -v $DIR:/srv/http \
  -p 80:80 \
  -p 8081:8081 \
  -e HOC_GITHUB_CLIENT \
  -e HOC_MYSQL_HOST \
  -e HOC_GITHUB_SECRET \
  -e HOC_MYSQL_NAME \
  -e HOC_MYSQL_USER \
  -e HOC_MYSQL_PASS \
  dhoodlum/legacy-havenofcode
