FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Base
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y locales locales-all

# Locales
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US
ENV LC_ALL en_US.UTF-8

# Packages
RUN apt-get install -y software-properties-common
RUN apt-get install -y python3-pip

RUN add-apt-repository -y ppa:certbot/certbot
RUN apt-get install -y certbot

#RUN apt-get purge -y python3-cryptography
#RUN pip3 install cryptography
#RUN pip3 install certbot_dns_route53==0.22.2

CMD ["/bin/bash"]
