<?php

/**
 * This service is responsible to provide data reading from Divo DB and filtering them by defined
 * criteria if needed.
 * It uses ORMmanager service to perform operations.
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Eventi;
use App\Entity\Confxvotanti;
use App\Entity\Utenti;
use App\Entity\Enti;
use App\Entity\Candidatisecondari;
use App\Entity\Candidatiprincipali;
use App\Entity\Secondarioxlista;
use App\Entity\Listaxprincipale;
use App\Entity\Listapreferenze;
use App\Entity\Circoscrizioni;
use App\Entity\Circoxcandidato;
use App\Entity\Rxsezioni;
use App\Entity\Rxvotanti;
use App\Entity\Rxcandidati;
use App\Entity\Rxscrutiniliste;
use App\Entity\Actionlogs;

use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\StatesService;
use App\Service\ReportService;

/**
 * This is an utility service able to return Objects of data extracted by Divo.
 * It returns 3 types of items:
 * - 1) arrays of objects read from divo db
 * - 2) precooked objects needed to render data inside Twig pages or that help controller to do their jobs
 * - 3) arrays of results return from ReportService
 */
class RTDivoDataMiner {

    private $manager;
    private $ORMmanager;

    public function __construct( 
        EntityManagerInterface $manager, 
        ORMManager $ORMmanager, 
        RTServicesProvider $servicesProvider, 
        StatesService $serviceWf, 
        ReportService $reportService 
        ) 
    {
        $this->manager = $manager;
        $this->ORMmanager = $ORMmanager;
        $this->serviceProvider = $servicesProvider;
        $this->serviceWf = $serviceWf;
        $this->reportService = $reportService;
    }

    public function getReportService() 
    {
        return $this->reportService;
    }

    //******************************************************************
    //METHODS JOINING DETAILS FROM DIVO DB AND RETURNING ARRAY OF OBJECTS
    //******************************************************************

    /**
     * Return the list of Entexevento filtered by Ente associated to the used Service User.
     * So if for example user is enabled to only 2 events on 3 existent, it will have only 2 links that 
     * make an association event_1 - ente , event_2 - ente.
     */
    public function getEventsLinksByEnte($serviceUser): array 
    {
        $eventLinks_array = [];
        $eventLinks = $this->ORMmanager->getActiveEntityObjects($this->serviceProvider->getSeedEnteEvento());
        foreach(  $eventLinks as $eventLink ) {
            if ( $eventLink->getEnteId() ==  $serviceUser->getEnti()->getId() ) {
                array_push( $eventLinks_array, $eventLink );
            }
        }
        return $eventLinks_array;
    }

    /**
     * Return the list of Entexevento filtered by Ente associated to the used Service User.
     * So if for example user is enabled to only 2 events on 3 existent, it will have only 2 links that 
     * make an association event_1 - ente , event_2 - ente.
     */
    public function getEventsLinksByEvent($event): array 
    {
      
        $eventLinks = $this->ORMmanager->getActiveEntityObjects($this->serviceProvider->getSeedEnteEvento(),['evento_id'=>$event]);
        
        return $eventLinks;
    }

    /**
     * Return the list of Evento-Comunicazione enabled for that Ente (associated to the used Service User) as Array of Objects.
     */
    public function readComunicazioniEventiArray(): array 
    {
        //TODO: raname as getComunicazioniEventi()
        $comunicazioniEventi = [];
        
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();

        $eventLinks = $this->getEventsLinksByEnte( $serviceUser );

        foreach( $eventLinks as $eventLink ) {
            $event = $eventLink->getEventi();

            //the list of communications for that specific event
            $communications = $event->getConfxvotantis();
            
            foreach( $communications as $communication) {
                array_push($comunicazioniEventi, $communication);
            }
        }

        return $comunicazioniEventi;
    }

    /** 
     * Return an array of lists visible by the user
     */
    public function readListe(): array
    {
        //TODO: raname as getListe()
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();

        $divoLists = array();

        $entexevento_arr = $this->getEventsLinksByEnte( $serviceUser );

        foreach( $entexevento_arr as $entexevento ) {
            //get circoscrizione linked to the events enabled for this ente
            $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                'evento_id' => $entexevento->getEventi()->getId(),
            ] );
            foreach($circoscrizioni as $circDivo) {
                $candidateLinks = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                    'circ_id' => $circDivo->getId() 
                ]); 
                foreach($candidateLinks as $candidateLink) {
                    $divoLists = array_merge( $divoLists,  $this->getListe( $candidateLink->getCandidatiPrincipali() ) );
                }

            }
        }
        return $divoLists;
    }


    /** 
     * Return an array of lists visible by the user
     */
    public function readListeByEvent($event): array
    {
        //TODO: raname as getListe()
        $ORMmanager = $this->ORMmanager;
        $divoLists = array();


  
        //get circoscrizione linked to the events enabled for this ente
        $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
            'evento_id' => $event,
        ] );
        foreach($circoscrizioni as $circDivo) {
            $candidateLinks = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                'circ_id' => $circDivo->getId() 
            ]); 
            foreach($candidateLinks as $candidateLink) {
                $divoLists = array_merge( $divoLists,  $this->getListe( $candidateLink->getCandidatiPrincipali() ) );
            }

         }
        
        return $divoLists;
    }
    /**
     * Return an array of lists linked to the given main candidate
     */
    public function getListe( $mainCandidate ): array 
    {
        $results = array();
        $listsxprincipale = $this->ORMmanager->getEntityObjects( RTServicesProvider::ENT_LISTA_PRINCIPALE , [
            'candidato_principale_id' => $mainCandidate->getId() 
        ]); 
        foreach($listsxprincipale as $listxprincipale) {
            array_push($results, $listxprincipale->getListapreferenze());
        }
        return $results;
    }


     /** 
     * Return an array of candidates by event
     */
    public function getAllMainCandidatesByEvent($event): array
    {
        $ORMmanager = $this->ORMmanager;

        $divoCandidates = array();

        //get circoscrizione linked to the events enabled for this ente
        $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
            'evento_id' => $event,
        ] );
        foreach($circoscrizioni as $circDivo) {
            $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                'circ_id' => $circDivo->getId() 
            ]); 
            foreach($candidates as $candidate) {
                array_push( $divoCandidates , $candidate->getCandidatiPrincipali() );
            }
        }
       
        return $divoCandidates;
    }

    /** 
     * Return an array of candidates visible by the user/ente
     */
    public function getAllMainCandidates(): array
    {
        //TODO: refactor this method name as getCandidati()
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();

        $divoCandidates = array();

        $entexevento_arr = $this->getEventsLinksByEnte( $serviceUser );

        foreach( $entexevento_arr as $entexevento ) {
            //get circoscrizione linked to the events enabled for this ente
            $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                'evento_id' => $entexevento->getEventi()->getId(),
            ] );
            foreach($circoscrizioni as $circDivo) {
                $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                    'circ_id' => $circDivo->getId() 
                ]); 
                foreach($candidates as $candidate) {
                    array_push( $divoCandidates , $candidate->getCandidatiPrincipali() );
                }
            }
        }
        return $divoCandidates;
    }

    /**
     * Return the array of main candidates filter by the given circoscrizione.
     * This is an utility method that can be used in several moments.     * 
     */
    public function getMainCandidates( $circoscrizione ) 
    {
        //array that method will return
        $divoCandidates = array();

        //circoscrizione has already filterd for ente and for event (see field event_id into table circoscrizioni)
        $circoCandidateLinks = $this->ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
            'circ_id' => $circoscrizione->getId() 
        ]); 
        //fetch links and insert main candidates into final output array
        foreach($circoCandidateLinks as $link) {
            array_push( $divoCandidates , $link->getCandidatiPrincipali() );
        }
        return $divoCandidates;
    }

    /** 
     * Return an array of secondary candidates by the event
     */
    public function readCandidatiSecondariByEvent($event) 
    {
        $ORMmanager = $this->ORMmanager;

        $divoCandidates = array();

            //get circoscrizione linked to the events enabled for this ente
            $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                'evento_id' => $event,
            ] );
            foreach($circoscrizioni as $circDivo) {
                $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                    'circ_id' => $circDivo->getId() 
                ]); 
                foreach($candidates as $candidate) {
                    $listsxprincipale = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_LISTA_PRINCIPALE , [
                        'candidato_principale_id' => $candidate->getCandidatiPrincipali()->getId() 
                    ]); 

                    foreach( $listsxprincipale as $listxprinc ) { 
                        $preferences = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_SECONDARIO_LISTA , [
                            'lista_id' => $listxprinc->getListapreferenze()->getId() 
                        ]);
                        foreach( $preferences as $preference) {
                           array_push( $divoCandidates, $preference->getCandidatiSecondari() );
                        }
                       

                    }
                }

            }
        return $divoCandidates;
    }


    /** 
     * Return an array of secondary candidates visible by the user
     */
    public function readCandidatiSecondari() 
    {
        //TODO: raname as getCandidatiSecondari()
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();

        $divoCandidates = array();

        $entexevento_arr = $this->getEventsLinksByEnte( $serviceUser );

        foreach( $entexevento_arr as $entexevento ) {
            //get circoscrizione linked to the events enabled for this ente
            $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                'evento_id' => $entexevento->getEventi()->getId(),
            ] );
            foreach($circoscrizioni as $circDivo) {
                $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                    'circ_id' => $circDivo->getId() 
                ]); 
                foreach($candidates as $candidate) {
                    $listsxprincipale = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_LISTA_PRINCIPALE , [
                        'candidato_principale_id' => $candidate->getCandidatiPrincipali()->getId() 
                    ]); 

                    foreach( $listsxprincipale as $listxprinc ) { 
                        $preferences = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_SECONDARIO_LISTA , [
                            'lista_id' => $listxprinc->getListapreferenze()->getId() 
                        ]);
                        foreach( $preferences as $preference) {
                           array_push( $divoCandidates, $preference->getCandidatiSecondari() );
                        }
                       

                    }
                }

            }
        }
        return $divoCandidates;
    }

    /**
     * Read affluenza provided for the specific communication and for specific section
     * If not active affluences found, it looks for already sent affluences
     */
    public function getAffluenze($communication, $section): ?Rxvotanti
    {
        $result = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedRxAffluenze(), [
            'rxsezione_id' => $section->getId(),
            'confxvotanti_id' => $communication->getId(),
        ]);
        //TODO: evaluate if confirm this part
        if ( $result === null) {
            $result = $this->ORMmanager->popSentEntity( $this->serviceProvider->getSeedRxAffluenze(), [
                'rxsezione_id' => $section->getId(),
                'confxvotanti_id' => $communication->getId(),
            ]);
        }
        return $result;
    }

    /**
     * Read affluenze provided for the specific communication
     */
    public function getAffluenzeFromCommunication($communication): array
    {
        $results = $this->ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedRxAffluenze(), [ 
            'confxvotanti_id' => $communication->getId(),
        ] );
        return $results;
    }

    /**
     * Return the report of active and not sent Affluenze for the given communication.
     */
    public function getReportAffluenze($communication): array
    {
        $array = array();
        $array = $this->reportService->reportAffluenze($communication);
        return $array;
    }

    /**
     * Return the list of sections enabled to send.
     */
    public function getSectionsEnabledToSend($type): array
    {
        $array = array();
        $array = $this->reportService->getSectionEnabledToSend($type);
        return $array;
    }


    /**
     * Return the report of active and not sent Affluenze for the given communication.
     */
    public function getReportAffluenzeBitnew($communication): array
    {
        $array = array();
        $array = $this->reportService->reportAffluenzeBitnew($communication);
        return $array;
    }


    /**
     * Return the report of active and not sent Scrutini for the given section.
     */
    public function getReportScrutiniAll(): array
    {
        $array = array();
        $array = $this->reportService->reportAllScrutiniCandidatiPrincipali();
        return $array;
    }


     /**
     * Return the report of active Scrutini.
     */
    public function getReportScrutiniAllBitnew(): array
    {
        $array = array();
        $array = $this->reportService->reportAllScrutiniCandidatiPrincipaliBitnew();
        return $array;
    }

     /**
     * Return the report of active Affluence.
     */
    public function getReportAffluenceFinalBitnew($event_id): array
    {
        $array = array();
        $array = $this->reportService->reportAffluenceFinalBitnew($event_id);
        return $array;
    }


    /**
     * Return the report of active  Voti Non validi.
     */
    public function getReportScrutiniVotiNonValidiBitnew(): array
    {
        $array = array();
        $array = $this->reportService->reportAllScrutiniVotiNonValidiBitnew();
        return $array;
    }
  
    /**
     * Return the report of active and not sent Preferences.
     */
    public function getReportPreferenzeAllBitnew($enteId): array
    {
        $array = array();
        $array = $this->reportService->reportPreferencesBitnew($enteId);
        return $array;
    }


    /**
     * Extract array of events linked to the entexevento links
     */
    public function getEventsFromLinks(array $entexeventoLinks): array 
    {
        $results = array();
        foreach ($entexeventoLinks as $link) {
            $evento = $link->getEventi();
            if (!is_numeric(array_search($evento, $results))) {
                array_push($results, $evento);
            }
        }
        return $results;
    }

    /**
     * Extract array of links preference list- main candidate where given main candidates are linked to
     */
    public function getLinkListaFromCandidates(array $mainCandidates): array 
    {
        $results = array();
        foreach ($mainCandidates as $candidate) {
            $linkListCandidate = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedListaPrincipale(), [
                'candidato_principale_id' => $candidate->getId(), 
            ] );
            array_push($results, $linkListCandidate);
        }
        return $results;
    }

    /**
     * Extract array of links preference list- secondary candidate where given secondary candidates are linked to
     */
    public function getLinkListaFromSecondaryCandidates(array $secondaryCandidates): array 
    {
        $results = array();
        foreach ($secondaryCandidates as $candidate) {
            $linkListCandidate = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedSecondarioLista(), [
                'candidato_secondario_id' => $candidate->getId(), 
            ] );
            array_push($results, $linkListCandidate);
        }
        return $results;
    }
    
    /**
     * Extract array of links circoscrizioni-candidate where given main candidates are linked to
     */
    public function getCircoCandidateFromCandidates(array $mainCandidates): array 
    {
        $results = array();
        foreach ($mainCandidates as $candidate) {
            $circoxcandidate = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedCircoCandidato(), [
                'candidato_principale_id' => $candidate->getId(), 
            ] );
            array_push($results, $circoxcandidate);
        }
        return $results;
    }

    /**
     * Extract array of Circoscrizioni from links between circoscrizione and main candidates
     */
    public function getCircoscrizioniFromCircoCandidateLinks(array $circoxCandidates): array
    {
        $results = array();
        foreach ($circoxCandidates as $link) {
            $circo = $link->getCircoscrizioni();
            if (!is_numeric(array_search($circo, $results))) {
                array_push($results, $circo);
            }
        }
        return $results;
    }

    /**
     * Extract array of Circoscrizioni from array of main candidates
     */
    public function getCircoscrizioniFromCandidates(array $mainCandidates): array
    {
        $circoCandidates = $this->getCircoCandidateFromCandidates($mainCandidates);
        $circoscrizioni = $this->getCircoscrizioniFromCircoCandidateLinks($circoCandidates);
        return $circoscrizioni;
    }

    /**
     * Return an array with visible sections for the logged user/service user/ente
     */
    public function getSections(): array
    {
        $results = array();
        //which bounds ente x event are enabled for this service user ?
        $eventLinks = $this->getEventsLinksByEnte( $this->ORMmanager->getServiceUser() );

        foreach ($eventLinks as $eventLink) {
            $circoscrizioni = $this->ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedCircoscrizioni(), [ 
                'evento_id' => $eventLink->getEventi()->getId(),
            ] );
            //which sections are available for each enabled circoscrizione?
            foreach ($circoscrizioni as $circoscrizione) {
                $sezioni = $this->ORMmanager->getActiveEntityObjects(  $this->serviceProvider->getSeedRxSezioni() , [
                    'circo_id' => $circoscrizione->getId(),
                ]);
                $results = array_merge( $results, $sezioni);
            }
        }
        return $results;
    }

    /**
     * Return an array of rows with Section inside interval requested
     */
    public function getSectionsInterval($startId, $endId): array 
    {
        $array = array();
        $array = $this->reportService->reportSectionsInterval($startId, $endId);
        return $array;
    }

    /**
     * Return an array with visible sections for the given communication
     */
    public function getSectionsFromCommunication(Confxvotanti $communication): array
    {
        $circoscrizioni = $communication->getEventi()->getActiveCircoscrizionis();
        $outcome = array();
        foreach( $circoscrizioni as $circoscrizione) {
            //retrieve sections linked to each circoscrizione
            $sezioni = $this->ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedRxSezioni() , [
                'circo_id' => $circoscrizione->getId(),
            ]);
            $outcome = array_merge($outcome, $sezioni);
        }
        return $outcome;
    }

    /**
     * Read not valid votes for the given section item.
     * It expects to find only 1 record.
     * If exists read active record, otherwise read already sent entity
     */
    public function getVotiNonValidi($section) 
    {
        $result = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedRxVotiNonValidi() , [
            'rxsezione_id' => $section->getId(),
        ]);
        if (!isset($result)) {
            $result = $this->ORMmanager->popSentEntity( $this->serviceProvider->getSeedRxVotiNonValidi() , [
                'rxsezione_id' => $section->getId(),
            ]);
        }
        
        return $result;
    }

    /**
     * Return final votes for the given Section and given Candidate
     */
    public function getScrutiniCandidato(Rxsezioni $section, Candidatiprincipali $candidate ) 
    {
        $rxScrutinioCandidato = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedRxScrutini() , [
            'rxsezione_id' => $section->getId(),
            'candidato_principale_id' => $candidate->getId(),
        ]);  //TODO: evaluate if confirm this part
        if ( $rxScrutinioCandidato === null) {
            $rxScrutinioCandidato = $this->ORMmanager->popSentEntity( $this->serviceProvider->getSeedRxScrutini(), [
                'rxsezione_id' => $section->getId(),
                'candidato_principale_id' => $candidate->getId(),
            ]);
        }
        return $rxScrutinioCandidato;
    }

    /**
     * Return all final main candidates votes for the given Section
     */
    public function getScrutini(Rxsezioni $section) 
    {
        $rxScrutinioCandidato = $this->ORMmanager->getEntities( RTServicesProvider::ENT_RX_SCRUTINI_CANDIDATI, [
            'rxsezione_id' => $section->getId(),
        ]);
        return $rxScrutinioCandidato;
    }

    /**
     * Return all final preference secondary candidates votes for the given Section
     */
    public function getPreferenze(Rxsezioni $section) 
    {
        $rxPreferenze = $this->ORMmanager->getActiveEntityObjects(  $this->serviceProvider->getSeedRxPreferenze(), [
            'rxsezione_id' => $section->getId(),
        ]);
        return $rxPreferenze;
    }

    /**
     * Return an array of final main candidates votes limited to the ones visible to the logged user / ente.
     */
    public function getAllScrutini(): array
    {
        //get the list of main candidates limited to the logged service user / ente
        $mainCandidates = $this->getAllMainCandidates();
        //read from database main candidates final votes (for all, also main candidates outside of logged service user / ente visibility)
        $rxScrutiniCandidati = $this->ORMmanager->getEntities( RTServicesProvider::ENT_RX_SCRUTINI_CANDIDATI, []);
        //create an empty array that will contain all found objects
        $results = array();
        foreach ($rxScrutiniCandidati as $scrutinio) {
            if (array_search($scrutinio->getCandidatiPrincipali(), $mainCandidates)) {
                array_push($results, $scrutinio);
            }
        }
        return $results;
    }

    /**
     * Return all final main candidates votes limited to the ones visible to the logged user / ente.
     */
    public function getAllScrutiniListe() 
    {
        //get the list of main candidates limited to the logged service user / ente
        $mainCandidates = $this->getAllMainCandidates();
        //prepare an empty array to return
        $results = array();
        foreach ($mainCandidates as $candidate) {
            $listePreferenza = $this->getListe($candidate);
            foreach($listePreferenza as $lista) {
                $scrutini = $this->getScrutiniListaSezioniCandidato($lista);
                //append into results new data found
                $results = array_merge($results, $scrutini);
            }
        }
        return $results;
    }

    /**
     * Return final votes for main candidates for the given Section and given Preference list.
     * Or null if nothing has found.
     */
    public function getScrutiniListaCandidato(Rxsezioni $section, Listapreferenze $list): ?Rxscrutiniliste
    {
        $rxScrutiniListe = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedRxScrutiniListe() , [
            'rxsezione_id' => $section->getId(),
            'lista_preferenze_id' => $list->getId(),
        ]);
         //TODO: evaluate if confirm this part
         if ( $rxScrutiniListe === null) {
            $rxScrutiniListe = $this->ORMmanager->popSentEntity( $this->serviceProvider->getSeedRxScrutiniListe(), [
                'rxsezione_id' => $section->getId(),
                'lista_preferenze_id' => $list->getId(),
            ]);
        }
        return $rxScrutiniListe;
    }

    /**
     * Return an arrya of final votes for main candidates for the given Preference list
     */
    public function getScrutiniListaSezioniCandidato(Listapreferenze $list): array
    {
        $rxScrutiniListe = $this->ORMmanager->getEntities( RTServicesProvider::ENT_RX_SCRUTINI_LISTE, [
            'lista_preferenze_id' => $list->getId(),
        ]);
        return $rxScrutiniListe;
    }

    /**
     * Return the array of logs archived into DIVO
     */
    public function getLogs() {
        $logs = [];
        $ORMmanager = $this->ORMmanager;
        $logs = $ORMmanager->getEntities( Actionlogs::class , [] , [
            'timestamp' => 'DESC',
        ] );
        return $logs;
    }

    /**
     * Return the array of logs archived into DIVO
     */
    public function getLogsPaginator($currentPage = 1, $limit=100, $filter = null) {
        
            // Create our query
            $ORMmanager = $this->ORMmanager;
            $query = $ORMmanager->getManager()->createQueryBuilder()
                ->select('a')->from(Actionlogs::class, 'a');
                if($filter!=null) $query->where($filter);
                $query->orderBy('a.timestamp', 'DESC')
                ->getQuery();
        
            // No need to manually get get the result ($query->getResult())
        
            $paginator = $this->paginate($query, $currentPage, $limit);
        
            return $paginator;
        
    }

    


    /**
     * Paginator Helper
     *
     * Pass through a query object, current page & limit
     * the offset is calculated from the page and limit
     * returns an `Paginator` instance, which you can call the following on:
     *
     *     $paginator->getIterator()->count() # Total fetched (ie: `5` posts)
     *     $paginator->count() # Count of ALL posts (ie: `20` posts)
     *     $paginator->getIterator() # ArrayIterator
     *
     * @param Doctrine\ORM\Query $dql   DQL Query Object
     * @param integer            $page  Current page (defaults to 1)
     * @param integer            $limit The total number per page (defaults to 5)
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($dql, $page, $limit)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit
    
        return $paginator;
    }


    //******************************************************************
    //METHODS READING DETAILS FROM DIVO AND RETURNING PRE-COOKED ARRAYS FOR TWIGS OR RT COMMUNICATIONS
    //******************************************************************

    /**
     * Read votes for the given communication and return them with the following structure
     * [ { "sezione" : { "numero": number , "descrizione": string }, "comunicazioneVotanti": { "idComunicazioneElettorale": number, "descrizione": string, "numeroVotantiTotali": number } } , {} , ..., {} ]
     * In case that also configuration <i>gestioneAffluenzaMF</i> is enabled, then it will be inserted also votes for male and female into section "comunicazioneVotanti"
     */
    public function getPrecookedAffluenze( $communication ) {
        $ORMmanager = $this->ORMmanager;
        //read configurations of given communication in order to include/exclude number of votes by sex
        $configurazioni = json_decode( $communication->getConfigurazioni() );
        $includeAffluenzaMF = $configurazioni->gestioneAffluenzaMF;
        //get all circoscrizioni linked to the given communication
        $circoscrizioni = $communication->getEventi()->getActiveCircoscrizionis();
        $json_votes = array();
        foreach( $circoscrizioni as $circoscrizione) {
            //retrieve sections linked to each circoscrizione
            $sezioni = $ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedRxSezioni() , [
                'circo_id' => $circoscrizione->getId(),
            ]);

            foreach($sezioni as $sezione) {
                //for each section we can extract main details...

                $json_sezione = [
                    'numero' => $sezione->getNumero(),
                    'descrizione' => $sezione->getDescrizione(),
                ];
                $votes = $ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedRxAffluenze() , [
                    'rxsezione_id' => $sezione->getId(),
                    'confxvotanti_id' => $communication->getId(),
                ]);
                //dump($votes);
                //... and single votes
                foreach($votes as $vote) {
                    $json_vote = [
                        'idComunicazioneElettorale' => $communication->getComunicazioneCodice(),
                        'descrizione' => $communication->getComunicazioneDesc(), 
                        'numeroVotantiTotali' => $vote->getNumVotantiTotali(),
                    ];
                    if ($includeAffluenzaMF == 1) {
                        $json_vote['numeroVotantiMaschi'] = $vote->getNumVotantiMaschi();
                        $json_vote['numeroVotantiFemmine'] = $vote->getNumVotantiFemmine();
                        $json_vote['numeroVotantiTotali'] = $vote->getNumVotantiMaschi() + $vote->getNumVotantiFemmine();
                    }
                    $json_content = [
                        'sezione' => $json_sezione,
                        'comunicazioneVotanti' => $json_vote,
                    ];
                    array_push( $json_votes, $json_content );
                }
            }
        }
        return $json_votes;
    }



   /**
     * Read votes for the given communication and return them with the following structure
     * [ { "sezione" : { "numero": number , "descrizione": string }, "comunicazioneVotanti": { "idComunicazioneElettorale": number, "descrizione": string, "numeroVotantiTotali": number } } , {} , ..., {} ]
     * In case that also configuration <i>gestioneAffluenzaMF</i> is enabled, then it will be inserted also votes for male and female into section "comunicazioneVotanti"
     */
    public function getPrecookedAffluenzeOnlyChanged( $communication ) {
        $ORMmanager = $this->ORMmanager;
        //read configurations of given communication in order to include/exclude number of votes by sex
        $configurazioni = json_decode( $communication->getConfigurazioni() );
        $includeAffluenzaMF = $configurazioni->gestioneAffluenzaMF;
        //get all circoscrizioni linked to the given communication
        $json_votes = array();
        $rxRecords = array();
         
        $votes = $this->getReportAffluenzeBitnew($communication);
        
        foreach($votes as $vote) {
             if($vote['bitnew']==1){
                $json_sezione = [
                    'numero' => $vote['numero'],
                    'descrizione' => $vote['sezione'],
                 ];
                 $rxvotanti_item=new RxVotanti();
                 $rxvotanti_item->setId($vote['id']);
                
                $json_vote = [
                    'idComunicazioneElettorale' => $communication->getComunicazioneCodice(),
                    'descrizione' => $communication->getComunicazioneDesc(), 
                    'numeroVotantiTotali' => $vote['num_votanti_totali'],
                ];
                if ($includeAffluenzaMF == 1) {
                    $json_vote['numeroVotantiMaschi'] = $vote['num_votanti_maschi'];
                    $json_vote['numeroVotantiFemmine'] =$vote['num_votanti_femmine'];
                    $json_vote['numeroVotantiTotali'] = $vote['num_votanti_maschi'] + $vote['num_votanti_femmine'];
                }
                $json_content = [
                    'sezione' => $json_sezione,
                    'comunicazioneVotanti' => $json_vote,
                    ];
                 array_push( $rxRecords, $rxvotanti_item );
                 array_push( $json_votes, $json_content );
                       
             }  
        }
        $return_array['json_votes']=$json_votes;
        $return_array['rxRecords']=$rxRecords;
        return $return_array;
    }

    /**
     * It reaturns a payload of 'scrutionioSezione' compliant with RT expected data, filtered by Section results
     */
    public function getPrecookedScrutini( $section ) {
        $results = [];
        //TODO: implement this section
        return $results;
    }



    /**
     * It reaturns a payload of 'scrutionioSezione' compliant with RT expected data, filtered by Section results
     */
    public function getConfStatus() {
        $results = []; 
    
        $ente_id = $this->ORMmanager->getServiceUser()->getEnti()->getId();       
        $results['ncandprinc'] = $this->reportService->reportNotConfCandPrinc($ente_id);
        $results['ncandsec'] = $this->reportService->reportNotConfCandSec($ente_id);
        $results['nliste'] = $this->reportService->reportNotConfLists($ente_id);
        return $results;
    }

    /**
     * Retrieve the universe of candidates and lists, filtered by events visible by ente
     */
    public function readCandidatiListe() {
        //TODO: change method name and bring it to be a pre-cooked
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();
        
        //$entexevento_arr = $ORMmanager->getEntityObjects( 'App\Entity\Entexevento' );
        $entexevento_arr = $this->getEventsLinksByEnte( $serviceUser );
        
        $driver_lists = [];

        $visible_events = [];
            foreach( $entexevento_arr as $entexevento ) {
                        //get circoscrizione linked to the events enabled for this ente
                        $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                            'evento_id' => $entexevento->getEventi()->getId(),
                        ] );
                        
                        //prepare a vector to handle the list of candidates
                        $myvec = [];
                        foreach($circoscrizioni as $circDivo) {
                            $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                                'circ_id' => $circDivo->getId() 
                            ]); 
                            $candidates_array = [];
                            foreach($candidates as $candidate ) {


                                $listsxprincipale = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_LISTA_PRINCIPALE , [
                                    'candidato_principale_id' => $candidate->getCandidatiPrincipali()->getId() 
                                ]); 

                                $lists_array = [];
                                foreach( $listsxprincipale as $listxprinc ) {
                                    

                                    $preferences = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_SECONDARIO_LISTA , [
                                        'lista_id' => $listxprinc->getListapreferenze()->getId() 
                                    ]); 
                                    $pref_array = [];
                                    foreach( $preferences as $preference ) {
                                        $pref_array[ $preference->getPosizione() ] =  $preference->getCandidatiSecondari();
                                    }

                                    $preference_obj = [
                                        'lista' => $listxprinc->getListapreferenze(),
                                        'candidatisecondari' => $pref_array,
                                    ];

                                    array_push( $driver_lists, $listxprinc->getListapreferenze()->getId() );

                                    $lists_array[ $listxprinc->getPosizione() ] = $preference_obj;


                                }

                                $candidate_list = [
                                    'candidato' =>  $candidate->getCandidatiPrincipali(),
                                    'liste' => $lists_array,
                                ];
                                $candidates_array[ $candidate->getPosizione() ] = $candidate_list;
  
                            }
                            
                            $candidatixcirco = [
                                'circoscrizione' => $circDivo,
                                'candidati' => $candidates_array,
                            ];

                            array_push( $myvec , $candidatixcirco );

                        }
                        $myEvent = $entexevento->getEventi();
                        $myEvent->storeStatoWfDesc($this->serviceWf);
                        $vec = [
                            'evento' => $myEvent,
                            'circoscrizioni' => $myvec,
                            'driver_lists' => $driver_lists,
                        ];

                  
                        array_push( $visible_events, $vec );                           
            };
        return $visible_events;
    }


     /**
     * Retrieve the universe of candidates and lists, filtered by events visible by ente
     */
    public function readCandidatiListeByEvent($event) {
        //TODO: change method name and bring it to be a pre-cooked
        $ORMmanager = $this->ORMmanager;
     
        
        //$entexevento_arr = $ORMmanager->getEntityObjects( 'App\Entity\Entexevento' );
        $entexevento_arr = $this->getEventsLinksByEvent( $event );
        
        $driver_lists = [];

        $visible_events = [];
            foreach( $entexevento_arr as $entexevento ) {
                        //get circoscrizione linked to the events enabled for this ente
                        $circoscrizioni = $ORMmanager->getEntityObjects(RTServicesProvider::ENT_CIRCOSCRIZIONI, [
                            'evento_id' => $entexevento->getEventi()->getId(),
                        ] );
                        
                        //prepare a vector to handle the list of candidates
                        $myvec = [];
                        foreach($circoscrizioni as $circDivo) {
                            $candidates = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCO_CANDIDATO , [
                                'circ_id' => $circDivo->getId() 
                            ]); 
                            $candidates_array = [];
                            foreach($candidates as $candidate ) {


                                $listsxprincipale = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_LISTA_PRINCIPALE , [
                                    'candidato_principale_id' => $candidate->getCandidatiPrincipali()->getId() 
                                ]); 

                                $lists_array = [];
                                foreach( $listsxprincipale as $listxprinc ) {
                                    

                                    $preferences = $ORMmanager->getEntityObjects( RTServicesProvider::ENT_SECONDARIO_LISTA , [
                                        'lista_id' => $listxprinc->getListapreferenze()->getId() 
                                    ]); 
                                    $pref_array = [];
                                    foreach( $preferences as $preference ) {
                                        $pref_array[ $preference->getPosizione() ] =  $preference->getCandidatiSecondari();
                                    }

                                    $preference_obj = [
                                        'lista' => $listxprinc->getListapreferenze(),
                                        'candidatisecondari' => $pref_array,
                                    ];

                                    array_push( $driver_lists, $listxprinc->getListapreferenze()->getId() );

                                    $lists_array[ $listxprinc->getPosizione() ] = $preference_obj;


                                }

                                $candidate_list = [
                                    'candidato' =>  $candidate->getCandidatiPrincipali(),
                                    'liste' => $lists_array,
                                ];
                                $candidates_array[ $candidate->getPosizione() ] = $candidate_list;
  
                            }
                            
                            $candidatixcirco = [
                                'circoscrizione' => $circDivo,
                                'candidati' => $candidates_array,
                            ];

                            array_push( $myvec , $candidatixcirco );

                        }
                        $myEvent = $entexevento->getEventi();
                        $myEvent->storeStatoWfDesc($this->serviceWf);
                        $vec = [
                            'evento' => $myEvent,
                            'circoscrizioni' => $myvec,
                            'driver_lists' => $driver_lists,
                        ];

                  
                        array_push( $visible_events, $vec );                           
            };
        return $visible_events;
    }



   /**
     * Return the list of Sezioni enabled for that Ente (associated to the used Service User).
     * It returns a Precooked array of items composed by an event and a section.
     */
    public function getPrecookedSezioni(): array 
    {
        $array_sezioni = [];
        
        $serviceUser = $this->ORMmanager->getServiceUser();
        
        //which bounds ente x event are enabled for this service user ?
        $eventLinks = $this->getEventsLinksByEnte( $serviceUser );

        foreach ($eventLinks as $eventLink) {
            $event = $eventLink->getEventi();

            $circoscrizioni = $this->ORMmanager->getEntityObjects( RTServicesProvider::ENT_CIRCOSCRIZIONI, [ 
                'evento_id' => $event->getId(),
            ] );
            
            //which sections are available for each enabled circoscrizione?
            foreach ($circoscrizioni as $circoscrizione) {
                $sezioni = $this->ORMmanager->getEntities( Rxsezioni::class , [
                    'circo_id' => $circoscrizione->getId(),
                ], 
                [
                    'numero' => 'ASC',
                ]);
                foreach ($sezioni as $sezione) {
                    $item = [ 
                        'sezione' => $sezione,  
                        'evento' => $event,
                    ];
                    array_push( $array_sezioni, $item);
                }
            }
        }
        return $array_sezioni;
    }

    /**
     * Return the list of Evento - Comunicazione enabled for that Ente (associated to the used Service User) 
     */
    public function readComunicazioniEventi() {
        //TODO: change method name and bring it to be a pre-cooked
        $comunicazioniEventi = [];
        
        $ORMmanager = $this->ORMmanager;
        $serviceUser = $ORMmanager->getServiceUser();
        
        //$entexevento_arr = $ORMmanager->getEntityObjects( 'App\Entity\Entexevento' );
        $eventLinks = $this->getEventsLinksByEnte( $serviceUser );

        foreach( $eventLinks as $eventLink ) {
            $event = $eventLink->getEventi();

            //the list of communications for that specific event
            $communications = $event->getConfxvotantis();
            
            foreach( $communications as $communication) {
                $map_communication = [
                    'event' => $event->getEvento(),
                    'event_desc' => $event->getDescrizioneEvento(),
                    'com_id' => $communication->getId(),
                    'com_code' => $communication->getComunicazioneCodice(),                    
                    'com_desc' => $communication->getComunicazioneDesc(),
                ];
                array_push($comunicazioniEventi, $map_communication);
            }
        }

        return $comunicazioniEventi;
    }



    /**
     * Return the list of Sections for Event 
     */

    public function getSectionsByEvent($event){
    
        $obj=[];
        $obj['array']=[];
        $obj['listOfId']=[];
        $array=[];
        $listOfId=[];
        $circo_id=-1;
        $listRxCircoscrizioniServ = $this->serviceProvider->getSeedCircoscrizioni();
   
        $circoscrizioni = $this->ORMmanager->getActiveEntityObjects( $listRxCircoscrizioniServ, [ 
            'evento_id' => $event,
        ] );
        //MULTI-CIRCOSCRIZIONE?
        if(isset($circoscrizioni[0])){
            $sezioni = $this->ORMmanager->getActiveEntityObjects( $this->serviceProvider->getSeedRxSezioni() , [
                'circo_id' => $circoscrizioni[0]->getId(),
            ]);
            $circo_id= $circoscrizioni[0]->getId();
            foreach($sezioni as $sezione){
                $array[$sezione->getNumero()]=$sezione;
                array_push($listOfId,$sezione->getId());
            }
        }
       
     
      
        $obj['listOfId']=$listOfId;
        $obj['array']=$array;
        $obj['circo_id']=$circo_id;
        return $obj;
    }
 
    /**
     * Return the mapping array of Lists for Event 
     */
    public function getMappingListsByEvent($event){
      
        $array=array();
        $readList = $this->readListeByEvent($event);
        foreach($readList as $item){
            $array[$item->getIdSource()]=$item;
        }
   
        return $array;
    }

}