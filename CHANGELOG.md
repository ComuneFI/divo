# Changelog

## [1.3.7] - 2020-08-12
### Fix invio numeroTotaleVotiDiCuiSoloCandidato
- In alcuni casi i voti totali di cui solo candidato potevano non essere inclusi nel payload della comunicazione causando l'errore "I voti validi non corrispondono ai voti alle liste + voti solo candidato". Con il fix incluso nella versione 1.3.7, viene corretta questa anomalia

## [1.2.16] - 2020-08-06
### Fix numeroTotaleVotiDiCuiSoloCandidato
- In caso di aggiornamento della versione 1.2.15
```
bin/console doctrine:schema:update --force
```
Eseguire sql:
```
update divoschema.candidatisecondari set indipendente = 0
```

## [1.2.13] - 2020-08-04
### Fix
- In caso di aggiornamento della versione 1.2.10 modificare nel proprio file .env.local
```
RT_AFF_STATES=READY,POST_AFFLUENCE_1,POST_AFFLUENCE_2,POST_AFFLUENCE_3
```
in
```
RT_AFF_STATES=READY,POST_AFFLUENCE_4
```



