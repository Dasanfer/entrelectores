<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSeoUrlCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectores:seo:checkurls')
            ->setDescription('querys urls is csv')
            ->addArgument(
                'fileDir',
                InputArgument::REQUIRED,
                'csv with the urls'
            )->addArgument(
                'host',
                InputArgument::REQUIRED,
                'host to query'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('guzzle.client');
        $host = $input->getArgument('host');
        $client->setBaseUrl($input->getArgument('host'));

        $csvFile = fopen($input->getArgument('fileDir'),'r');
        $line = fgetcsv($csvFile,0,';');
        $statusCount = array('error' => 0, 'exception' => 0);
        $errors = array();
        while($line){
            try {
                $objUrl = parse_url($line[0]);
                $request = $client->get(
                    $host.$objUrl['path']
                );
                $response = $request->send();
                $status = $response->getStatusCode();

                if(!array_key_exists($status,$statusCount))
                    $statusCount[$status] = 1;
                else
                    $statusCount[$status] = $statusCount[$status] + 1;

            } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e){
                $statusCount['error'] = $statusCount['error'] + 1;
                $errors[$e->getRequest()->getUrl()] = $e->getResponse()->getStatusCode();
            } catch(\Exception $e){
                $statusCount['exception'] = $statusCount['exception'] + 1;
            }
            $line = fgetcsv($csvFile,0,';');
        }

        foreach($statusCount as $num => $status)
            $output->writeln('<info>'.$num.':'.$status.'</info>');

        foreach($errors as $url => $status)
            $output->writeln('<error>'.$url.' ----> '.$status.'</error>');

    }
}
