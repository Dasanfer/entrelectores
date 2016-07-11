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
use AppBundle\Command\Sitemap;

class CompareBooksCommand extends ContainerAwareCommand
{

    /**
     * @see Command
     */

    protected function configure()
    {
        $this
            ->setName('entrelectores:compareBooks')
            ->setDescription('compare csv book with our database book command')
            ->addArgument(
                'fileDir',
                InputArgument::REQUIRED,
                'csv with the books'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $csvFile = fopen($input->getArgument('fileDir'),'r');
        $outCsv = fopen('output-book-check.csv', 'w');
        $line = fgetcsv($csvFile,0,';');
        $existing = 0;
        $new = 0;
        while($line){

            // $EAN = '9788490661765';

            // $doctrine = $this->getContainer()->get('doctrine');
            // $qb = $doctrine->getManager()->createQueryBuilder();
            // $qb ->select('b.title')->from('AppBundle\Entity\Book', 'b')
            //     ->where("b.isbn = '$EAN'");

            // $result = $qb->getQuery()->getResult();
            // $output->writeln($result[0]);


            foreach ($line as $single) {
                $EAN = substr($single, 0, 14);
                if(is_numeric($EAN)){
                    $EAN = str_replace("\r", '', $EAN);
                    $doctrine = $this->getContainer()->get('doctrine');
                    $qb = $doctrine->getManager()->createQueryBuilder();
                    $qb ->select('b.title')->from('AppBundle\Entity\Book', 'b')
                        ->where($qb->expr()->eq('b.isbn',':isbn'))
                        ->setMaxResults(1);

                    $qb ->setParameter('isbn',$EAN);

                    $result = $qb->getQuery()->getResult();
                    if(count($result) == 0){
                        fputcsv($outCsv,$line);
                        $new++;
                    } else {
                        $existing++;
                    }
                }

            }

            $line = fgetcsv($csvFile,0,';');
        }

        fclose($outCsv);

        $output->writeln('Existing:'.$existing);
        $output->writeln('New:'.$new);
    }

}

?>
