FROM ubuntu:trusty
MAINTAINER Simon Dittlmann <simon.dittlmann@tado.com>, Inaki Anduaga <inaki.anduaga@tado.com>

# ===============================================================================
# BASE UBUNTU + HHVM Installation
#
# Code Taken from brunoric/hhvm:deb ###
# ===============================================================================

#
# Flag commands as non-interactive
#
ENV DEBIAN_FRONTEND=noninteractive

#
# Bootstrap & general tools
#
RUN apt-get update && apt-get upgrade -y

#
# Installing packages
#
RUN wget -O - http://dl.hhvm.com/conf/hhvm.gpg.key | sudo apt-key add -
RUN echo deb http://dl.hhvm.com/ubuntu trusty main | sudo tee /etc/apt/sources.list.d/hhvm.list
RUN sudo apt-get update && apt-get -y install libgmp-dev libmemcached-dev hhvm-nightly
RUN apt-get clean && apt-get autoremove -y

#
# Make php cli use hhvm instead of php (so we can use composer, phpunit etc)
#
RUN sudo /usr/bin/update-alternatives --install /usr/bin/php php /usr/bin/hhvm 60
