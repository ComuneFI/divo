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

class CreateUserCommand extends Command {

    private $errors;
    private $output;
    private $logger;
    private $logfile;

    protected function configure() {
        $this
                ->setName('App:CreateUser')
                ->setDescription('Creazione utente di lavoro')
                ->addArgument('divousername', InputArgument::REQUIRED, 'Divo Username?')
                ->addArgument('divouserpassword', InputArgument::REQUIRED, 'Divo password?')
                ->addArgument('divouseremail', InputArgument::REQUIRED, 'Divo Email?')
                ->addArgument('rtusername', InputArgument::REQUIRED, 'Regione toscana Username?')
                ->addArgument('rtuserpassword', InputArgument::REQUIRED, 'Regione toscana password?');
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

        $divousername = $input->getArgument('divousername');
        $divouserpassword = $input->getArgument('divouserpassword');
        $divouseremail = $input->getArgument('divouseremail');
        $rtusername = $input->getArgument('rtusername');
        $rtuserpassword = $input->getArgument('rtuserpassword');

        //Si controlla se è già presente l'utente
        $utentiobj = $this->em->getRepository(\Cdf\BiCoreBundle\Entity\Operatori::class)->findByUsername($divousername);
        if (count($utentiobj) > 0) {
            $output->writeln("<error>Utente " . $divousername . " già presente</error>");
            return -1;
        }
        //Si controlla se è già presente l'ente
        $entiobj = $this->em->getRepository(Enti::class)->findAll();
        if (count($entiobj) != 1) {
            $output->writeln("<error>Nella tabella Enti deve essere presente un solo Ente, trovati: " . count($entiobj) . "</error>");
            return -1;
        }
        //Si prende il primo ente
        $ente = $entiobj[0];

        //Si esegue il comando che crea l'utente fosuserbundle
        $command = $this->getApplication()->find('fos:user:create');

        $arguments = [
            'username' => $divousername,
            'email' => $divouseremail,
            'password' => $divouserpassword,
        ];

        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

        //Si prende l'utente appena creato nella tabella di bicore Operatori
        $utentibiobj = $this->em->getRepository(\Cdf\BiCoreBundle\Entity\Operatori::class)->findByUsername($divousername);
        //Si prende il ruolo Amministratore
        $ruoliadmin = $this->em->getRepository(\Cdf\BiCoreBundle\Entity\Ruoli::class)->FindBy(array("admin" => true, "superadmin" => false));
        $ruoloadmin = $ruoliadmin[0];
        $newOperatore = $utentibiobj[0];

        //Una volta creato l'utente si sitema il ruolo e l'attributo operatore
        $newOperatore->setOperatore($divousername);
        $newOperatore->setRuoli($ruoloadmin);
        $this->em->persist($newOperatore);
        $this->em->flush();

        //Si controlla se sono presenti i permessi, in caso non siano presenti si creano
        $this->checkPermission("Enti", $ruoloadmin);
        $this->checkPermission("Utenti", $ruoloadmin);
        $this->checkPermission("Listapreferenze", $ruoloadmin);
        $this->checkPermission("Rxcandidati", $ruoloadmin);
        $this->checkPermission("Rxliste", $ruoloadmin);
        $this->checkPermission("Rxcandidatisecondari", $ruoloadmin);
        $this->checkPermission("States", $ruoloadmin);
        $this->checkPermission("Statesxgrant", $ruoloadmin);
        $this->checkPermission("divo", $ruoloadmin, "r");


        //Si crea l'utente per DIVO con i parametri passati
        $newutenti = new Utenti();
        $newutenti->setUsername($rtusername);
        $newutenti->setPsw($rtuserpassword);
        $newutenti->setEnti($ente);
        $newutenti->setUserId($newOperatore->getId());

        $this->em->persist($newutenti);
        $this->em->flush();

        $fine = microtime(true);
        $tempo = gmdate('H:i:s', $fine - $inizio);

        //$text = 'Fine in ' . $tempo . ' secondi';
        //$output->writeln($text);
    }

    private function checkPermission($modulo, $ruoloadmin, $permesso = "crud") {
        $ruolibiobj = $this->em->getRepository(\Cdf\BiCoreBundle\Entity\Permessi::class)->findBy(array("modulo" => $modulo, "ruoli" => $ruoloadmin));
        if (count($ruolibiobj) == 0) {
            $newpermesso = new \Cdf\BiCoreBundle\Entity\Permessi();
            $newpermesso->setModulo($modulo);
            $newpermesso->setCrud($permesso);
            $newpermesso->setRuoli($ruoloadmin);

            $this->em->persist($newpermesso);
            $this->em->flush();
        }
    }

}
