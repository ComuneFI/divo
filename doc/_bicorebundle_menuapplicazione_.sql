INSERT INTO divotest."__bicorebundle_menuapplicazione" (id,nome,percorso,padre,ordine,attivo,target,tag,notifiche,autorizzazionerichiesta,percorsonotifiche,discr) VALUES 
(1,'Amministrazione',NULL,NULL,20,true,NULL,'Amministrazione',NULL,true,NULL,'extended')
,(2,'Operatori','Operatori',1,10,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(3,'Ruoli','Ruoli',1,20,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(4,'Permessi','Permessi',1,30,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(5,'Gestione tabelle di sistema',NULL,1,40,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(6,'Colonne tabelle','Colonnetabelle',5,10,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(7,'Opzioni tabelle','Opzionitabelle',5,20,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(8,'Menu Applicazione','Menuapplicazione',1,50,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(9,'Utilità','fi_pannello_amministrazione_homepage',1,100,true,NULL,NULL,NULL,NULL,NULL,'extended')
,(10,'Dati applicativo sorgente',NULL,NULL,50,true,NULL,'divo',false,true,NULL,'extended')
;
INSERT INTO divotest."__bicorebundle_menuapplicazione" (id,nome,percorso,padre,ordine,attivo,target,tag,notifiche,autorizzazionerichiesta,percorsonotifiche,discr) VALUES 
(11,'Candidati Principali','Rxcandidati_container',10,10,true,NULL,'divo',false,true,NULL,'extended')
,(12,'Liste','Rxliste_container',10,20,true,NULL,'divo',false,true,NULL,'extended')
,(13,'Candidati Secondari','Rxcandidatisecondari_container',10,30,true,NULL,'divo',false,true,NULL,'extended')
,(14,'Stati',NULL,NULL,60,true,NULL,'divo',false,true,NULL,'extended')
,(15,'Anagrafica Stati','States_container',14,10,true,NULL,'divo',false,true,NULL,'extended')
,(16,'Grant Stati','Statesxgrant_container',14,20,true,NULL,'divo',false,true,NULL,'extended')
,(17,'Scarica Dati','downCSVxSource',10,40,true,NULL,'divo',false,true,NULL,'extended')
,(18,'Stato Eventi','timelineEventi',14,30,true,NULL,'divo',false,true,NULL,'extended')
,(19,'Stato Sezioni','timelineRxSezioni',14,40,true,NULL,'divo',false,true,NULL,'extended')
,(20,'Kettle','kettle',NULL,30,true,NULL,'rice2divo',false,true,NULL,'extended')
;
INSERT INTO divotest."__bicorebundle_menuapplicazione" (id,nome,percorso,padre,ordine,attivo,target,tag,notifiche,autorizzazionerichiesta,percorsonotifiche,discr) VALUES 
(21,'Report','Report',NULL,40,true,NULL,'rice2divo',false,true,NULL,'extended')
,(22,'Carica Dati','UploadDataByCsv',10,70,true,NULL,'divo',false,true,NULL,'extended')
;