<?php
// src/Controller/DummyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Service\RTDivoDataMiner;
use App\Service\ReportService;
use App\Entity\Rxcandidati;
use App\Entity\Rxcandidatisecondari;
use App\Entity\Rxliste;



/**
 * This is the controller managing navigation of app
 */
class RTCSVController extends DivoController
{

    /**
     *  @Route("/csv/{event}/download/{fileRequested}.csv", name="downCSVEvent")
     */
    public function downloadCSVEvent($fileRequested, $event)
    {
        //prepare the array of records to export as CSV
        $rows = array();

        if ($fileRequested == "report_preferenze") {
            $data = array(
                'Evento','Circoscrizione','Sezione','Lista preferenze','Id Candidato','Nome','Cognome','Voti','Timestamp'
             );
            $rows = $this->extractPreferenze($data, $event);
        }
        elseif ($fileRequested == "template_preferenze") {
            $data = array(
                'Sezione','Id Lista Preferenze','Id Candidato','Voti','Timestamp'
             );
            $rows = $this->extractPreferenze($data, $event);
        }
        elseif ($fileRequested == "report_liste") {
            $data = array(
                'Evento','Circoscrizione','Sezione','Id Lista','Lista Preferenze','Voti','Timestamp'
             );
        
            $rows = $this->extractListe($data, $event);
        }
        elseif ($fileRequested == "template_liste") {
            $data = array(
                'Sezione','Id Lista','Lista Preferenze','Voti','Timestamp'
             );

            $rows = $this->extractListe($data, $event);
        }
        elseif ($fileRequested == "report_candidati") {
            $data = array('Evento','Circoscrizione','Sezione','Id Candidato','Nome','Cognome','Posizione','Voti','Timestamp');
            $rows = $this->extractCandidates($data, $event);
        }
        elseif ($fileRequested == "template_candidati") {
            $data = array('Sezione','Id Candidato','Nome','Cognome','Voti','Timestamp');
            $rows = $this->extractCandidates($data, $event);
        }
        
        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        //return the file CSV as download
        return $response;
    }

    /**
     *  @Route("/csv/download/{fileRequested}.csv", name="downCSV")
     */
    public function downloadCSV($fileRequested)
    {
        //prepare the array of records to export as CSV
        $rows = array();

        //in case of "candidati" it prepares export for them
        if ($fileRequested == "candidati") {
            $rows = $this->getCandidatiRows($rows);
        }
        elseif ($fileRequested == "liste") {
            $rows = $this->getListeRows($rows);
        }
        elseif ($fileRequested == "secondari") {
            $rows = $this->getCandidatiSecondariRows($rows);
        }
          
        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        //return the file CSV as download
        return $response;
    }

    /**
     * Extract Preferences from database as CSV format
     */
    private function extractPreferenze($data, $event): array 
    {
        $rows = array();
        //Header of file that it will be exported
        $rows[] = implode(';', $data);
       
        $records= $this->divoMiner->getReportService()->reportForCSVScrutiniCandidatiSecGlobal($event, $data);
        foreach($records as $record) {
            $rows[] = implode(';', $record);
        }
        return $rows;
    }

    /**
     * Extract Preferences from database as CSV format
     */
    private function extractListe($data, $event): array 
    {
        $rows = array();
        //Header of file that it will be exported
        $rows[] = implode(';', $data);
       
        $serviceUser = $this->ORMmanager->getServiceUser(); 
        $records= $this->divoMiner->getReportService()->reportForCSVScrutiniListGlobal($event, $data);
    
        foreach($records as $record) {
            $rows[] = implode(';', $record);
        }
        
        return $rows;
    }

    /**
     * It transforms a not valid votes record into a main candidate record
     */
    private function makeCandidateRecord(String $label, String $label_key, &$record, &$titles):array 
    {
        $array = array();
        if (in_array('Evento',$titles)) { $array['evento'] = $record['evento']; } 
        if (in_array('Circoscrizione',$titles)) { $array['circ_desc'] = $record['circ_desc']; } 
        if (in_array('Sezione',$titles)) {  $array['numero']=$record['numero']; } 
        if (in_array('Id Candidato',$titles)) {$array['candidato_principale_id']=0; } 
        if (in_array('Nome',$titles)) {  $array['nome']=$label; } 
        if (in_array('Cognome',$titles)) { $array['cognome']=$label; } 
        if (in_array('Posizione',$titles)) { $array['posizione']=0; } 
        if (in_array('Voti',$titles)) {$array['voti_totale_candidato']=$record[$label_key]; } 
        if (in_array('Timestamp',$titles)) { $array['timestamp']=$record['timestamp']; } 
        return $array;
    }

    /**
     * Extract Candidates from database as CSV format
     */
    private function extractCandidates($data, $event): array 
    {
        $rows = array();
        //Header of file that it will be exported
        $rows[] = implode(';', $data);
        
        $records= $this->divoMiner->getReportService()->reportForCSVScrutiniCandidatiGlobal($event, $data);
        $fieldsEmptyVotes = ['Schede Bianche','Schede Contestate','Schede Nulle'];
        $data = array_merge($data, $fieldsEmptyVotes);
        $records_nulli= $this->divoMiner->getReportService()->reportForCSVScrutiniVotiNulliGlobal($event, $data);

        foreach($records_nulli as $record_nulli){
            $record_bianche = $this->makeCandidateRecord('__BIANCHE__', 'numero_schede_bianche', $record_nulli, $data);
            $record_contestate = $this->makeCandidateRecord('__CONTESTATE__', 'numero_schede_contestate', $record_nulli, $data);
            $record_schedenulle = $this->makeCandidateRecord('__NULLE__', 'numero_schede_nulle', $record_nulli, $data);

            //we are appending last ones
            array_push($records,$record_bianche,$record_contestate,$record_schedenulle );
        }
    
        foreach($records as $record) {
            $rows[] = implode(';', $record);
        }
        
        return $rows;
    }

    /**
     * Return the rows for Liste, including also Rx Liste already matched with DIVO records
     */
    private function getListeRows( $rows ) {
        $serviceUser = $this->ORMmanager->getServiceUser();
 
        //Header of file that it will be exported
        $data = array(
            'Id',
            'Id Target',
            'Lista',
            'Id Source',
            'Lista Source'
          );
        $rows[] = implode(',', $data);

        //divo lists of main candidates
        $divoLists =  $this->divoMiner->readListe();
        foreach ($divoLists as $divoList ) {
            //look for 1 Rx candidate matching with divo candidate
            $rxList = new Rxliste();
            if ( $divoList->getIdSource() != null ) {
                $rxList = $this->ORMmanager->getOneEntity(Rxliste::class, [ 
                    'id_source' => $divoList->getIdSource(),
                    'ente_id' => $serviceUser->getEnti()->getId(), ]);
            }
            //append the additional row to the file 
            $data = array(
                "\"".$divoList->getId()."\"",
                "\"".$divoList->getIdTarget()."\"",
                "\"".$divoList->getListaDesc()."\"",
                "\"".$divoList->getIdSource()."\"",
                "\"".$rxList->getListaDesc()."\"",
              );
            $rows[] = implode(',', $data);
        }

        return $rows;      
    } 

    /**
     * Return the rows for Candidati, including also Rx Records already matched with DIVO records
     */
    private function getCandidatiRows( $rows ) {
        $serviceUser = $this->ORMmanager->getServiceUser();
        $divoMiner = $this->divoMiner;
 
        //Header of file that it will be exported
        $data = array(
            'Id',
            'Id Target',
            'Nome',
            'Cognome',
            'Luogo di Nascita',
            'Sesso',
            'Id Source',
            'Nome Source',
            'Cognome Source'
          );
        $rows[] = implode(',', $data);

        //divo candidates
        $candidates =  $divoMiner->getAllMainCandidates();
        foreach ($candidates as $candidate ) {
            //look for 1 Rx candidate matching with divo candidate
            $rxCandidate = new Rxcandidati();
            if ( $candidate->getIdSource() != null ) {
                $rxCandidate = $this->ORMmanager->getOneEntity(Rxcandidati::class, [ 
                    'id_source' => $candidate->getIdSource(),
                    'ente_id' => $serviceUser->getEnti()->getId(), ]);
            }
            //append the additional row to the file 
            $data = array(
                "\"".$candidate->getId()."\"",
                "\"".$candidate->getIdTarget()."\"",
                "\"".$candidate->getNome()."\"",
                "\"".$candidate->getCognome()."\"",
                "\"".$candidate->getLuogoNascita()."\"",
                "\"".$candidate->getSesso()."\"",
                "\"".$candidate->getIdSource()."\"",
                "\"".$rxCandidate->getnome()."\"",
                "\"".$rxCandidate->getcognome()."\""
              );
            $rows[] = implode(',', $data);
        }

        return $rows;      
    }

       /**
     * Return the rows for Candidati secondari, including also Rx Records already matched with DIVO records
     */
    private function getCandidatiSecondariRows( $rows ) {
        $serviceUser = $this->ORMmanager->getServiceUser();
        $divoMiner = $this->divoMiner;
 
        //Header of file that it will be exported
        $data = array(
            'Id',
            'Id Target',
            'Nome',
            'Cognome',
            'Luogo di Nascita',
            'Sesso',
            'Id Source',
            'Nome Source',
            'Cognome Source'
          );
        $rows[] = implode(',', $data);

        //divo candidates
        $candidates =  $divoMiner->readCandidatiSecondari();
        foreach ($candidates as $candidate ) {
            //look for 1 Rx candidate matching with divo candidate
            $rxCandidate = new Rxcandidatisecondari();
            if ( $candidate->getIdSource() != null ) {
                $rxCandidate = $this->ORMmanager->getOneEntity(Rxcandidatisecondari::class, [ 
                    'id_source' => $candidate->getIdSource(),
                    'ente_id' => $serviceUser->getEnti()->getId(), ]);
            }
            //append the additional row to the file 
            $data = array(
                "\"".$candidate->getId()."\"",
                "\"".$candidate->getIdTarget()."\"",
                "\"".$candidate->getNome()."\"",
                "\"".$candidate->getCognome()."\"",
                "\"".$candidate->getLuogoNascita()."\"",
                "\"".$candidate->getSesso()."\"",
                "\"".$candidate->getIdSource()."\"",
                "\"".$rxCandidate->getnome()."\"",
                "\"".$rxCandidate->getcognome()."\""
              );
            $rows[] = implode(',', $data);
        }
        return $rows;      
    }


}



