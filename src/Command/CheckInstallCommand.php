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
use Symfony\Component\HttpKernel\KernelInterface;

class CheckInstallCommand extends Command
{
    private $errors;
    private $output;
    private $logger;
    private $logfile;

    protected function configure()
    {
        $this
                ->setName('App:CheckInstall')
                ->setDescription('Check install environment');
    }

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel, LoggerInterface $logger, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->mailer = $mailer;
        $this->errors = false;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $this->output = $output;

        $inizio = microtime(true);

        $entiobj = $this->em->getRepository(Enti::class)->findAll();
        foreach ($entiobj as $ente) {
            $output->writeln('Trovato ente: '.$ente->getDescrizione().' (id: '.$ente->getId().')');
        }
        if (0 === count($entiobj)) {
            $output->writeln('<error>Tabella Enti vuota</error>');
            $this->errors = true;
        }

        $statesobj = $this->em->getRepository(States::class)->findAll();
        if (0 === count($statesobj)) {
            $output->writeln('<error>Tabella States vuota</error>');
            $this->errors = true;
        }
        foreach ($statesobj as $state) {
            $output->writeln('Trovato states : '.$state->getDescr().' (id: '.$state->getId().')');
        }

        $statesxgrantobj = $this->em->getRepository(Statesxgrant::class)->findAll();
        if (0 === count($statesxgrantobj)) {
            $output->writeln('<error>Tabella Statesxgrant vuota</error>');
            $this->errors = true;
        }
        foreach ($statesxgrantobj as $state) {
            $output->writeln('Trovato statesxgrant : '.$state->getCurrent().' (id: '.$state->getId().')');
        }
        $utentiobj = $this->em->getRepository(Utenti::class)->findAll();
        if (0 === count($utentiobj)) {
            $output->writeln('<error>Tabella Utenti vuota</error>');
            $this->errors = true;
        }
        foreach ($utentiobj as $utente) {
            $output->writeln('Trovato Utente: '.$utente->getUsername().' '.$utente->getEnti()->getDescrizione().' (id: '.$utente->getId().')');
        }

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        $text = 'Fine in '.$tempo.' secondi';
        $output->writeln($text);

        if ($this->errors) {
            return -1;
        }
    }
}
