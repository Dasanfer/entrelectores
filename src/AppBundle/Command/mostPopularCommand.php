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

class mostPopularCommand extends ContainerAwareCommand
{

    /**
     * @see Command
     */

    protected function configure()
    {
        $this
            ->setName('entrelectores:mostPopular')
            ->setDescription('get most popular book command');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
    	$ids = array();
    	$ids2 = array();
    	$ids3 = array();
    	$titles = array();

    	//Mas votados
    	$doctrine = $this->getContainer()->get('doctrine');
    	$qb = $doctrine->getManager()->createQueryBuilder();
    	$qb->select('v.id')->from('AppBundle\Entity\Book', 'v')
    	   ->orderBy('v.ratingCount', 'DESC')
    	   ->setMaxResults('5000');

    	$result = $qb->getQuery()->getResult();

    	foreach ($result as $single) {
    		array_push($ids, $single['id']);
    	}

    	//Mas resenas
    	$qb2 = $doctrine->getManager()->createQueryBuilder();
    	$qb2->select('r.id')->from('AppBundle\Entity\Book', 'r')
    	     ->orderBy('r.reviewCount', 'DESC')
    	     ->setMaxResults('5000');

    	$result2 = $qb2->getQuery()->getResult();

    	foreach ($result2 as $single) {
    		array_push($ids2, $single['id']);
    	}

    	$equals = array_intersect($ids, $ids2);

    	//Mas de 100 votos y mejor puntuacion media
    	$qb3 = $doctrine->getManager()->createQueryBuilder();
    	$qb3->select('c.id')->from('AppBundle\Entity\Book', 'c')
    		->where('c.ratingCount > 100')
    	    ->orderBy('c.cachedRate', 'DESC')
    	    ->setMaxResults('1000');

    	$result3 = $qb3->getQuery()->getResult();

    	foreach ($result3 as $single) {
    		array_push($ids3, $single['id']);
    	}

    	$finalIds = array_intersect($equals, $ids3);

    	//Titulos que coinciden en los 3 anteriores
    	$qb4 = $doctrine->getManager()->createQueryBuilder();
    	$qb4->select('t.title')->from('AppBundle\Entity\Book', 't')
    		->where($qb->expr()->in('t.id', $finalIds));

    	$final = $qb4->getQuery()->getResult();

    	foreach ($final as $single) {
    		array_push($titles, $single['title']);
    	}

    	foreach ($titles as $single) {
    		$output->write("'");
    		$output->write($single);
    		$output->write("';");
    		$output->write("\n");
    	}
    	$output->write("\n");
    }

}

?>