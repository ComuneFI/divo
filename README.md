# Divo

> ⚠️ **WORK IN PROGRESS** ⚠️

## Per iniziare

**NOTA**: richiede PHP 7.2.

### Prerequisiti

Testato su Debian next stable 10 (buster).

```sh
sudo apt install php-sqlite3 php-xml php-gd php-curl php-mbstring php-zip composer git
```

### Installazione

```
git clone https://github.com/ComuneFi/divo.git
cd divo
#Copiare il file `.env` in `.env.local` per impostare il database da utilizzare (DATABASE_URL, BICORE_SCHEMA)
composer install
bin/console cache:clear
bin/console bicorebundle:install admin password admin@admin.it
bin/console App:CreateViews
```

###Configurazione:

- Accedere all'applicativo da browser (es. http://divo.comune.intranet/)
- Inserire username e password forniti in fase di installazione (es. admin - password)
- Accedere alla rotta Enti (es. http://divo.comune.intranet/Enti)
- Inserire il nome del Comune e il codice provincia e codice del Comune prendendo le informazioni da https://www.istat.it/storage/codici-unita-amministrative/Elenco-comuni-italiani.csv (solo la parte numerica)
