<?php

namespace App\Command;

use App\Entity\Enti;
use App\Entity\States;
use App\Entity\Statesxgrant;
use App\Entity\Utenti;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\ArrayInput;

class LoadFixturesCommand extends Command {

    private $errors;
    private $output;
    private $logger;
    private $logfile;

    protected function configure() {
        $this
                ->setName('App:LoadFixtures')
                ->setDescription('Creazione i record di base');
    }

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel, LoggerInterface $logger, \Swift_Mailer $mailer) {
        $this->em = $em;
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->mailer = $mailer;
        $this->errors = false;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $this->output = $output;

        $inizio = microtime(true);

        //$em = $this->em->getManager();
        $connection = $this->em->getConnection();
        $fixtures = $this->getFixtures();

        foreach ($fixtures as $fixturesname => $fixtures) {
            $output->writeln("<info>Inserimento $fixturesname ...</info>");
            $statement = $connection->prepare($fixtures);
            $returnvalue = $statement->execute();
            $status = ($returnvalue ? "OK" : "KO");
            $statustype = ($returnvalue ? "info" : "error");
            $output->writeln("<$statustype>Esito $fixturesname : $status</$statustype>");
        }


        $fine = microtime(true);
    }

    private function getFixtures() {
        $schema = getenv("BICORE_SCHEMA");
        $fixtures = array();


        $fixtures["states"] = "INSERT INTO $schema.states (id,descr,code,discr,nextstate,entity_ref) VALUES 
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
,(4,'Config. Liste di Preferenza','COF_LISTS','extended','COF_PREFERENCESCANDIDATES','Eventi')
,(3,'Config. Candidati Principali','COF_CANDIDATES','extended','COF_LISTS','Eventi')
,(5,'Config. Candidati di Preferenza','COF_PREFERENCESCANDIDATES','extended','POST_AFFLUENCE','Eventi')
,(1,'Comunicazioni elettorali recuperate','GET_COMMUNICATIONS','extended','GET_LISTSCANDIDATES','Eventi')
,(6,'Affluenze inviate/modificate','POST_AFFLUENCE','extended','POST_POLL','Eventi')
;";

        $fixtures["statesxgrant"] = "INSERT INTO $schema.statesxgrant (id,\"current\",\"next\",enabled,crackable,discr,entity_ref) VALUES 
(103,'START','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(102,'START','COF_LISTS',false,false,'extended','Eventi')
,(99,'START','GET_COMMUNICATIONS',true,false,'extended','Eventi')
,(101,'START','COF_CANDIDATES',false,false,'extended','Eventi')
,(100,'START','GET_LISTSCANDIDATES',false,false,'extended','Eventi')
,(105,'START','POST_POLL',false,false,'extended','Eventi')
,(106,'START','POST_PREFERENCES',false,false,'extended','Eventi')
,(107,'START','CLOSE',false,false,'extended','Eventi')
,(104,'START','POST_AFFLUENCE',false,false,'extended','Eventi')
,(113,'READY','POST_POLL',false,false,'extended','RxSezioni')
,(108,'READY','POST_AFFLUENCE_4',true,false,'extended','RxSezioni')
,(110,'READY','END',false,false,'extended','RxSezioni')
,(112,'READY','POST_PREFERENCES',false,false,'extended','RxSezioni')
,(143,'POST_PREFERENCES','END',true,false,'extended','RxSezioni')
,(142,'POST_PREFERENCES','POST_POLL',false,false,'extended','RxSezioni')
,(81,'POST_PREFERENCES','GET_COMMUNICATIONS',false,false,'extended','Eventi')
,(141,'POST_PREFERENCES','POST_AFFLUENCE_4',false,false,'extended','RxSezioni')
,(82,'POST_PREFERENCES','GET_LISTSCANDIDATES',false,false,'extended','Eventi')
,(89,'POST_PREFERENCES','CLOSE',false,false,'extended','Eventi')
,(86,'POST_PREFERENCES','POST_AFFLUENCE',false,false,'extended','Eventi')
,(84,'POST_PREFERENCES','COF_LISTS',false,false,'extended','Eventi')
,(85,'POST_PREFERENCES','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(83,'POST_PREFERENCES','COF_CANDIDATES',false,false,'extended','Eventi')
,(87,'POST_PREFERENCES','POST_POLL',true,false,'extended','Eventi')
,(138,'POST_PREFERENCES','READY',false,false,'extended','RxSezioni')
,(76,'POST_POLL','POST_AFFLUENCE',false,false,'extended','Eventi')
,(71,'POST_POLL','GET_COMMUNICATIONS',false,false,'extended','Eventi')
,(72,'POST_POLL','GET_LISTSCANDIDATES',false,false,'extended','Eventi')
,(73,'POST_POLL','COF_CANDIDATES',false,false,'extended','Eventi')
,(74,'POST_POLL','COF_LISTS',false,false,'extended','Eventi')
,(75,'POST_POLL','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(136,'POST_POLL','POST_PREFERENCES',true,false,'extended','RxSezioni')
,(137,'POST_POLL','END',false,false,'extended','RxSezioni')
,(132,'POST_POLL','READY',false,false,'extended','RxSezioni')
,(78,'POST_POLL','POST_PREFERENCES',true,false,'extended','Eventi')
,(79,'POST_POLL','CLOSE',false,false,'extended','Eventi')
,(135,'POST_POLL','POST_AFFLUENCE_4',false,false,'extended','RxSezioni')
,(130,'POST_AFFLUENCE_4','POST_PREFERENCES',false,false,'extended','RxSezioni')
,(131,'POST_AFFLUENCE_4','POST_POLL',true,false,'extended','RxSezioni')
,(129,'POST_AFFLUENCE_4','END',false,false,'extended','RxSezioni')
,(126,'POST_AFFLUENCE_4','READY',false,false,'extended','RxSezioni')
,(62,'POST_AFFLUENCE','GET_LISTSCANDIDATES',false,false,'extended','Eventi')
,(63,'POST_AFFLUENCE','COF_CANDIDATES',false,false,'extended','Eventi')
,(64,'POST_AFFLUENCE','COF_LISTS',false,false,'extended','Eventi')
,(65,'POST_AFFLUENCE','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(68,'POST_AFFLUENCE','POST_PREFERENCES',false,false,'extended','Eventi')
,(67,'POST_AFFLUENCE','POST_POLL',true,false,'extended','Eventi')
,(69,'POST_AFFLUENCE','CLOSE',false,false,'extended','Eventi')
,(61,'POST_AFFLUENCE','GET_COMMUNICATIONS',false,false,'extended','Eventi')
,(23,'GET_LISTSCANDIDATES','COF_CANDIDATES',true,false,'extended','Eventi')
,(24,'GET_LISTSCANDIDATES','COF_LISTS',false,false,'extended','Eventi')
,(25,'GET_LISTSCANDIDATES','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(26,'GET_LISTSCANDIDATES','POST_AFFLUENCE',false,false,'extended','Eventi')
,(27,'GET_LISTSCANDIDATES','POST_POLL',false,false,'extended','Eventi')
,(28,'GET_LISTSCANDIDATES','POST_PREFERENCES',false,false,'extended','Eventi')
,(29,'GET_LISTSCANDIDATES','CLOSE',false,false,'extended','Eventi')
,(21,'GET_LISTSCANDIDATES','GET_COMMUNICATIONS',false,true,'extended','Eventi')
,(12,'GET_COMMUNICATIONS','GET_LISTSCANDIDATES',true,false,'extended','Eventi')
,(13,'GET_COMMUNICATIONS','COF_CANDIDATES',false,false,'extended','Eventi')
,(14,'GET_COMMUNICATIONS','COF_LISTS',false,false,'extended','Eventi')
,(15,'GET_COMMUNICATIONS','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(16,'GET_COMMUNICATIONS','POST_AFFLUENCE',false,false,'extended','Eventi')
,(17,'GET_COMMUNICATIONS','POST_POLL',false,false,'extended','Eventi')
,(18,'GET_COMMUNICATIONS','POST_PREFERENCES',false,false,'extended','Eventi')
,(19,'GET_COMMUNICATIONS','CLOSE',false,false,'extended','Eventi')
,(147,'END','POST_AFFLUENCE_4',false,false,'extended','RxSezioni')
,(149,'END','POST_PREFERENCES',false,false,'extended','RxSezioni')
,(144,'END','READY',false,false,'extended','RxSezioni')
,(148,'END','POST_POLL',false,false,'extended','RxSezioni')
,(52,'COF_PREFERENCESCANDIDATES','GET_LISTSCANDIDATES',false,true,'extended','Eventi')
,(56,'COF_PREFERENCESCANDIDATES','POST_AFFLUENCE',true,false,'extended','Eventi')
,(59,'COF_PREFERENCESCANDIDATES','CLOSE',false,false,'extended','Eventi')
,(53,'COF_PREFERENCESCANDIDATES','COF_CANDIDATES',false,true,'extended','Eventi')
,(54,'COF_PREFERENCESCANDIDATES','COF_LISTS',false,true,'extended','Eventi')
,(57,'COF_PREFERENCESCANDIDATES','POST_POLL',false,false,'extended','Eventi')
,(58,'COF_PREFERENCESCANDIDATES','POST_PREFERENCES',false,false,'extended','Eventi')
,(51,'COF_PREFERENCESCANDIDATES','GET_COMMUNICATIONS',false,true,'extended','Eventi')
,(45,'COF_LISTS','COF_PREFERENCESCANDIDATES',true,false,'extended','Eventi')
,(49,'COF_LISTS','CLOSE',false,false,'extended','Eventi')
,(48,'COF_LISTS','POST_PREFERENCES',false,false,'extended','Eventi')
,(47,'COF_LISTS','POST_POLL',false,false,'extended','Eventi')
,(46,'COF_LISTS','POST_AFFLUENCE',false,false,'extended','Eventi')
,(42,'COF_LISTS','GET_LISTSCANDIDATES',false,true,'extended','Eventi')
,(41,'COF_LISTS','GET_COMMUNICATIONS',false,true,'extended','Eventi')
,(43,'COF_LISTS','COF_CANDIDATES',false,true,'extended','Eventi')
,(38,'COF_CANDIDATES','POST_PREFERENCES',false,false,'extended','Eventi')
,(36,'COF_CANDIDATES','POST_AFFLUENCE',false,false,'extended','Eventi')
,(35,'COF_CANDIDATES','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(34,'COF_CANDIDATES','COF_LISTS',true,false,'extended','Eventi')
,(32,'COF_CANDIDATES','GET_LISTSCANDIDATES',false,true,'extended','Eventi')
,(31,'COF_CANDIDATES','GET_COMMUNICATIONS',false,true,'extended','Eventi')
,(39,'COF_CANDIDATES','CLOSE',false,false,'extended','Eventi')
,(37,'COF_CANDIDATES','POST_POLL',false,false,'extended','Eventi')
,(94,'CLOSE','COF_LISTS',false,false,'extended','Eventi')
,(93,'CLOSE','COF_CANDIDATES',false,false,'extended','Eventi')
,(98,'CLOSE','POST_PREFERENCES',false,false,'extended','Eventi')
,(97,'CLOSE','POST_POLL',false,false,'extended','Eventi')
,(96,'CLOSE','POST_AFFLUENCE',false,false,'extended','Eventi')
,(95,'CLOSE','COF_PREFERENCESCANDIDATES',false,false,'extended','Eventi')
,(92,'CLOSE','GET_LISTSCANDIDATES',false,false,'extended','Eventi')
,(91,'CLOSE','GET_COMMUNICATIONS',false,false,'extended','Eventi')
;";

        
        $fixtures["menuapplicazione"] = "INSERT INTO $schema.\"__bicorebundle_menuapplicazione\" (id,nome,percorso,padre,ordine,attivo,target,tag,notifiche,autorizzazionerichiesta,percorsonotifiche,discr) VALUES 
(10,'Dati applicativo sorgente',NULL,NULL,50,true,NULL,'divo',false,true,NULL,'extended')
,(11,'Candidati Principali','Rxcandidati_container',10,10,true,NULL,'divo',false,true,NULL,'extended')
,(12,'Liste','Rxliste_container',10,20,true,NULL,'divo',false,true,NULL,'extended')
,(13,'Candidati Secondari','Rxcandidatisecondari_container',10,30,true,NULL,'divo',false,true,NULL,'extended')
,(14,'Stati',NULL,NULL,60,true,NULL,'divo',false,true,NULL,'extended')
,(15,'Anagrafica Stati','States_container',14,10,true,NULL,'divo',false,true,NULL,'extended')
,(16,'Grant Stati','Statesxgrant_container',14,20,true,NULL,'divo',false,true,NULL,'extended')
,(17,'Scarica Dati','downCSVxSource',10,40,true,NULL,'divo',false,true,NULL,'extended')
,(18,'Stato Eventi','timelineEventi',14,30,true,NULL,'divo',false,true,NULL,'extended')
,(19,'Stato Sezioni','timelineRxSezioni',14,40,true,NULL,'divo',false,true,NULL,'extended')
,(22,'Carica Dati','UploadDataByCsv',10,70,true,NULL,'divo',false,true,NULL,'extended');";

        return $fixtures;
    }

}
