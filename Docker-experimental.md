# Run in docker

## Build image
```
docker build \
--build-arg BICORE_SCHEMA="divoschema" \
--build-arg DATABASE_URL="pgsql://divodbuser:divodbpassword@divodbhost:5432/divodbnname" \
-t divo .

```
## Start container

```
docker run \
-e "BICORE_SCHEMA=divoschema" \
-e "DATABASE_URL=pgsql://divodbuser:divodbpassword@divodbhost:5432/divodbnname" \
-e "RT_GET_EVENTI=/servizi-cooperativi/webapi/preelettorale/comunicazionieventi" \
-e "RT_GET_CANDIDATI=/servizi-cooperativi/webapi/preelettorale/scheda" \
-e "RT_PUT_VOTANTI=/servizi-cooperativi/webapi/elettorale/votanti/invio" \
-e "RT_PUT_SCRUTINI=/servizi-cooperativi/webapi/elettorale/scrutinio/invio" \
-e "RT_PUT_PREFERENZE=/servizi-cooperativi/webapi/elettorale/preferenze/invio" \
-e "RT_HOST=https://gestione.elezioni2020.regione.toscana.it" \
-e "RT_SERVICE_USER=testws_firenze" \
-e "RT_EVENT_CONFIG_LIST=acquisizioneAffluenza,acquisizioneListe,acquisizionePreferenze,gestioneAffluenzaMF,gestioneSchedeBianche,gestioneSchedeNulle,gestioneVotiNulliCoalizione,gestioneVotiContestatiCoalizione,gestioneVotiNulliListe,gestioneVotiContestatiListe,gestioneVotoDisgiunto,numeroMassimoPreferenze,gestioneVotiDiCui" \
-e "RT_AFF_STATES=READY,POST_AFFLUENCE_4" \
-d -p 8001:8001 --name=divo divo
```

## Eseguire comandi in docker

```
docker exec -ti divo bash
```

## Installare l'ambiente in docker
```
docker exec -ti divo bash
bin/console cache:clear
bin/console bicorebundle:install admin password admin@admin.it
bin/console App:CreateViews
bin/console App:LoadFixtures
bin/console App:CreateEnte "Comune di Firenze" 48 17
bin/console App:CreateUser "divouser" "divopass" "email@email.it" "ws_user_per_accedere_regione_toscana" "ws_password_per_accedere_regione_toscana"
```

### Configurazione data evento:
- Accedere all'applicativo da browser ( es. http://localhost:8001/ )
- Inserire username e password forniti in fase di installazione (es. admin - password)
- Accedere alla rotta Utenti ( es. http://localhost:8001/Utenti/ )
- Modificare la riga (con doppio click sull'utente) e impostare la data evento (per i test 31/05/2015, per produzione sarà la data dell'elezione)



