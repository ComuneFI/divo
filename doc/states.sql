INSERT INTO states (id,descr,code,discr,nextstate,entity_ref) VALUES 
(1,'Comunicazioni elettorali recuperate','GET_COMMUNICATIONS','extended','GET_LISTSCANDIDATES','Eventi')
,(2,'Liste e candidati recuperati','GET_LISTSCANDIDATES','extended','COF_CANDIDATES','Eventi')
,(7,'Scrutini inviati/modificati','POST_POLL','extended','POST_PREFERENCES','Eventi')
,(8,'Preferenze inviate/modificate','POST_PREFERENCES','extended','CLOSE','Eventi')
,(6,'Affluenze inviate/modificate','POST_AFFLUENCE','extended','POST_POLL','Eventi')
,(21,'Scrutini inviati/modificati','POST_POLL','extended','POST_PREFERENCES','RxSezioni')
,(9,'Fine','CLOSE','extended','CLOSE','Eventi')
,(10,'Importato','START','extended','GET_COMMUNICATIONS','Eventi')
,(19,'Post Affluenze 2','POST_AFFLUENCE_2','extended','POST_AFFLUENCE_3','RxSezioni')
,(20,'Post Affluenze 3','POST_AFFLUENCE_3','extended','POST_POLL','RxSezioni')
;
INSERT INTO states (id,descr,code,discr,nextstate,entity_ref) VALUES 
(23,'Fine','END','extended','END','RxSezioni')
,(22,'Invia preferenze','POST_PREFERENCES','extended','END','RxSezioni')
,(3,'Config. Candidati Principali','COF_CANDIDATES','extended','COF_LISTS','Eventi')
,(4,'Config. Liste di Preferenza','COF_LISTS','extended','COF_PREFERENCESCANDIDATES','Eventi')
,(5,'Config. Candidati di Preferenza','COF_PREFERENCESCANDIDATES','extended','POST_AFFLUENCE','Eventi')
,(24,'Affluenze Test','POST_AFFLUENCE_Z','extended','POST_AFFLUENCE_2','RxSezioni')
,(17,'Sezione pronta','READY','extended','POST_AFFLUENCE_1','RxSezioni')
,(18,'Post Affluenze 1','POST_AFFLUENCE_1','extended','POST_AFFLUENCE_2','RxSezioni')
;