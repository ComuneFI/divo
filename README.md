# Divo

> ⚠️ **WORK IN PROGRESS** ⚠️

## Per iniziare

**NOTA**: richiede PHP 7.2.

### Prerequisiti

Testato su Debian next stable 10 (buster).

```sh
sudo apt install php-sqlite3 php-xml php-gd php-curl php-mbstring php-zip composer git
```

### Configurazione

Se necessario, copiare il file `.env` in `.env.local` per impostare il database da utilizzare.

### Installazione

```
git clone https://github.com/ComuneFi/divo.git
cd divo
composer install
bin/console cache:clear
bin/console bicorebundle:install admin password admin@admin.it
bin/console App:CreateViews
```