<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OAuthClientCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('oauth-server:client-create')
            ->setDescription('Create a new client')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getApplication()->getKernel()->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://localhost:8888/OC/Projet7/bilemo/web/app.php'));
        $client->setAllowedGrantTypes(array('password', 'refresh_token'));
        $clientManager->updateClient($client);
        $output->writeln(sprintf('Added a new client with public id <info>%s</info>.', $client->getPublicId()));
    }
}