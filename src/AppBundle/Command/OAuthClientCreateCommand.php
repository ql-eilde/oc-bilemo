<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OAuthClientCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth-server:client-create')
            ->setDescription('Create a new client')
            ->addArgument('email', InputArgument::REQUIRED, 'Your email address.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clientManager = $this->getApplication()->getKernel()->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array($this->getContainer()->get('kernel')->getRootDir()));
        $client->setAllowedGrantTypes(array('password', 'refresh_token'));
        $clientManager->updateClient($client);
        $message = \Swift_Message::newInstance()
            ->setSubject('Your Bilemo API credentials')
            ->setFrom('noreply@quentinleilde.com')
            ->setTo($input->getArgument('email'))
            ->setBody(
                $this->getContainer()->get('templating')->render(
                    'Emails/new-oauth-client.html.twig',
                    array(
                        'client_id' => $client->getPublicId(),
                        'client_secret' => $client->getSecret(),
                    )
                ),
                'text/html'
            )
        ;
        $this->getContainer()->get('mailer')->send($message);
        $output->writeln('Congrats ! You\'ve been emailed your API credentials.');
    }
}
