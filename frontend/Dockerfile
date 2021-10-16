FROM node:15.12.0-buster-slim  

ENV NODE_OPTIONS=--max_old_space_size=4096
ENV HOST 0.0.0.0

RUN apt-get update

ENV APP_ROOT /src

WORKDIR ${APP_ROOT}
COPY . ${APP_ROOT}

RUN npm ci   
RUN npm run build 


