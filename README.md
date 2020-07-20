# Divo
[![Build Status](https://travis-ci.org/ComuneFI/divo.svg?branch=master)](https://travis-ci.org/ComuneFI/divo)

> ⚠️ **WORK IN PROGRESS** ⚠️

## Per iniziare

**NOTA**: richiede PHP 7.2 e PostgreSQL >= 10.13 

### Prerequisiti

Testato su Debian e CentOS 7 ma compatibile con altre distribuzioni Linux.

- apache HTTP server 
- Composer (https://getcomposer.org/) 
- git

Moduli php

- php7.*-xml  
- php7.*-intl  
- php7.*-mbstring  
- php7.*-sqlite3  
- php7.*-zip 
- php7.*-gd 
- php7.*-curl 
- php7.*-bz2 
- php7.*-pgsql 

### Installazione

ATTENZIONE: Il DataBase e lo schema del DataBase non devono avere lo stesso nome, si consiglia di nominare il DB “divo” e lo schema “divoschema”

```
git clone https://github.com/ComuneFi/divo.git
cd divo
#Copiare il file `.env` in `.env.local` per impostare il database da utilizzare
##DATABASE_URL https://symfony.com/doc/current/doctrine.html#configuring-the-database
### es. DATABASE_URL="pgsql://db_user:db_password@127.0.0.1:3306/db_name"
##BICORE_SCHEMA (nome schema nel database)
### es. BICORE_SCHEMA=divoschema
#Infine per abilitare l'ambiente di produzione:
##APP_ENV=prod
##con APP_ENV=env è utile per la fase di debug, in ambiente di produzione impostare prod
composer install
bin/console cache:clear
bin/console bicorebundle:install admin password admin@admin.it
bin/console App:CreateViews
bin/console App:LoadFixtures
```

### Esempio di VirtualHost
```
<VirtualHost *:80>
    ServerName          divo.comune.it
    ServerAlias         divo divo.*
    DocumentRoot        /var/www/html/divo/public
    DirectoryIndex      index.php
    <Directory "/var/www/html/divo/public">
       AllowOverride All
       Require all granted
       php_admin_value open_basedir "/var/www/html/divo:/tmp"
    </Directory>

    ErrorLog            logs/divo-error_log
    CustomLog           logs/divo-access_log common

    # optionally disable the RewriteEngine for the asset directories
    # which will allow apache to simply reply with a 404 when files are
    # not found instead of passing the request into the full symfony stack
    <Directory /var/www/html/divo/public/bundles>
        <IfModule mod_rewrite.c>
            RewriteEngine Off
        </IfModule>
    </Directory>
</VirtualHost>
```

### Configurazione:

- Accedere all'applicativo da browser (es. http://divo.comune.it/)
- Inserire username e password forniti in fase di installazione (es. admin - password)
- Accedere alla rotta Enti (es. http://divo.comune.it/Enti)
- Inserire il nome del Comune e il codice provincia e codice del Comune prendendo le informazioni da https://www.istat.it/storage/codici-unita-amministrative/Elenco-comuni-italiani.csv (solo la parte numerica)

Oppure come esempio per il Comune di Firenze:
```
bin/console App:CreateEnte "Comune di Firenze" 48 17
```
Il comando accetta 3 parametri
- Descrizione ente
- Codice provincia
- Codice comune


### Creazione utenti:
* divouser : Nome utente
* divopass : Password utente
* email@email.it : Indirizzo email dell'utente
* ws_user_per_accedere_regione_toscana : Nome utente per l'erogazione dei servizi di RT
* ws_password_per_accedere_regione_toscana : Password utente per l'erogazione dei servizi di RT

```
bin/console App:CreateUser "divouser" "divopass" "email@email.it" "ws_user_per_accedere_regione_toscana" "ws_password_per_accedere_regione_toscana"
```

### Configurazione data evento:
- Accedere all'applicativo da browser (es. http://divo.comune.it/)
- Inserire username e password forniti in fase di installazione (es. admin - password)
- Accedere alla rotta Enti (es. http://divo.comune.it/Utenti)
- Modificare la riga (con doppio click sull'utente) e impostare la data evento (per i test 31/05/2015, per produzione sarà la data dell'elezione)

### Upgrade
```
cd divo
git pull
composer install
```

### Upgrade ad una specifica release
```
cd divo
git pull
#Per posizionarsi sulla release 1.2.2
git checkout 1.2.2
composer install
```
