DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

docker run -ti \
  -v $DIR:/srv/http \
  -p 80:80 \
  dhoodlum/legacy-havenofcode
