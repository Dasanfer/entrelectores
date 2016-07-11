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

class GenerateSitemapCommand extends ContainerAwareCommand
{
    private $out;
    private $fileName;
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectores:seo:generatesitemap')
            ->setDescription('querys urls is csv')
            ->addArgument(
                'host',
                InputArgument::REQUIRED,
                'host to prepend'
            )->addArgument(
                'fileName',
                InputArgument::REQUIRED,
                'prepend name for files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $this->fileName = $input->getArgument('fileName');
        $host = $input->getArgument('host');
        $this->out = $output;

        $sitemap = new Sitemap($host);
        $sitemap->setFilename($this->fileName);
        $this->generateArbitraryUrl($sitemap);
        $this->generateGenreSitemap($sitemap);
        $sitemap->createSitemapIndex($host.'/sitemap/', 'Today');

        $sitemap = new Sitemap($host);
        $sitemap->setFilename($this->fileName.'-libros');
        $this->generateBookSitemap($sitemap);
        $sitemap->createSitemapIndex($host.'/sitemap/', 'Today');

        $sitemap = new Sitemap($host);
        $sitemap->setFilename($this->fileName.'-autores');
        $this->generateAuthorSitemap($sitemap);
        $sitemap->createSitemapIndex($host.'/sitemap/', 'Today');
    }

    private function generateArbitraryUrl($sitemap){
        $router = $this->getContainer()->get('router');
        $sitemap->addItem($router->generate('all_genre_page'));
        $sitemap->addItem($router->generate('homepage'));
        $sitemap->addItem($router->generate('public_interview_list'));
        $sitemap->addItem($router->generate('public_blog_post_list'));

    }

    private function generateBookSitemap($sitemap){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle\Entity\Book','b')->join('b.author','a');
        $offset = 0;
        $results = 100;
        $books = $qb->getQuery()->iterate();
        $count = 0;
        foreach($books as $row){
            $book = $row[0];
            $url = $this->getContainer()->get('router')
                ->generate('bookpage',
                    array(
                        'authorslug' => $book->getAuthor()->getSlug(),
                        'slug' => $book->getSlug()
                    )
                );
            $sitemap->addItem($url,'0.8');
            $count = $count + 1;

            if($count % 1000 == 0){
                $this->out->writeln('Books: '.$count);
            }
        }
    }

    private function generateAuthorSitemap($sitemap){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle\Entity\Author','b');
        $offset = 0;
        $results = 100;
        $authors = $qb->getQuery()->iterate();
        $count = 0;

        foreach($authors as $row){
            $author = $row[0];
            $url = $this->getContainer()->get('router')
                ->generate('authorpage',
                    array(
                        'slug' => $author->getSlug()
                    )
                );

            $sitemap->addItem($url,'0.7');
            $count = $count + 1;

            if($count % 1000 == 0){
                $this->out->writeln('Authors: '.$count);
            }
        }
    }

    private function generateGenreSitemap($sitemap){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle\Entity\Genre','b');
        $offset = 0;
        $results = 100;
        $genres = $qb->getQuery()->iterate();
        $count = 0;

        foreach($genres as $row){
            $genre = $row[0];
            $url = $this->getContainer()->get('router')
                ->generate('bookgenre',
                    array(
                        'slug' => $genre->getSlug()
                    )
                );

            $sitemap->addItem($url);
            $count = $count + 1;

            if($count % 10 == 0){
                $this->out->writeln('Genero: '.$count);
            }
        }
    }
}
