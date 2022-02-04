FROM williarin/php:8.0

WORKDIR /srv/app
COPY . .

CMD ["php", "bin/console", "app:anonymize"]
