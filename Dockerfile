FROM bushelpowered/alpine-nginx:alpine3.9
MAINTAINER David Kanenwisher <dkanenwisher@bushelpowered.com>

copy ./build_local/ /var/www/public/