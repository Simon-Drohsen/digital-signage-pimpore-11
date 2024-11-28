FROM pimcore/pimcore:php8.3-debug-latest

# Install PHP XSL extension and Open SSH Server
RUN set -eux; \
  build-install.sh; \
  apt-get install -y --no-install-recommends \
      libxslt-dev \
      openssh-server; \
  docker-php-ext-install \
    ftp \
    xsl;
