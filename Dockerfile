FROM myriadmobile/alpine-nginx-php:7.2
MAINTAINER David Kanenwisher <dkanenwisher@bushelpowered.com>

copy ./build_local/ /var/www/public/