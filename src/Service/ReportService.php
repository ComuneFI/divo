<?php

/**
 * It contains usefull queries to perform complex reports or retrive massive data.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\ClientException;

use App\Entity\Confxvotanti;
use App\Entity\Rxsezioni;

use App\Service\ORMManager;
use App\Service\StatesService;

/**
 * ReportService is able to return arrays of results for complex report on database
*/
class ReportService {

    public function __construct(ORMmanager $ORMmanager) 
    {
        $this->ORMmanager = $ORMmanager;
        $this->schema = getenv('BICORE_SCHEMA');
    }

    /**
     * It return an array of elements having following values:
     * - numero: number of section
     * - descrizione: description of section
     * - statowfdesc: description of section state
     * - num_votanti_maschi: number of votes by male
     * - num_votanti_femmine: number of votes by female
     * - num_votanti_totali: num_votanti_maschi + num_votanti_femmine
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     */
    public function reportAffluenze(Confxvotanti $communication): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            sezioni.numero, 
            sezioni.descrizione,
            states.descr as statowfdesc,
            affluenze.num_votanti_maschi ,
            affluenze.num_votanti_femmine ,
            affluenze.num_votanti_totali 
            from
            '.$this->schema.'.confxvotanti as comm,
            '.$this->schema.'.rxvotanti as affluenze,
            '.$this->schema.'.rxsezioni as sezioni,
            '.$this->schema.'.states as states
            where 1=1
            and (comm.off = false or comm.off is null)
            and (affluenze.off = false or affluenze.off is null)
            and affluenze.sent = 0
            and (affluenze.sent = 0)
            and affluenze.confxvotanti_id = comm.id
            and states.entity_ref = \''.StatesService::ENT_SECTION.'\'
            and states.code = sezioni.stato_wf
            and sezioni.id = affluenze.rxsezione_id
            and comm.id = :commid
            order by sezioni.numero asc
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['commid' => $communication->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


    /**
     * It return the interval of section (rows) matching initial section and final section
     */
    public function reportSectionsInterval($startSecId, $endSecId): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select * 
            from 
            '.$this->schema.'.rxsezioni 
            where circo_id in (				                
                select distinct circo_id from 
                '.$this->schema.'.rxsezioni sez,
                '.$this->schema.'.circoscrizioni circo,
                '.$this->schema.'.eventi eventi
                where sez.id in (:startid,:endid)
                and circo.id = sez.circo_id 
                and eventi.id = circo.evento_id 
                and (eventi.off = false or eventi.off is null)
                and (circo.off = false or circo.off is null)
            )
            and numero between 
            (select numero from '.$this->schema.'.rxsezioni where id = :startid) and 
            (select numero from '.$this->schema.'.rxsezioni where id = :endid)
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'startid' => $startSecId,
            'endid' => $endSecId,
            ]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


       /**
     * It return an array of elements having following values:
     * - ente_id: identifier of ente
     * - evento_id: identifier of evento
     * - circ_desc: description of circoscrizione
     * - sezione: description of section
     * - numero: number of section
     * - stato_wf: stato_wf of section
     * - id: id of rxvotanti
     * - rxsezione_id: rxsezione_id of rxvotanti
     * - confxvotanti_id: confxvotanti_id of rxvotanti
     * - num_votanti_maschi: number of votes by male
     * - num_votanti_femmine: number of votes by female
     * - num_votanti_totali: num_votanti_maschi + num_votanti_femmine
     * - off: confxvotanti_id of rxvotanti 
     * - timestamp: timestamp of rxvotanti 
     * - descrizione: description of section
     * - statowfdesc: description of states 
     * - bitnew: bit 1 the record is new, 0 the record is already sent
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     */



  

    public function reportAffluenzeBitnew(Confxvotanti $communication): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = 'select
                    tab1.* ,
                    tab1.sezione as descrizione,
                    states.descr as statowfdesc,
                    CASE WHEN EXISTS(select tab2.id from '.$this->schema.'.rxvotanti tab2  
                            where tab2.rxsezione_id=tab1.rxsezione_id and tab2.sent=1 and tab2.timestamp >= tab1.timestamp 
                            and tab1.confxvotanti_id=tab2.confxvotanti_id ) THEN 0  ELSE 1  END BITNEW 
                from    '.$this->schema.'.view_affluenze tab1,
                        '.$this->schema.'.states as states

                WHERE  
                tab1.sent=0  and states.code = tab1.stato_wf and tab1.confxvotanti_id = :commid  and states.entity_ref=\'RxSezioni\'
                order by tab1.numero::integer
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['commid' => $communication->getId()]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }




    /**
     * It return an array of elements having following values:
     * - type_vote: 1-MAIN for main candidate, 2-LIST for votes inside the lists,
     * - sezione_num: number of section, 
     * - sezione_desc: section descriptions,
     * - sezione_id: identifier internal for a section
     * - cand_id: identifier key of candidate,
     * - cand_cognome: surname of main candidate,
     * - cand_nome: name of main candidate,
     * - cand_sesso: sex,
     * - cand_luogo: birth location,
     * - lista: preference list description,
     * - voti: number of votes,
     * - circo_id: identifier of circoscrizione
     * - evento_id: identifier of evento
     * The report filters for active records where possible.
     * 
     */
    public function reportAllScrutiniCandidatiPrincipali(): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
                select
                \'1-MAIN\' as type_vote,
                sezioni.numero as sezione_num, 
                sezioni.descrizione as sezione_desc,
                sezioni.id as sezione_id,
                candidati.id as cand_id,
                candidati.cognome as cand_cognome,
                candidati.nome as cand_nome,
                candidati.sesso as cand_sesso,
                candidati.luogo_nascita as cand_luogo,
                \'\' as lista,
                scrutini.voti_totale_candidato as voti,
                circoscrizioni.id as circo_id,
                eventi.id as evento_id
                from
                '.$this->schema.'.rxscrutinicandidati as scrutini,
                '.$this->schema.'.rxsezioni as sezioni,
                '.$this->schema.'.candidatiprincipali as candidati,
                '.$this->schema.'.circoscrizioni as circoscrizioni,
                '.$this->schema.'.eventi as eventi
                where 1=1
                and (circoscrizioni.off = false or circoscrizioni.off is null)
                and (eventi.off = false or eventi.off is null)
                and (scrutini.off = false or scrutini.off is null)
                and (scrutini.sent = 0)
                and sezioni.id = scrutini.rxsezione_id
                and candidati.id = scrutini.candidato_principale_id
                and (candidati.off = false or candidati.off is null)
                and sezioni.circo_id =circoscrizioni.id
                and circoscrizioni.evento_id =eventi.id
                UNION
                select 
                \'2-LIST\' as type_vote,
                sezioni.numero as sezione_num, 
                sezioni.descrizione as sezione_desc,
                sezioni.id as sezione_id,
                candidati.id as cand_id,
                candidati.cognome as cand_cognome,
                candidati.nome as cand_nome,
                candidati.sesso as cand_sesso,
                candidati.luogo_nascita as cand_luogo,
                lista.lista_desc as lista,
                scrutiniliste.voti_tot_lista as voti,
                circoscrizioni.id as circo_id,
                eventi.id as evento_id
                from 
                '.$this->schema.'.candidatiprincipali as candidati,
                '.$this->schema.'.listaxprincipale as link,
                '.$this->schema.'.listapreferenze as lista,
                '.$this->schema.'.rxscrutiniliste as scrutiniliste,
                '.$this->schema.'.rxsezioni as sezioni,
                '.$this->schema.'.circoscrizioni as circoscrizioni,
                '.$this->schema.'.eventi as eventi
                where 
                1=1
                and (circoscrizioni.off = false or circoscrizioni.off is null)
                and (eventi.off = false or eventi.off is null)
                and candidati.id = link.candidato_principale_id 
                and lista.id = link.lista_id 
                and (link.off = false or link.off is null)
                and (lista.off = false or lista.off is null)
                and (candidati.off = false or candidati.off is null)
                and (scrutiniliste.off = false or scrutiniliste.off is null)
                and (scrutiniliste.sent = 0)
                and sezioni.id = scrutiniliste.rxsezione_id
                and scrutiniliste.lista_preferenze_id =lista.id 
                and sezioni.circo_id =circoscrizioni.id
                and circoscrizioni.evento_id =eventi.id
                order by sezione_num, cand_id, type_vote asc
        ';
        $stmt = $conn->prepare($sql);
        //$stmt->execute(['sezid' => $section->getId()]);
        $stmt->execute();
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * It return an array of elements having following values:,
     * - sezione_num: number of section, 
     * - sezione_id: identifier internal for a section
     * - schede_bianche: number of white votes,
     * - schede_contestate: number of discussed votes,
     * - schede_nulle: not valid votes
     * - circo_id: identifier of circoscrizione
     * - evento_id: identifier of evento
     * - bitnew: bit 1 the record is new, 0 the record is already sent
     * The report filters for active records where possible.
     * 
     */
    public function reportAllScrutiniVotiNonValidiBitnew(): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
                select
                numero as sezione_num, 
                rxsezione_id as sezione_id,
                numero_schede_bianche as schede_bianche,
                numero_schede_contestate as schede_contestate,
                numero_schede_nulle as schede_nulle,
                circo_id as circo_id,
                evento_id as evento_id,
                CASE  
                WHEN EXISTS(select tab2.id from '.$this->schema.'.rxvotinonvalidi tab2 
                where tab2.rxsezione_id=tab1.rxsezione_id  
                and tab2.sent=1  
                and tab2.timestamp >= tab1.timestamp)  
                THEN 0 
                ELSE 1 
                END BITNEW
                from
                '.$this->schema.'.view_scrutini_votinulli tab1,
                '.$this->schema.'.rxsezioni sezioni
                where
                sezioni.id = tab1.rxsezione_id 
                order by evento_id, sezione_num asc
        ';
        $stmt = $conn->prepare($sql);
        //$stmt->execute(['sezid' => $section->getId()]);
        $stmt->execute();
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


      /**
     * It return an array of elements having following values:
     * - ente_id: identifier of ente
     * - evento_id: identifier of evento
     * - circ_desc: description of circoscrizione
     * - sezione: description of section
     * - numero: number of section
     * - stato_wf: stato_wf of section
     * - id: id of rxvotanti
     * - rxsezione_id: rxsezione_id of rxvotanti
     * - confxvotanti_id: confxvotanti_id of rxvotanti
     * - num_votanti_maschi: number of votes by male
     * - num_votanti_femmine: number of votes by female
     * - num_votanti_totali: num_votanti_maschi + num_votanti_femmine
     * - off: confxvotanti_id of rxvotanti 
     * - timestamp: timestamp of rxvotanti 
     * - descrizione: description of section
     * - statowfdesc: description of states 
     * - bitnew: bit 1 the record is new, 0 the record is already sent
     * The report filters for active records where possible.
     * The report filters for ente.
     */

    public function reportAffluenceFinalBitnew($ente_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = 'select
        tab1.* ,
        tab1.sezione as descrizione,
      
        CASE WHEN EXISTS(select tab2.id from '.$this->schema.'.rxvotanti tab2  
                where tab2.rxsezione_id=tab1.rxsezione_id and tab2.sent=1 and tab2.timestamp >= tab1.timestamp 
                and tab1.confxvotanti_id=tab2.confxvotanti_id ) THEN 0  ELSE 1  END BITNEW 
        from    '.$this->schema.'.view_affluenze tab1,
                '.$this->schema.'.confxvotanti as confxvotanti,
                '.$this->schema.'.entexevento as exe
        WHERE  
        tab1.confxvotanti_id = confxvotanti.id 
        and tab1.off is not true
        and confxvotanti.comunicazione_final=true 
        and confxvotanti.evento_id=exe.evento_id 
        and exe.off is not true
        and exe.ente_id=:ente_id
       
        order by tab1.numero::integer';       
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id]);
        $stmt->execute();
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * It return an array of elements having following values:
     * - type_vote: 1-MAIN for main candidate, 2-LIST for votes inside the lists,
     * - sezione_num: number of section, 
     * - sezione_desc: section descriptions,
     * - sezione_id: identifier internal for a section
     * - cand_id: identifier key of candidate,
     * - cand_cognome: surname of main candidate,
     * - cand_nome: name of main candidate,
     * - cand_sesso: sex,
     * - cand_luogo: birth location,
     * - lista: preference list description,
     * - voti: number of votes,
     * - circo_id: identifier of circoscrizione
     * - evento_id: identifier of evento
     * - bitnew: bit 1 the record is new, 0 the record is already sent
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     */
    public function reportAllScrutiniCandidatiPrincipaliBitnew(): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
                select
                \'1-MAIN\' as type_vote,
                numero as sezione_num, 
                sezione as sezione_desc,
                rxsezione_id as sezione_id,
                candidato_principale_id as cand_id,
                cognome as cand_cognome,
                nome as cand_nome,
                sesso as cand_sesso,
                luogo_nascita as cand_luogo,
                \'\' as lista,
                voti_totale_candidato as voti,
                circo_id as circo_id,
                evento_id as evento_id,
                CASE  
				WHEN EXISTS(select tab2.id from '.$this->schema.'.rxscrutinicandidati tab2 
				where tab2.rxsezione_id=tab1.rxsezione_id  
                and tab2.sent=1  
				and tab2.timestamp >= tab1.timestamp)  
				THEN 0 
				ELSE 1 
				END BITNEW
                from
                '.$this->schema.'.view_scrutini_candidatoprincipale tab1
                
                UNION
                select 
                \'2-LIST\' as type_vote,
                numero as sezione_num, 
                sezione as sezione_desc,
                rxsezione_id as sezione_id,
                candidati.id as cand_id,
                candidati.cognome as cand_cognome,
                candidati.nome as cand_nome,
                candidati.sesso as cand_sesso,
                candidati.luogo_nascita as cand_luogo,
                lista_desc as lista,
                voti_tot_lista as voti,
                circo_id,
                evento_id,
                CASE WHEN EXISTS(select tab2.id from '.$this->schema.'.rxscrutiniliste tab2  
                where tab2.rxsezione_id=tab1.rxsezione_id and tab2.sent=1 and tab2.timestamp >= tab1.timestamp)   
                THEN 0  
                ELSE 1  
                END BITNEW 
                from 
                '.$this->schema.'.view_scrutini_liste tab1 ,
                '.$this->schema.'.candidatiprincipali as candidati,
                '.$this->schema.'.listaxprincipale as link
                where 
             
                candidati.id = link.candidato_principale_id 
                and lista_preferenze_id = link.lista_id 
                and (candidati.off = false or candidati.off is null)
                and (link.off = false or link.off is null)
              
                order by sezione_num, cand_id, type_vote asc
        ';
        $stmt = $conn->prepare($sql);
        //$stmt->execute(['sezid' => $section->getId()]);
        $stmt->execute();
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

  /**
     * It return an array of elements
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id .
     */
    public function getCircoscrizioniByEnte($ente_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc
          
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             where exe.ente_id=:ente_id
             order by eventi.id, circ.id asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


  /**
     * It return an array of elements
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for event_id .
     */
    public function getCircoscrizioniByEvent($event_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc
          
            from
            '.$this->schema.'.eventi eventi
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and circ.evento_id=eventi.id)
             where circ.evento_id=:event_id and eventi.off is not true
             order by eventi.id, circ.id asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['event_id' => $event_id]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * It return an array of elements
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id .
     */
    public function reportForCSVRxsezioni($ente_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             where exe.ente_id=:ente_id
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }



    /**
     * It return an array of elements
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id .
     */
    public function reportForCSVRxsezioniByEvent($event): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             where eventi.id=:event_id
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
    
        $stmt = $conn->prepare($sql);
        $stmt->execute(['event_id' => $event]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


     /**
     * It return an array of elements
     * - id_record: id record of affluences
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - confxvotanti_id: id of confxvotanti
     * - comunicazione_desc: description of confxvotanti
     * - num_votanti_maschi: number of votes by male
     * - num_votanti_femmine: number of votes by female
     * - num_votanti_totali: num_votanti_maschi + num_votanti_femmine
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and type of communcations and sezione.id.
     */
    public function reportForCSVAffluenze($ente_id,$idComm,$sezione_sel): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            affluenze.id as id_record,
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione,
            comm.id as confxvotanti_id,
            comm.comunicazione_desc,
            affluenze.num_votanti_maschi ,
            affluenze.num_votanti_femmine ,
            affluenze.num_votanti_totali 
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.confxvotanti as comm
             ON ( comm.off is not true and exe.evento_id=comm.evento_id and comm.id= :idComm)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             LEFT JOIN '.$this->schema.'.rxvotanti as affluenze
             ON ( affluenze.off is not true and affluenze.rxsezione_id=sezioni.id and comm.id = confxvotanti_id and affluenze.sent=0)
             where exe.ente_id=:ente_id and sezioni.id= :sezione_sel
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'idComm'=>$idComm,'sezione_sel'=>$sezione_sel ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


    /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - posizione: position of candidates 
     * - candidato_principale_id: id of candidates
     * - nome: name of candidates
     * - cognome: surname of candidates
     * - voti_totale_candidato: votes of candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id.
     */
    public function reportForCSVScrutiniCandidati($ente_id,$sezione_sel): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select
            scrutcand.id as id_record, 
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione,
            circoxcandidato.posizione as posizione,
            cand.id as candidato_principale_id,
            cand.nome, 
            cand.cognome,
            scrutcand.voti_totale_candidato
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
             ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
             JOIN '.$this->schema.'.candidatiprincipali as cand
             ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
             LEFT JOIN '.$this->schema.'.rxscrutinicandidati as scrutcand
             ON ( scrutcand.off is not true and scrutcand.sent=0 and scrutcand.rxsezione_id=sezioni.id and cand.id=scrutcand.candidato_principale_id)
        
             where exe.ente_id=:ente_id and sezioni.id= :sezione_sel
             order by eventi.id, circ.id, sezioni.numero::integer asc, circoxcandidato.posizione
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'sezione_sel'=>$sezione_sel ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - posizione: position of candidates 
     * - candidato_principale_id: id of candidates
     * - nome: name of candidates
     * - cognome: surname of candidates
     * - voti_totale_candidato: votes of candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id.
     */
    public function reportForCSVScrutiniCandidatiGlobal($evento_id, array $fields): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $parameters = array();
        $parameters['evento_id'] = $evento_id;

        $sql = ' select ';
        $this->addTitle('Id Record',$fields, $sql, 'scrutcand.id as id_record,');
        $this->addTitle('Id Evento',$fields, $sql, 'eventi.id as evento_id,');
        $this->addTitle('Evento',$fields, $sql, 'eventi.evento,');
        $this->addTitle('Id Circoscrizione',$fields, $sql, 'circ.id as circo_id,');
        $this->addTitle('Circoscrizione',$fields, $sql, 'circ.circ_desc as circ_desc, ');
        $this->addTitle('Sezione',$fields, $sql, 'sezioni.numero,');    
        $this->addTitle('Id Candidato',$fields, $sql, 'cand.id_source as candidato_principale_id,');
        $this->addTitle('Nome',$fields, $sql, 'cand.nome,');
        $this->addTitle('Cognome',$fields, $sql, 'cand.cognome,');
        $this->addTitle('Posizione',$fields, $sql, 'circoxcandidato.posizione as posizione,');
        $this->addTitle('Voti',$fields, $sql, 'scrutcand.voti_totale_candidato,');
        $this->addTitle('Timestamp',$fields, $sql, 'scrutcand.timestamp');
       
        $sql = $sql.' from
        '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
             ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
             JOIN '.$this->schema.'.candidatiprincipali as cand
             ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
             LEFT JOIN '.$this->schema.'.rxscrutinicandidati as scrutcand
             ON ( scrutcand.off is not true and scrutcand.sent=0 and scrutcand.rxsezione_id=sezioni.id and cand.id=scrutcand.candidato_principale_id)
        
             where 1=1
             --and exe.ente_id=:ente_id 
             --and sezioni.id= :sezione_sel
             and eventi.id=:evento_id
             order by eventi.id, circ.id, sezioni.numero::integer asc, circoxcandidato.posizione
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

     /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - lista_preferenze_id: id of list 
     * - lista_desc: description of list
     * - voti_tot_lista: votes of list
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniListe($ente_id,$sezione_sel): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();
        $sql = '
        select 
        rxscrutiniliste.id as id_record, 
        eventi.id as evento_id,
        eventi.evento,
        circ.id as circo_id, 
        circ.circ_desc as circ_desc, 
        sezioni.id as rxsezione_id, 
        sezioni.numero, 
        sezioni.descrizione,
        listapreferenze.id as lista_preferenze_id,
        listapreferenze.lista_desc,
        rxscrutiniliste.voti_tot_lista
        from
        '.$this->schema.'.eventi eventi
          LEFT JOIN 
        '.$this->schema.'.entexevento exe 
         ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
         LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
         ON (  circ.off is not true and exe.evento_id=circ.evento_id)
         LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
         ON (  sezioni.circo_id=circ.id)
         JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
         ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
         JOIN '.$this->schema.'.candidatiprincipali as cand
         ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
         LEFT JOIN '.$this->schema.'.listaxprincipale as lp
         ON ( lp.off is not true and cand.id=lp.candidato_principale_id)
         JOIN '.$this->schema.'.listapreferenze as listapreferenze
         ON ( listapreferenze.off is not true and listapreferenze.id=lp.lista_id)
         LEFT JOIN '.$this->schema.'.rxscrutiniliste as rxscrutiniliste
         ON ( rxscrutiniliste.off is not true and rxscrutiniliste.sent=0 and rxscrutiniliste.rxsezione_id=sezioni.id and rxscrutiniliste.lista_preferenze_id=listapreferenze.id)
        
    
         where exe.ente_id=:ente_id and sezioni.id= :sezione_sel
         order by eventi.id, circ.id, sezioni.numero::integer asc
    ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'sezione_sel'=>$sezione_sel ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
 /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - lista_preferenze_id: id of list 
     * - lista_desc: description of list
     * - voti_tot_lista: votes of list
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for event_id and sezioni.id
     */
    public function reportForCSVScrutiniListGlobal($event_id, array $fields): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $parameters = array();
  
        $parameters['event_id'] = $event_id;

        $sql = ' select ';
        $this->addTitle('Id Record',$fields, $sql, 'rxscrutiniliste.id as id_record,');
        $this->addTitle('Id Evento',$fields, $sql, 'eventi.id as evento_id,');
        $this->addTitle('Evento',$fields, $sql, 'eventi.evento,');
        $this->addTitle('Id Circoscrizione',$fields, $sql, 'circ.id as circo_id,');
        $this->addTitle('Circoscrizione',$fields, $sql, 'circ.circ_desc as circ_desc, ');
        $this->addTitle('Sezione',$fields, $sql, 'sezioni.numero,');
        $this->addTitle('Id Lista',$fields, $sql, 'listapreferenze.id_source as lista_preferenze_id,');
        $this->addTitle('Lista Preferenze',$fields, $sql, 'listapreferenze.lista_desc as lista_desc,');
        $this->addTitle('Voti',$fields, $sql, ' rxscrutiniliste.voti_tot_lista,');
        $this->addTitle('Timestamp',$fields, $sql, 'rxscrutiniliste.timestamp');
       
        $sql = $sql.' from
        '.$this->schema.'.eventi eventi
          LEFT JOIN 
        '.$this->schema.'.entexevento exe 
         ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
         LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
         ON (  circ.off is not true and exe.evento_id=circ.evento_id)
         LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
         ON (  sezioni.circo_id=circ.id)
         JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
         ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
         JOIN '.$this->schema.'.candidatiprincipali as cand
         ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
         LEFT JOIN '.$this->schema.'.listaxprincipale as lp
         ON ( lp.off is not true and cand.id=lp.candidato_principale_id)
         JOIN '.$this->schema.'.listapreferenze as listapreferenze
         ON ( listapreferenze.off is not true and listapreferenze.id=lp.lista_id)
         LEFT JOIN '.$this->schema.'.rxscrutiniliste as rxscrutiniliste
         ON ( rxscrutiniliste.off is not true and rxscrutiniliste.sent=0 and rxscrutiniliste.rxsezione_id=sezioni.id and rxscrutiniliste.lista_preferenze_id=listapreferenze.id)
         where eventi.id= :event_id
         order by eventi.id, circ.id, sezioni.numero::integer, lp.posizione ::integer asc
         ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


     /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - lista_preferenze_id: id of list 
     * - lista_desc: description of list
     * - voti_tot_lista: votes of list
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniListeAll($ente_id,$event_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();
        $sql = '
        select 
        rxscrutiniliste.id as id_record, 
        eventi.id as evento_id,
        eventi.evento,
        circ.id as circo_id, 
        circ.circ_desc as circ_desc, 
        sezioni.id as rxsezione_id, 
        sezioni.numero, 
        sezioni.descrizione,
        listapreferenze.id_source as id_source,
        listapreferenze.id as lista_preferenze_id,
        listapreferenze.lista_desc,
        rxscrutiniliste.voti_tot_lista,
        rxscrutiniliste.timestamp
        from
        '.$this->schema.'.eventi eventi
          LEFT JOIN 
        '.$this->schema.'.entexevento exe 
         ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
         LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
         ON (  circ.off is not true and exe.evento_id=circ.evento_id)
         LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
         ON (  sezioni.circo_id=circ.id)
         JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
         ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
         JOIN '.$this->schema.'.candidatiprincipali as cand
         ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
         LEFT JOIN '.$this->schema.'.listaxprincipale as lp
         ON ( lp.off is not true and cand.id=lp.candidato_principale_id)
         JOIN '.$this->schema.'.listapreferenze as listapreferenze
         ON ( listapreferenze.off is not true and listapreferenze.id=lp.lista_id)
         LEFT JOIN '.$this->schema.'.rxscrutiniliste as rxscrutiniliste
         ON ( rxscrutiniliste.off is not true and rxscrutiniliste.sent=0 and rxscrutiniliste.rxsezione_id=sezioni.id and rxscrutiniliste.lista_preferenze_id=listapreferenze.id)
        
    
 
    ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'event_id'=>$event_id ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    

         /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - lista_preferenze_id: id of list 
     * - lista_desc: description of list
     * - posizione: position of secondary candidates
     * - nome: name of secondary candidates
     * - cognome: surname of secondary candidates
     * - numero_voti: votes of secondary candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniCandidatiSecGlobal(/*$ente_id,*/ $evento_id, array $fields): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $parameters = array();
        //$parameters['ente_id'] = $ente_id;
        $parameters['evento_id'] = $evento_id;


        $sql = ' select ';
        $this->addTitle('Id Record',$fields, $sql, 'rxprefernze.id as id_record,');
        $this->addTitle('Id Evento',$fields, $sql, 'eventi.id as evento_id,');
        $this->addTitle('Evento',$fields, $sql, 'eventi.evento,');
        $this->addTitle('Id Circoscrizione',$fields, $sql, 'circ.id as circo_id,');
        $this->addTitle('Circoscrizione',$fields, $sql, 'circ.circ_desc as circ_desc, ');
        $this->addTitle('Sezione',$fields, $sql, 'sezioni.numero,');
        $this->addTitle('Id Lista Preferenze',$fields, $sql, 'listapreferenze.id_source as lista_preferenze_id,');
        $this->addTitle('Lista preferenze',$fields, $sql, 'listapreferenze.lista_desc,');
        $this->addTitle('Id Candidato',$fields, $sql, 'candidatisecondari.id_source as candidato_secondario_id,');
        $this->addTitle('Nome',$fields, $sql, 'candidatisecondari.nome,');
        $this->addTitle('Cognome',$fields, $sql, 'candidatisecondari.cognome,');
        $this->addTitle('Voti',$fields, $sql, 'rxprefernze.numero_voti,');
        $this->addTitle('Timestamp',$fields, $sql, 'rxprefernze.timestamp');
       
        $sql = $sql.' from
        '.$this->schema.'.eventi eventi
          LEFT JOIN 
        '.$this->schema.'.entexevento exe 
         ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
         LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
         ON (  circ.off is not true and exe.evento_id=circ.evento_id)
         LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
         ON (  sezioni.circo_id=circ.id)
         JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
         ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
         JOIN '.$this->schema.'.candidatiprincipali as cand
         ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
         LEFT JOIN '.$this->schema.'.listaxprincipale as lp
         ON ( lp.off is not true and cand.id=lp.candidato_principale_id)
         JOIN '.$this->schema.'.listapreferenze as listapreferenze
         ON ( listapreferenze.off is not true and listapreferenze.id=lp.lista_id)
         JOIN '.$this->schema.'.secondarioxlista as secondarioxlista
         ON(secondarioxlista.off is not true and secondarioxlista.lista_id=listapreferenze.id)
         JOIN '.$this->schema.'.candidatisecondari as candidatisecondari
         ON(candidatisecondari.off is not true and candidatisecondari.id=secondarioxlista.candidato_secondario_id)
         LEFT JOIN '.$this->schema.'.rxpreferenze as rxprefernze
         ON(rxprefernze.off is not true and rxprefernze.sent =0 and rxprefernze.rxsezione_id=sezioni.id 
            and rxprefernze.listapreferenze_id=listapreferenze.id and rxprefernze.candidato_secondario_id=candidatisecondari.id)
        where 1=1
        --and exe.ente_id=:ente_id 
        and eventi.id=:evento_id
        order by eventi.id, circ.id, sezioni.numero::integer,  lp.posizione ::integer,secondarioxlista.posizione :: integer asc  ';
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * Add the title if has found between allowed titles
     */
    private function addTitle($key, array $titles, String &$sql, String $title) 
    {
        if(in_array($key,$titles)) {
            $sql =  $sql.' '.$title.' ';
          }
    }


     /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - lista_preferenze_id: id of list 
     * - lista_desc: description of list
     * - posizione: position of secondary candidates
     * - nome: name of secondary candidates
     * - cognome: surname of secondary candidates
     * - numero_voti: votes of secondary candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniCandidatiSec($ente_id, $sezione_sel): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $parameters = array();
        $parameters['ente_id'] = $ente_id;

        $sql = '
        select 
        rxprefernze.id as id_record,
        eventi.id as evento_id,
        eventi.evento,
        circ.id as circo_id, 
        circ.circ_desc as circ_desc, 
        sezioni.id as rxsezione_id, 
        sezioni.numero, 
        sezioni.descrizione,
        listapreferenze.id as lista_preferenze_id,
        listapreferenze.lista_desc,
        secondarioxlista.posizione,
        candidatisecondari.id as candidato_secondario_id,
        candidatisecondari.nome,
        candidatisecondari.cognome,
        rxprefernze.numero_voti
        from
        '.$this->schema.'.eventi eventi
          LEFT JOIN 
        '.$this->schema.'.entexevento exe 
         ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
         LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
         ON (  circ.off is not true and exe.evento_id=circ.evento_id)
         LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
         ON (  sezioni.circo_id=circ.id)
         JOIN '.$this->schema.'.circoxcandidato as circoxcandidato
         ON ( circoxcandidato.off is not true  and circoxcandidato.circ_id=circ.id)
         JOIN '.$this->schema.'.candidatiprincipali as cand
         ON ( cand.off is not true and circoxcandidato.candidato_principale_id=cand.id )
         LEFT JOIN '.$this->schema.'.listaxprincipale as lp
         ON ( lp.off is not true and cand.id=lp.candidato_principale_id)
         JOIN '.$this->schema.'.listapreferenze as listapreferenze
         ON ( listapreferenze.off is not true and listapreferenze.id=lp.lista_id)
         JOIN '.$this->schema.'.secondarioxlista as secondarioxlista
         ON(secondarioxlista.off is not true and secondarioxlista.lista_id=listapreferenze.id)
         JOIN '.$this->schema.'.candidatisecondari as candidatisecondari
         ON(candidatisecondari.off is not true and candidatisecondari.id=secondarioxlista.candidato_secondario_id)
         LEFT JOIN '.$this->schema.'.rxpreferenze as rxprefernze
         ON(rxprefernze.off is not true and rxprefernze.sent =0 and rxprefernze.rxsezione_id=sezioni.id 
            and rxprefernze.listapreferenze_id=listapreferenze.id and rxprefernze.candidato_secondario_id=candidatisecondari.id)
        where 1=1
        and exe.ente_id=:ente_id and sezioni.id=:sezione_sel 
         order by eventi.id, circ.id, sezioni.numero::integer asc ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'sezione_sel'=>$sezione_sel ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

/**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - posizione: position of candidates 
     * - candidato_principale_id: id of candidates
     * - nome: name of candidates
     * - cognome: surname of candidates
     * - voti_totale_candidato: votes of candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniVotiNulli($ente_id,$sezione_sel): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();


        $sql = '
            select 
            rxvotinonvalidi.id as id_record,
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione,
            rxvotinonvalidi.numero_schede_bianche,
            rxvotinonvalidi.numero_schede_nulle,
            rxvotinonvalidi.numero_schede_contestate,
            rxvotinonvalidi.tot_voti_dicui_solo_candidato
            from
            '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             LEFT JOIN '.$this->schema.'.rxvotinonvalidi as rxvotinonvalidi
             ON ( rxvotinonvalidi.off is not true and rxvotinonvalidi.sent=0 and rxvotinonvalidi.rxsezione_id=sezioni.id)
        
             where exe.ente_id=:ente_id and sezioni.id= :sezione_sel
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id,'sezione_sel'=>$sezione_sel ]);
         // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /**
     * It return an array of elements
     * - id_record: id record of votes
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - posizione: position of candidates 
     * - candidato_principale_id: id of candidates
     * - nome: name of candidates
     * - cognome: surname of candidates
     * - voti_totale_candidato: votes of candidates
     
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for ente_id and sezioni.id
     */
    public function reportForCSVScrutiniVotiNulliGlobal($evento_id, $fields): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $parameters = array();
        $parameters['evento_id'] = $evento_id;

        $sql = ' select ';
        $this->addTitle('Id Record',$fields, $sql, 'rxvotinonvalidi.id as id_record,');
        $this->addTitle('Id Evento',$fields, $sql, 'eventi.id as evento_id,');
        $this->addTitle('Evento',$fields, $sql, 'eventi.evento,');
        $this->addTitle('Id Circoscrizione',$fields, $sql, 'circ.id as circo_id,');
        $this->addTitle('Circoscrizione',$fields, $sql, 'circ.circ_desc as circ_desc, ');
        $this->addTitle('Sezione',$fields, $sql, 'sezioni.numero,');
        $this->addTitle('Schede Bianche',$fields, $sql, 'rxvotinonvalidi.numero_schede_bianche,');
        $this->addTitle('Schede Nulle',$fields, $sql, 'rxvotinonvalidi.numero_schede_nulle,');
        $this->addTitle('Schede Contestate',$fields, $sql, 'rxvotinonvalidi.numero_schede_contestate,');
        $this->addTitle('Timestamp',$fields, $sql, 'rxvotinonvalidi.timestamp');
       
        $sql = $sql.' from
        '.$this->schema.'.eventi eventi
              LEFT JOIN 
            '.$this->schema.'.entexevento exe 
             ON (eventi.off is not true and exe.off is not true and eventi.id=exe.evento_id  )
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and exe.evento_id=circ.evento_id)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             LEFT JOIN '.$this->schema.'.rxvotinonvalidi as rxvotinonvalidi
             ON ( rxvotinonvalidi.off is not true and rxvotinonvalidi.sent=0 and rxvotinonvalidi.rxsezione_id=sezioni.id)
        
             where 1=1
             --and exe.ente_id=:ente_id 
             --and sezioni.id= :sezione_sel
             and eventi.id=:evento_id
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
         // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


     /**
     * It return an array of elements having following values:
     * 
     * - ente_id: identifier of ente
     * - evento_id: identifier of evento
     * - numero: number of section
     * - circo_id: identifier of circoscrizione
     * - descrizione: description of section
     * - cognome: surname of secondary candidates
     * - id_source: id_source of secondary candidates
     * - nome: name of secondary candidates
     * - lista_desc: description of list
     * - posizione: position of secondary candidates
     * - id: id of rxpreferences
     * - rxsezione_id: rxsezione_id of rxpreferences
     * - listapreferenze_id: listapreferenze_id of rxpreferences
     * - candidato_secondario_id: candidato_secondario_id of rxpreferences
     * - off: off of rxpreferences
     * - timestamp: timestamp of rxpreferences
     * - sent: sent of rxpreferences
     * - off: off of rxpreferences
     * - numero_voti: votes of secondary candidates
     * - bitnew: bit 1 the record is new, 0 the record is already sent
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     */
    public function reportPreferencesBitnew($ente_id): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

 
        $sql ='select
                tab1.* ,
                tab1.sezione as descrizione,
            
                CASE WHEN EXISTS(select tab2.id from '.$this->schema.'.rxpreferenze tab2  
                        where tab2.rxsezione_id=tab1.rxsezione_id and tab2.sent=1 and tab2.timestamp >= tab1.timestamp 
                        and tab2.listapreferenze_id =tab1.listapreferenze_id  and tab2.candidato_secondario_id =tab1.candidato_secondario_id 
                    ) THEN 0  ELSE 1  END BITNEW 
                    from    '.$this->schema.'.view_scrutini_candidatosecondario tab1

            WHERE  
            tab1.sent=0  and tab1.ente_id = :ente_id
            order by tab1.numero::integer
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }



    /**
     * It return an array of rxsezioni_id valid
     * 
     */
    public function getSectionEnabledToSend($type): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();
      
        switch($type){
            case 'scrutini'; 
            $sql = '
                    select * from '.$this->schema.'.view_enabledsend_scrutini
                    ';
        
            break;
            case 'preferenze'; $view= "view_enabledsend_preferenze"; break;
          
        }
      
     
        $stmt = $conn->prepare($sql);
        $stmt->execute();
 
     
       
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE)        ;
    }





    /**
     * It return number of principal candidates not configurate
     */
    public function reportNotConfCandPrinc($ente_id): int 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
              select count(c.*) as n 
                from 
                '.$this->schema.'.entexevento exe,
                '.$this->schema.'.circoscrizioni circ,
                '.$this->schema.'.circoxcandidato cc, 
                '.$this->schema.'.candidatiprincipali c 
                where
                exe.ente_id =:ente_id
                and exe.evento_id = circ.evento_id
                and circ.id=cc.circ_id 
                and cc.candidato_principale_id=c.id
                and  exe.off is not true and circ.off is not true and cc.off is not true and c.off is not true
                and (c.id_source is null or c.id_source =\'\') and cc.candidato_principale_id = c.id 

            ';
           
        $stmt = $conn->prepare($sql);
        $stmt->execute(['ente_id' => $ente_id]);
        $values=$stmt->fetchAll(); 
        return $values[0]['n'];  

    }


    /**
     * It return number of secondary candidates not configurate
     */
    public function reportNotConfCandSec($ente_id): int 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

       $sql = '
               select count(csec.*) as n
          from 
          '.$this->schema.'.entexevento exe,
          '.$this->schema.'.circoscrizioni circ,
          '.$this->schema.'.circoxcandidato cc, 
          '.$this->schema.'.listaxprincipale lxp ,
          '.$this->schema.'.listapreferenze l, 
          '.$this->schema.'.secondarioxlista sxl, 
          '.$this->schema.'.candidatisecondari csec 
          where
          exe.ente_id =:ente_id
          and exe.evento_id = circ.evento_id
          and circ.id=cc.circ_id 
          and  exe.off is not true and circ.off is not true and cc.off is not true  
          and lxp.off is not true and l.off is not true and sxl.off is not true and csec.off is not true
          and lxp.candidato_principale_id=cc.candidato_principale_id and lxp.lista_id=l.id
          and sxl.lista_id =l.id
          and sxl.candidato_secondario_id =csec.id 
          and (csec.id_source is null or csec.id_source =\'\') 

      ';
      $stmt = $conn->prepare($sql);
      $stmt->execute(['ente_id' => $ente_id]);
      $values=$stmt->fetchAll(); 
      return $values[0]['n'];      
        
    }


      /**
     * It return number of lists not configurate
     */
    public function reportNotConfLists($ente_id): int 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
      
          select count(l.*) as n 
          from 
          '.$this->schema.'.entexevento exe,
          '.$this->schema.'.circoscrizioni circ,
          '.$this->schema.'.circoxcandidato cc, 
          '.$this->schema.'.listaxprincipale lxp ,
          '.$this->schema.'.listapreferenze l
          where
          exe.ente_id =:ente_id
          and exe.evento_id = circ.evento_id
          and circ.id=cc.circ_id 
          and  exe.off is not true and circ.off is not true and cc.off is not true  
          and lxp.off is not true and l.off is not true
          and (l.id_source is null or l.id_source =\'\') 
          and lxp.candidato_principale_id=cc.candidato_principale_id and lxp.lista_id=l.id
          
      ';
           
      $stmt = $conn->prepare($sql);
      $stmt->execute(['ente_id' => $ente_id]);
      $values=$stmt->fetchAll(); 
      return $values[0]['n'];  
    }



//metodi per csv massivi


 /**
     * It return an array of elements
     * - id_record: id record of affluences
     * - evento_id: identifier of evento
     * - evento: description of evento
     * - circo_id: identifier of circoscrizione
     * - circ_desc: description of circoscrizione
     * - rxsezione_id: id of section
     * - numero: number of section
     * - descrizione: description of section
     * - confxvotanti_id: id of confxvotanti
     * - comunicazione_desc: description of confxvotanti
     * - num_votanti_maschi: number of votes by male
     * - num_votanti_femmine: number of votes by female
     * - num_votanti_totali: num_votanti_maschi + num_votanti_femmine
     * The report filters for active records where possible.
     * The report filters for not sent records where possible.
     * The report filters for event_id and type of communcations and sezione.id.
     */
    public function reportForCSVAffluenzeAll($event_id,$idComm): array 
    {
        $conn = $this->ORMmanager->getManager()->getConnection();

        $sql = '
            select 
            affluenze.id as id_record,
            eventi.id as evento_id,
            eventi.evento,
            circ.id as circo_id, 
            circ.circ_desc as circ_desc, 
            sezioni.id as rxsezione_id, 
            sezioni.numero, 
            sezioni.descrizione,
            comm.id as confxvotanti_id,
            comm.comunicazione_desc,
            affluenze.num_votanti_maschi ,
            affluenze.num_votanti_femmine ,
            affluenze.num_votanti_totali ,
            affluenze.timestamp
            from
            '.$this->schema.'.eventi eventi
             LEFT JOIN '.$this->schema.'.circoscrizioni as circ 
             ON (  circ.off is not true and eventi.id=circ.evento_id )
             LEFT JOIN '.$this->schema.'.confxvotanti as comm
             ON ( comm.off is not true and circ.evento_id=comm.evento_id and comm.id= :idComm)
             LEFT JOIN '.$this->schema.'.rxsezioni as sezioni
             ON (  sezioni.circo_id=circ.id)
             LEFT JOIN '.$this->schema.'.rxvotanti as affluenze
             ON ( affluenze.off is not true and affluenze.rxsezione_id=sezioni.id and comm.id = confxvotanti_id and affluenze.sent=0)
             where circ.evento_id=:event_id and eventi.off is not true
             order by eventi.id, circ.id, sezioni.numero::integer asc
        ';
     
        $stmt = $conn->prepare($sql);
        $stmt->execute(['event_id' => $event_id,'idComm'=>$idComm ]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }


}
