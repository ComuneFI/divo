# Divo

> ⚠️ **WORK IN PROGRESS** ⚠️

## Per iniziare

**NOTA**: richiede PHP 7.2 e PostgreSQL >= 10.13 

### Prerequisiti

Testato su CentOS 7 ma compatibile con altre distribuzioni Linux.

```sh
sudo apt install php-sqlite3 php-xml php-gd php-curl php-mbstring php-zip composer git
```

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
composer install
bin/console cache:clear
bin/console bicorebundle:install admin password admin@admin.it
bin/console App:CreateViews
```

### Configurazione:

- Accedere all'applicativo da browser (es. http://divo.comune.intranet/)
- Inserire username e password forniti in fase di installazione (es. admin - password)
- Accedere alla rotta Enti (es. http://divo.comune.intranet/Enti)
- Inserire il nome del Comune e il codice provincia e codice del Comune prendendo le informazioni da https://www.istat.it/storage/codici-unita-amministrative/Elenco-comuni-italiani.csv (solo la parte numerica)


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
