<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CleanEnvCommand extends Command {

    private $output;
    private $logger;
    private $logfile;

    protected function configure() {
        $this
                ->setName('App:CleanEnv')
                ->setDescription('Clean environment')
                ->addOption('states', null, InputOption::VALUE_NONE, 'Se si devono sovrascrivere i record di states')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Se si devono sovrascrivere i record presenti');
    }

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel, LoggerInterface $logger, \Swift_Mailer $mailer) {
        $this->em = $em;
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->mailer = $mailer;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        if ($input->getOption('force')) {
            
        } else {
            $output->writeln("Specificare l'opzione --force per confermare");

            return;
        }
        $this->output = $output;

        $inizio = microtime(true);

        $this->truncateTable($this->em, 'actionlogs');
        $this->truncateTable($this->em, 'candidatiprincipali');
        $this->truncateTable($this->em, 'candidatisecondari');
        $this->truncateTable($this->em, 'circoscrizioni');
        $this->truncateTable($this->em, 'circoxcandidato');
        $this->truncateTable($this->em, 'confxvotanti');
        $this->truncateTable($this->em, 'entexevento');
        $this->truncateTable($this->em, 'eventi');
        $this->truncateTable($this->em, 'listapreferenze');
        $this->truncateTable($this->em, 'listaxprincipale');
        $this->truncateTable($this->em, 'rxcandidati');
        $this->truncateTable($this->em, 'rxcandidatisecondari');
        $this->truncateTable($this->em, 'rxliste');
        $this->truncateTable($this->em, 'rxpreferenze');
        $this->truncateTable($this->em, 'rxscrutinicandidati');
        $this->truncateTable($this->em, 'rxscrutiniliste');
        $this->truncateTable($this->em, 'rxsezioni');
        $this->truncateTable($this->em, 'rxvotanti');
        $this->truncateTable($this->em, 'rxvotinonvalidi');
        $this->truncateTable($this->em, 'secondarioxlista');

        if ($input->getOption('states')) {
            $this->truncateTable($this->em, 'states');
            $this->truncateTable($this->em, 'statesxgrant');
            $this->truncateTable($this->em, '__bicorebundle_menuapplicazione');
            $output->writeln("<info>Ricordarsi di eseguire il task bin/console App:LoadFixtures<info>");
        }

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        $text = 'Fine in ' . $tempo . ' secondi';
        $output->writeln($text);
    }

    private function truncateTable($em, $tablename) {
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $schema = getenv('BICORE_SCHEMA');
        $connection->executeUpdate($platform->getTruncateTableSQL($schema . '.' . $tablename, true));
        $this->output->writeln('<info>' . $schema . '.' . $tablename . ' clean!</info>');
        //Se si usa PostgresSql si azzerano anche i sequence
        if ('pdo_pgsql' === $connection->getDriver()->getName()) {
            $connection->executeUpdate('ALTER SEQUENCE ' . $schema . '.' . str_replace("__bicorebundle_", "", $tablename) . '_id_seq RESTART WITH 1');
        }
    }

    private function refreshSequence($em, $tablename) {
        $connection = $em->getConnection();

        if ('pdo_pgsql' === $connection->getDriver()->getName()) {
            $camelTable = \Symfony\Component\DependencyInjection\Container::camelize($tablename);
            /* @var $query \Doctrine\ORM\Query */
            $schema = getenv('BICORE_SCHEMA');
            $query = $em->createQuery("SELECT MAX(tbl.id) +1 as newseq FROM App:$camelTable tbl");
            $max = $query->execute();
            $connection->executeUpdate('ALTER SEQUENCE ' . $schema . '.' . $tablename . '_id_seq RESTART WITH ' . ($max[0]['newseq'] ? $max[0]['newseq'] : 1));
        }
    }

}
