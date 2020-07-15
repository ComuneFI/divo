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

class CreateViewsCommand extends Command {

    private $errors;
    private $output;
    private $logger;
    private $logfile;

    protected function configure() {
        $this
                ->setName('App:CreateViews')
                ->setDescription('Creazione viste');
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
        $views = $this->getViews();

        foreach ($views as $viewname => $view) {
            $output->writeln("<info>Creazione $viewname ...</info>");
            $statement = $connection->prepare($view);
            $returnvalue = $statement->execute();
            $status = ($returnvalue ? "OK" : "KO");
            $statustype = ($returnvalue ? "info" : "error");
            $output->writeln("<$statustype>Esito $viewname : $status</$statustype>");
        }


        $fine = microtime(true);
        //$tempo = gmdate('H:i:s', $fine - $inizio);
        //$text = 'Fine in ' . $tempo . ' secondi';
        //$output->writeln($text);
    }

    private function getViews() {
        $schema = getenv("BICORE_SCHEMA");
        $views = array();


        $views["view_sezioni"] = "CREATE OR REPLACE VIEW $schema.view_sezioni
AS SELECT e.ente_id,
    c.evento_id,
    c.circ_desc,
    r.id,
    r.circo_id,
    r.numero,
    r.descrizione,
    r.discr,
    r.stato_wf
   FROM $schema.entexevento e,
    $schema.rxsezioni r,
    $schema.circoscrizioni c,
    $schema.eventi eventi
  WHERE c.evento_id = e.evento_id AND r.circo_id = c.id AND eventi.id = e.evento_id AND e.off IS NOT TRUE AND c.off IS NOT TRUE AND eventi.off IS NOT TRUE
  ORDER BY r.numero;";


        $views["view_affluenze"] = "CREATE OR REPLACE VIEW $schema.view_affluenze
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    vs.numero,
    vs.stato_wf,
    cxv.comunicazione_desc,
    rxv.id,
    rxv.rxsezione_id,
    rxv.confxvotanti_id,
    rxv.num_votanti_maschi,
    rxv.num_votanti_femmine,
    rxv.num_votanti_totali,
    rxv.off,
    rxv.\"timestamp\",
    rxv.sent,
    rxv.discr
   FROM $schema.rxvotanti rxv,
    $schema.confxvotanti cxv,
    $schema.view_sezioni vs
  WHERE cxv.id = rxv.confxvotanti_id AND cxv.evento_id = vs.evento_id AND vs.id = rxv.rxsezione_id AND rxv.off IS NOT TRUE AND cxv.off IS NOT TRUE
  ORDER BY vs.numero;";


        $views["view_enabledsend_scrutini"] = "CREATE OR REPLACE VIEW $schema.view_enabledsend_scrutini
AS SELECT r.rxsezione_id
   FROM $schema.rxscrutinicandidati r,
    $schema.rxvotinonvalidi r3,
    $schema.rxvotanti r4,
    $schema.confxvotanti c
  WHERE r.off IS NOT TRUE AND r3.off IS NOT TRUE AND r4.off IS NOT TRUE AND c.off IS NOT TRUE AND c.comunicazione_final = true AND r4.confxvotanti_id = c.id AND r.rxsezione_id = r3.rxsezione_id AND r.rxsezione_id = r4.rxsezione_id
  GROUP BY r.rxsezione_id
  ORDER BY r.rxsezione_id;";



        $views["view_scrutini_candidatoprincipale"] = "CREATE OR REPLACE VIEW $schema.view_scrutini_candidatoprincipale
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.numero,
    vs.circo_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    cand.cognome,
    cand.nome,
    cand.luogo_nascita,
    cand.sesso,
    cand.id_source,
    scrutini.id,
    scrutini.rxsezione_id,
    scrutini.candidato_principale_id,
    scrutini.voti_totale_candidato,
    scrutini.voti_dicui_solo_candidato,
    scrutini.off,
    scrutini.\"timestamp\",
    scrutini.sent,
    scrutini.discr
   FROM $schema.rxscrutinicandidati scrutini,
    $schema.candidatiprincipali cand,
    $schema.view_sezioni vs
  WHERE cand.id = scrutini.candidato_principale_id AND scrutini.rxsezione_id = vs.id AND scrutini.off IS NOT TRUE AND cand.off IS NOT TRUE
  ORDER BY vs.numero;";

        $views["view_scrutini_candidatosecondario"] = "CREATE OR REPLACE VIEW $schema.view_scrutini_candidatosecondario
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.numero,
    vs.circo_id,
    vs.descrizione AS sezione,
    candsec.cognome,
    candsec.nome,
    candsec.luogo_nascita,
    candsec.sesso,
    candsec.id_source,
    l.lista_desc,
    sxl.posizione,
    preferenze.id,
    preferenze.rxsezione_id,
    preferenze.listapreferenze_id,
    preferenze.candidato_secondario_id,
    preferenze.off,
    preferenze.\"timestamp\",
    preferenze.sent,
    preferenze.numero_voti,
    preferenze.discr
   FROM $schema.rxpreferenze preferenze,
    $schema.candidatisecondari candsec,
    $schema.secondarioxlista sxl,
    $schema.listapreferenze l,
    $schema.view_sezioni vs
  WHERE preferenze.candidato_secondario_id = candsec.id AND sxl.candidato_secondario_id = candsec.id AND preferenze.listapreferenze_id = sxl.lista_id AND sxl.lista_id = l.id AND preferenze.rxsezione_id = vs.id AND preferenze.off IS NOT TRUE AND candsec.off IS NOT TRUE AND sxl.off IS NOT TRUE AND l.off IS NOT TRUE
  ORDER BY vs.numero, l.id, sxl.posizione;";

        $views["view_scrutini_liste"] = "CREATE OR REPLACE VIEW $schema.view_scrutini_liste
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.circo_id,
    vs.numero,
    vs.descrizione AS sezione,
    l.lista_desc,
    l.id_source,
    scrutiniliste.id,
    scrutiniliste.lista_preferenze_id,
    scrutiniliste.rxsezione_id,
    scrutiniliste.voti_tot_lista,
    scrutiniliste.off,
    scrutiniliste.\"timestamp\",
    scrutiniliste.sent,
    scrutiniliste.discr
   FROM $schema.rxscrutiniliste scrutiniliste,
    $schema.listaxprincipale listaxprincipale,
    $schema.listapreferenze l,
    $schema.view_sezioni vs
  WHERE scrutiniliste.lista_preferenze_id = listaxprincipale.lista_id AND listaxprincipale.lista_id = l.id AND scrutiniliste.rxsezione_id = vs.id AND scrutiniliste.off IS NOT TRUE AND listaxprincipale.off IS NOT TRUE AND l.off IS NOT TRUE
  ORDER BY vs.numero, (l.id_source::integer);";


        $views["view_scrutini_votinulli"] = "CREATE OR REPLACE VIEW $schema.view_scrutini_votinulli
AS SELECT vs.ente_id,
    vs.evento_id,
    vs.circ_desc,
    vs.descrizione AS sezione,
    rnon.id,
    rnon.rxsezione_id,
    rnon.numero_schede_bianche,
    rnon.numero_schede_nulle,
    rnon.numero_schede_contestate,
    rnon.tot_voti_dicui_solo_candidato,
    rnon.voti_nulli_liste,
    rnon.voti_nulli_coalizioni,
    rnon.voti_contestati_liste,
    rnon.off,
    rnon.\"timestamp\",
    rnon.sent,
    rnon.discr
   FROM $schema.rxvotinonvalidi rnon,
    $schema.view_sezioni vs
  WHERE rnon.rxsezione_id = vs.id AND rnon.off IS NOT TRUE
  ORDER BY vs.numero;";

        return $views;
    }

}
