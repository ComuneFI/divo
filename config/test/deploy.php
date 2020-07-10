<?php

use EasyCorp\Bundle\EasyDeployBundle\Configuration\Option;
use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class() extends DefaultDeployer {
    public function configure()
    {
        $currentTag = getenv('CI_COMMIT_TAG');
        $this->log('Deploy tag:'.$currentTag);

        return $this->getConfigBuilder()
            // SSH connection string to connect to the remote server (format: user@host-or-IP:port-number)
            ->server( getenv('CI_TARGET_HOST')) 
            // the absolute path of the remote server directory where the project is deployed
            ->deployDir( getenv('CI_TARGET_FOLDER') )
            // the URL of the Git repository where the project code is hosted
            ->repositoryUrl( getenv('CI_GIT_REPOSITORY_URL'))

            ->composerInstallFlags('--no-interaction --no-scripts --prefer-dist --no-dev --no-progress --no-suggest --quiet')

            ->keepReleases(2)

            //->writableDirs(['public/media'])

            //->useSshAgentForwarding(false)
            //->fixPermissionsWithChown('apache')
            //->sharedFilesAndDirs(['.env'])
            // the repository branch to deploy
            ->repositoryBranch($currentTag)
        ;
    }

    public function beforePreparing()
    {
        $this->log('<h3>Copying over the .env files</>');
        $this->runRemote(sprintf('cp {{ deploy_dir }}/repo/.env {{ project_dir }} 2>/dev/null'));
        $this->runRemote(sprintf('echo APP_ENV=prod > {{ project_dir }}/.env.local 2>/dev/null'));
        $this->runRemote('echo DATABASE_URL="'.getenv('CI_TARGET_DB_URL').'" >> {{ project_dir }}/.env.local 2>/dev/null');
        $envapp = getenv('TESTAPPENVS');
        //$pagopacrt = getenv('PAGOPA_CRT');
        $this->runRemote('echo "'.$envapp.'" | base64 -d > {{ project_dir }}/.env.local');
        //$this->runRemote('mkdir -p {{ project_dir }}/var/crt');
        //$this->runRemote('echo "'.$pagopacrt.'" | base64 -d > {{ project_dir }}/var/crt/pagopa.pem');
    }

    // run some local or remote commands before the deployment is started
    public function beforeStartingDeploy()
    {
        $remoteDeployDir = $this->getConfig(Option::deployDir);
        $this->runRemote(sprintf('sudo chown -R '.getenv('CI_TARGET_USER').':'.getenv('CI_TARGET_USER').' '.$remoteDeployDir.' 2&1>/dev/null'));
    }

    /*public function beforeStartingRollback()
    {
        $remoteDeployDir = $this->getConfig(Option::deployDir);
        $this->runRemote(sprintf('sudo chown -R po.xxx:po.xxx '.$remoteDeployDir.'/releases'));
    }*/

    public function beforeCancelingDeploy()
    {
        // An error happened during the deployment and remote servers are
        // going to be reverted to their original state. Here you can perform
        // clean ups or send notifications about the error.
        $remoteDeployDir = $this->getConfig(Option::deployDir);
        $this->runRemote(sprintf('sudo chown -R '.getenv('CI_TARGET_USER').':'.getenv('CI_TARGET_USER').' '.$remoteDeployDir));
    }

    /*
    public function beforeFinishingRollback()
    {
        $this->runRemote("APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`");
        $this->runRemote('setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX {{ project_dir }}/var/cache {{ project_dir }}/var/log');
        $this->runRemote('setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX {{ project_dir }}/var/cache {{ project_dir }}/var/log');
        $this->runRemote('sudo chown -R apache:apache {{ project_dir }}');
    }
    */
    // run some local or remote commands after the deployment is finished
    public function beforeFinishingDeploy()
    {
        //$this->runRemote('{{ console_bin }} cache:clear');
        $this->runRemote('ln -s {{ project_dir }}/public {{ project_dir }}/public/divo-test');
        $this->runRemote("APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`");
        $this->runRemote('setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX {{ project_dir }}/var/cache {{ project_dir }}/var/log');
        $this->runRemote('setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX {{ project_dir }}/var/cache {{ project_dir }}/var/log');
        $this->runRemote('sudo chown -R apache:apache {{ project_dir }}');
        //$this->runRemote('{{ console_bin }} bicorebundle:install admin admin admin@admin.it');
        //$this->runLocal('say "The deployment has finished."');
    }
};
