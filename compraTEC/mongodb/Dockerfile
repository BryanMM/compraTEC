FROM mongo:3.2
COPY init.sh /tmp/init.sh
RUN chmod +x /tmp/init.sh
LABEL maintainer="Eric Smalling <smalls@docker.com>"
EXPOSE 27017
CMD /tmp/init.sh
