INSERT INTO divodev.states (id,descr,code,discr,nextstate,entity_ref) VALUES 
(17,'Sezione pronta','READY','extended','POST_AFFLUENCE_4','RxSezioni')
,(7,'Scrutini inviati/modificati','POST_POLL','extended','POST_PREFERENCES','Eventi')
,(21,'Scrutini inviati/modificati','POST_POLL','extended','POST_PREFERENCES','RxSezioni')
,(8,'Preferenze inviate/modificate','POST_PREFERENCES','extended','CLOSE','Eventi')
,(24,'Post Affluenze Finali','POST_AFFLUENCE_4','extended','POST_POLL','RxSezioni')
,(2,'Liste e candidati recuperati','GET_LISTSCANDIDATES','extended','COF_CANDIDATES','Eventi')
,(22,'Invia preferenze','POST_PREFERENCES','extended','END','RxSezioni')
,(10,'Importato','START','extended','GET_COMMUNICATIONS','Eventi')
,(9,'Fine','CLOSE','extended','CLOSE','Eventi')
,(23,'Fine','END','extended','END','RxSezioni')
;
INSERT INTO divodev.states (id,descr,code,discr,nextstate,entity_ref) VALUES 
(4,'Config. Liste di Preferenza','COF_LISTS','extended','COF_PREFERENCESCANDIDATES','Eventi')
,(3,'Config. Candidati Principali','COF_CANDIDATES','extended','COF_LISTS','Eventi')
,(5,'Config. Candidati di Preferenza','COF_PREFERENCESCANDIDATES','extended','POST_AFFLUENCE','Eventi')
,(1,'Comunicazioni elettorali recuperate','GET_COMMUNICATIONS','extended','GET_LISTSCANDIDATES','Eventi')
,(6,'Affluenze inviate/modificate','POST_AFFLUENCE','extended','POST_POLL','Eventi')
;