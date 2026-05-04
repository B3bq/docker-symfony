FROM httpd:latest

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y nano vim curl git
    
RUN echo "" >> /usr/local/apache2/conf/httpd.conf && \
  echo "Include /usr/local/apache2/conf/overwrite/**" >> /usr/local/apache2/conf/httpd.conf