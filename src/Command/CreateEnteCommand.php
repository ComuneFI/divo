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

class CreateEnteCommand extends Command {

    private $errors;
    private $output;
    private $logger;
    private $logfile;

    protected function configure() {
        $this
                ->setName('App:CreateEnte')
                ->setDescription('Creazione ente')
                ->addArgument('descrizione_comune', InputArgument::REQUIRED, 'Descrizione comune?')
                ->addArgument('codice_provincia', InputArgument::REQUIRED, 'Codice provincia?')
                ->addArgument('codice_comune', InputArgument::REQUIRED, 'Codice comune?');
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

        $descrizione_comune = $input->getArgument('descrizione_comune');
        $codice_provincia = $input->getArgument('codice_provincia');
        $codice_comune = $input->getArgument('codice_comune');


        //Si controlla se è già presente l'utente
        $entiobj = $this->em->getRepository(Enti::class)->findAll();
        if (count($entiobj) > 0) {
            $output->writeln("<error>Ente già presente</error>");
            return -1;
        }

        $newEnte = new Enti();
        $newEnte->setDescrizione($descrizione_comune);
        $newEnte->setCodProvincia($codice_provincia);
        $newEnte->setCodComune($codice_comune);
        $this->em->persist($newEnte);
        $this->em->flush();

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        //$text = 'Fine in ' . $tempo . ' secondi';
        //$output->writeln($text);
    }

}
