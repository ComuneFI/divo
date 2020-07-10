<?php

/**
 * This file contains definitio of a service providing basic functionalities to interact with RT.
 * It provides methods to get URLs where to invoke exposed web services.
 * It provides payloads to be used in order to invoke these web services.
 */

namespace App\Service;

use App\Entity\Eventi;

use App\Service\ORMManager;
use App\Service\DivoEntitySent;
use App\Service\DivoEntityStatic;
use App\Service\RTSentableInterface;


/**
 * It collects all end-points of application
 * It's a Singleton class.
 */
class RTServicesProvider {
    // Variables
    private $pre_comunicazionieventi;
    private $pre_liste_candidati;
    private $ele_votanti;
    private $ele_scrutini;
    private $ele_preferenze;
    private $host;

    // Const variables
    //TODO these const variables should be private const ENT_* one time ORMmanager deprecated methods are no more used
    const ENT_EVENTI = 'App\Entity\Eventi'; 
    const ENT_COMUNICAZIONI = 'App\Entity\Confxvotanti';
    const ENT_ENTEXEVENTO = 'App\Entity\Entexevento';
    const ENT_CAND_PRINCIPALI = 'App\Entity\Candidatiprincipali';
    const ENT_CAND_SECONDARI = 'App\Entity\Candidatisecondari';
    const ENT_SECONDARIO_LISTA = 'App\Entity\Secondarioxlista';
    const ENT_LISTA_PRINCIPALE = 'App\Entity\Listaxprincipale';
    const ENT_LISTA_PREFERENZE = 'App\Entity\Listapreferenze';
    const ENT_CIRCOSCRIZIONI = 'App\Entity\Circoscrizioni';
    const ENT_CIRCO_CANDIDATO = 'App\Entity\Circoxcandidato';
    //private consts
    private const ENT_STATES = 'App\Entity\States';
    private const ENT_STATESXGRANT = 'App\Entity\Statesxgrant';
  
    //Const variables by external source
    const ENT_RX_SEZIONI = 'App\Entity\Rxsezioni';
    const ENT_RX_VOTANTI = 'App\Entity\Rxvotanti';
    const ENT_RX_VOTI_NON_VALIDI = 'App\Entity\Rxvotinonvalidi';
    const ENT_RX_SCRUTINI_CANDIDATI = 'App\Entity\Rxscrutinicandidati';
    const ENT_RX_SCRUTINI_LISTE = 'App\Entity\Rxscrutiniliste';
    const ENT_RX_CANDIDATI_PRINCIPALI = 'App\Entity\Rxcandidati';
    const ENT_RX_CANDIDATI_SECONDARI = 'App\Entity\Rxcandidatisecondari';

    //private consts, used to create instances of seeds
    private const ENT_RX_LISTE = 'App\Entity\Rxliste';
    private const ENT_RX_PREFERENZE = 'App\Entity\Rxpreferenze';

    /**
     * It create an instance of this RTServicesProvider.
     * It reads environment .env file and handle these details.
     * It prepares seeds in order to interact with storage tables.
     */
    public function __construct(ORMmanager $ORMmanager)
    {
      // The expensive process (e.g.,db connection) goes here.
      $this->pre_comunicazionieventi = getenv('RT_GET_EVENTI');
      $this->pre_liste_candidati = getenv('RT_GET_CANDIDATI');
      $this->ele_votanti = getenv('RT_PUT_VOTANTI');
      $this->ele_scrutini = getenv('RT_PUT_SCRUTINI');
      $this->ele_preferenze = getenv('RT_PUT_PREFERENZE');
      //Host where services are hosted
      $this->host = getenv('RT_HOST');
      //initialize the payload management
      $this->payload = [];
      //set serviceUser as ORMmanager
      $this->serviceUser = $ORMmanager->getServiceUser();
      //prepare seeds
      $this->buildDivoSeeds();
      $this->buildRxSeeds();
    }

    /**
     * For each DIVO entity it defines if is a Static or Sent Divo Entity.
     * This means that Sent foresees sent bit is present into managed table, 
     * Static foresees that sent bit is not present.
     * For default each SentableInterface is off-able (the off flag is present); for tables where this flag is not existent,
     * it's enough to use method unsetOffable()
     */
    private function buildDivoSeeds() 
    {
        $this->ent_eventi = new DivoEntityStatic(RTServicesProvider::ENT_EVENTI);
        $this->ent_comunicazioni = new DivoEntityStatic(RTServicesProvider::ENT_COMUNICAZIONI);
        $this->ent_entexevento = new DivoEntityStatic(RTServicesProvider::ENT_ENTEXEVENTO);
        $this->ent_candidati = new DivoEntityStatic(RTServicesProvider::ENT_CAND_PRINCIPALI);
        $this->ent_candidatisecondari = new DivoEntityStatic(RTServicesProvider::ENT_CAND_SECONDARI);
        $this->ent_secondario_lista = new DivoEntityStatic(RTServicesProvider::ENT_SECONDARIO_LISTA);
        $this->ent_lista_principale = new DivoEntityStatic(RTServicesProvider::ENT_LISTA_PRINCIPALE);
        $this->ent_lista_preferenze = new DivoEntityStatic(RTServicesProvider::ENT_LISTA_PREFERENZE);
        $this->ent_circoscrizioni = new DivoEntityStatic(RTServicesProvider::ENT_CIRCOSCRIZIONI);
        $this->ent_circo_candidato = new DivoEntityStatic(RTServicesProvider::ENT_CIRCO_CANDIDATO);

        $this->ent_states = new DivoEntityStatic(RTServicesProvider::ENT_STATES);
        $this->ent_states->unsetOffable();
        $this->ent_statesxgrant = new DivoEntityStatic(RTServicesProvider::ENT_STATESXGRANT);
        $this->ent_statesxgrant->unsetOffable();
    }

    /**
     * get detail about Link between Circoscrizione and Main Candidate.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedCircoCandidato(): RTSentableInterface 
    {
        return $this->ent_circo_candidato;
    }

    /**
     * get detail about Circoscrizioni.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedCircoscrizioni(): RTSentableInterface 
    {
        return $this->ent_circoscrizioni;
    }

    /**
     * get detail about Preferences Lists.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedListaPreferenze(): RTSentableInterface 
    {
        return $this->ent_lista_preferenze;
    }

    /**
     * get detail about Link between Preference List and Main Candidate.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedListaPrincipale(): RTSentableInterface 
    {
        return $this->ent_lista_principale;
    }

    /**
     * get detail about Link between Secondary candidate and Preference list.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedSecondarioLista(): RTSentableInterface 
    {
        return $this->ent_secondario_lista;
    }

    /**
     * get detail about Secondary Candidadates.
     * It returns a class implementing interface RTSentableInterface
     */
    public function getSeedCandidatiSecondari(): RTSentableInterface 
    {
        return $this->ent_candidatisecondari;
    }

    /**
     * get detail about Main Candidates.
     * It returns a class implementing interface RTSentableInterface
     */    
    public function getSeedCandidati(): RTSentableInterface 
    {
        return $this->ent_candidati;
    }

    /**
     * get detail about Link between Ente and Events.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedEnteEvento(): RTSentableInterface 
    {
        return $this->ent_entexevento;
    }

    /**
     * get detail about Communications.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedComunicazioni(): RTSentableInterface 
    {
        return $this->ent_comunicazioni;
    }

    /**
     * get detail about Events.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedEventi(): RTSentableInterface 
    {
        return $this->ent_eventi;
    }

    /**
     * get detail about States.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedStates(): RTSentableInterface 
    {
        return $this->ent_states;
    }


    /**
     * get detail about Statesxgrant.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedStatesxgrant(): RTSentableInterface 
    {
        return $this->ent_statesxgrant;
    }


    /**
     * For each RX entity it defines if is a Static or Sent Divo Entity.
     * This means that Sent foresees sent bit is present into managed table, 
     * Static foresees that sent bit is not present.
     * For default each SentableInterface is off-able (the off flag is present); for tables where this flag is not existent,
     * it's enough to use method unsetOffable()
     */
    private function buildRxSeeds() 
    {
        $this->ent_rx_liste = new DivoEntityStatic(RTServicesProvider::ENT_RX_LISTE);    
        $this->ent_rx_candidati = new DivoEntityStatic(RTServicesProvider::ENT_RX_CANDIDATI_PRINCIPALI);
        $this->ent_rx_candidatisecondari = new DivoEntityStatic(RTServicesProvider::ENT_RX_CANDIDATI_SECONDARI);
        $this->ent_rx_sezioni = new DivoEntityStatic(RTServicesProvider::ENT_RX_SEZIONI);
        $this->ent_rx_scrutini = new DivoEntitySent(RTServicesProvider::ENT_RX_SCRUTINI_CANDIDATI);
        $this->ent_rx_scrutini_liste = new DivoEntitySent(RTServicesProvider::ENT_RX_SCRUTINI_LISTE);
        $this->ent_rx_affluenze = new DivoEntitySent(RTServicesProvider::ENT_RX_VOTANTI);
        $this->ent_rx_voti_non_validi = new DivoEntitySent(RTServicesProvider::ENT_RX_VOTI_NON_VALIDI);
        $this->ent_rx_preferenze = new DivoEntitySent(RTServicesProvider::ENT_RX_PREFERENZE);
        //unset Off-able
        $this->ent_rx_sezioni->unsetOffable();    
    }


    /**
     * get detail about Preference Lists provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxListe(): RTSentableInterface
    {
        return $this->ent_rx_liste;
    }

    /**
     * get detail about Main candidates provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxCandidati(): RTSentableInterface
    {
        return $this->ent_rx_candidati;
    }



    /**
     * get detail about Main candidates provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxCandidatisecondari(): RTSentableInterface
    {
        return $this->ent_rx_candidatisecondari;
    }

    /**
     * get detail about Pools on main candidates provided by external source.
     * How many votes each main candidates have collected.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxScrutini(): RTSentableInterface
    {
        return $this->ent_rx_scrutini;
    }

    /**
     * get detail about Pools on Preference lists for secondary candidates provided by external source.
     * How many votes each secondary candidate has collected by list.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxPreferenze(): RTSentableInterface
    {
        return $this->ent_rx_preferenze;
    }

    /**
     * get detail about Pools on Lists provided by external source.
     * How many votes each main candidates have collected by list.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxScrutiniListe(): RTSentableInterface
    {
        return $this->ent_rx_scrutini_liste;
    }

    /**
     * get detail about Sections provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxSezioni(): RTSentableInterface
    {
        return $this->ent_rx_sezioni;
    }

    /**
     * get detail about Affluences provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxAffluenze(): RTSentableInterface
    {
        return $this->ent_rx_affluenze;
    }

    /**
     * get detail about Not valid votes provided by external source.
     * It returns a class implementing interface RTSentableInterface
     */ 
    public function getSeedRxVotiNonValidi(): RTSentableInterface
    {
        return $this->ent_rx_voti_non_validi;
    }
   
    /**
     * Return the WS REST API URL to GET Comunicazioni Elettorali
     */
    public function getRT_ComunicazioniEventi() 
    {
        return $this->host.$this->pre_comunicazionieventi;
    }

    /**
     * Return the WS REST API URL to GET Liste and Candidati
     */
    public function getRT_Liste() 
    {
        return $this->host.$this->pre_liste_candidati;
    }

    /**
     * Return the WS REST API URL to PUT Votanti
     */
    public function getRT_Votanti() 
    {
        return $this->host.$this->ele_votanti;
    }

    /**
     * Return the WS REST API URL to PUT Scrutini
     */
    public function getRT_Scrutini() 
    {
        return $this->host.$this->ele_scrutini;
    }

    /**
     * Return the WS REST API URL to PUT Preferenze
     */
    public function getRT_Preferenze() 
    {
        return $this->host.$this->ele_preferenze;
    }

    /**
     * Return the service user using the system
     */
    public function getServiceUser() 
    {
        return $this->serviceUser;
    }

    /**
     * It returns the payload needed to be included into POST requests with target system REGIONE TOSCANA.
     * It is able to return a type of payload or an other one according to the given event.
     */
    public function getServiceUserPayload(String $service, Eventi $evento = null) 
    {
        if (!isset($this->payload[$service])) {
            $evento_codice = '';
            $evento_descrizione = '';
            if ($evento != null) {
                $evento_codice = $evento->getCodiceEvento();
                $evento_descrizione = $evento->getEvento();
            }
            $utente = [
                'username' => $this->serviceUser->getUsername(),
                'password' => $this->serviceUser->getPsw(),
            ];
            $evento = [
                'data' => $this->serviceUser->getDataEvento()->format('Y-m-d'),
                'codice' => $evento_codice,
                'descrizione' => $evento_descrizione,
            ];
            $ente = [
                'codProvincia' => $this->serviceUser->getEnti()->getCodProvincia(),
                'codComune' => $this->serviceUser->getEnti()->getCodComune(),
            ];

            $this->payload[$service] = [
                'utente' => $utente,
                'evento' => $evento,
                'ente' => $ente,
            ];
        }
        return $this->payload[$service];
    }
  }