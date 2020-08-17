ARG RT_GET_EVENTI=/servizi-cooperativi/webapi/preelettorale/comunicazionieventi
ARG RT_GET_CANDIDATI=/servizi-cooperativi/webapi/preelettorale/scheda
ARG RT_PUT_VOTANTI=/servizi-cooperativi/webapi/elettorale/votanti/invio
ARG RT_PUT_SCRUTINI=/servizi-cooperativi/webapi/elettorale/scrutinio/invio
ARG RT_PUT_PREFERENZE=/servizi-cooperativi/webapi/elettorale/preferenze/invio
ARG RT_HOST=https://gestione.elezioni2020.regione.toscana.it
ARG RT_SERVICE_USER=testws_firenze
ARG RT_EVENT_CONFIG_LIST=acquisizioneAffluenza,acquisizioneListe,acquisizionePreferenze,gestioneAffluenzaMF,gestioneSchedeBianche,gestioneSchedeNulle,gestioneVotiNulliCoalizione,gestioneVotiContestatiCoalizione,gestioneVotiNulliListe,gestioneVotiContestatiListe,gestioneVotoDisgiunto,numeroMassimoPreferenze,gestioneVotiDiCui
ARG RT_AFF_STATES=READY,POST_AFFLUENCE_4

# This file is part of the Kimai time-tracking app.
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
FROM php:7.2.9-apache-stretch AS tmp_divo_base

RUN apt update && \
    apt install -y --allow-unauthenticated \
        git \
        haveged \
        libicu-dev \
        libjpeg-dev \
        libldap2-dev \
        libldb-dev \
        libpng-dev \
        libpq-dev \
        mysql-client \
        unzip \
        wget \
        zip \
        && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    docker-php-ext-install \
        gd \
        intl \
        ldap \
        pdo_pgsql \
        zip && \
    apt remove -y wget && \
    apt -y autoremove && \
    apt clean && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

FROM tmp_divo_base

ARG DATABASE_URL
ARG BICORE_SCHEMA

RUN git clone https://github.com/ComuneFI/divo.git /opt/divo && \
    sed "s/prod/dev/g" /opt/divo/.env > /opt/divo/.env.local && \
    echo "BICORE_SCHEMA=${BICORE_SCHEMA}" >> /opt/divo/.env.local && \
    echo "DATABASE_URL=${DATABASE_URL}" >> /opt/divo/.env.local && \
    echo "RT_GET_EVENTI=${RT_GET_EVENTI}" >> /opt/divo/.env.local && \
    echo "RT_GET_CANDIDATI=${RT_GET_CANDIDATI}" >> /opt/divo/.env.local && \
    echo "RT_PUT_VOTANTI=${RT_PUT_VOTANTI}" >> /opt/divo/.env.local && \
    echo "RT_PUT_SCRUTINI=${RT_PUT_SCRUTINI}" >> /opt/divo/.env.local && \
    echo "RT_PUT_PREFERENZE=${RT_PUT_PREFERENZE}" >> /opt/divo/.env.local && \
    echo "RT_HOST=${RT_HOST}" >> /opt/divo/.env.local && \
    echo "RT_SERVICE_USER=${RT_SERVICE_USER}" >> /opt/divo/.env.local && \
    echo "RT_EVENT_CONFIG_LIST=${RT_EVENT_CONFIG_LIST}" >> /opt/divo/.env.local && \
    echo "RT_AFF_STATES=${RT_AFF_STATES}" >> /opt/divo/.env.local && \
    cat /opt/divo/.env.local && \
    composer install --working-dir=/opt/divo --optimize-autoloader && \
    composer require --working-dir=/opt/divo server && \
    /opt/divo/bin/console cache:warmup && \
    chown -R www-data:www-data /opt/divo/var

WORKDIR /opt/divo

EXPOSE 8001
USER www-data
CMD /opt/divo/bin/console server:run 0.0.0.0:8001