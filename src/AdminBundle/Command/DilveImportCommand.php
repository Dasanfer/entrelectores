<?php

namespace AdminBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Command\SimpleImage;
use AppBundle\Entity\Book;
use AppBundle\Entity\Author;
use Gedmo\Sluggable\Util as Sluggable;
use AdminBundle\Command\PONIpar as PONIPar;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DilveImportCommand extends ContainerAwareCommand
{
    public $authorRepo;
    public $bookRepo;
    public $genreRepo;
    protected function configure()
    {
        $this
            ->setName('entrelectores:admin:dilveimport')
            ->setDescription('imports dilve xml file')
            ->addArgument(
                'fileDir',
                InputArgument::REQUIRED,
                'dilve xml dir'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bookRepo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Book');
        $this->authorRepo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Author');
        $this->genreRepo = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Genre');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $file_to_read = $input->getArgument('fileDir');

        $parser = new PONIpar\Parser();
        $parser->useFile($file_to_read);
        $instance = $this;
        $newCount = 0;
        $existingCount = 0;
        $parser->setProductHandler(function ($product) use ($instance, $file_to_read, $output, &$newCount, &$existingCount)       {
            $isbn = $instance->retrieveIsbn($product);
            if($isbn === false)
                return;

            $title = $instance->safeGetData($product,'Title/TitleText');
            $imageDir = $instance->safeGetData($product,'MediaFile/MediaFileLink');
            $onixSubject = $instance->safeGetData($product,'MainSubject/SubjectCode');

            $genreSlug = $instance->getOnixCategory($onixSubject);

            $genre = $instance->genreRepo->findOneBySlug($genreSlug);
            $data = array(
                'titulo_original' => $title,
                'ano_publicacion' => substr($instance->safeGetData($product,'PublicationDate'),0,4),
                'post_content' => $instance->safeGetData($product,'OtherText/Text'),
                'isbn' => $isbn,
                'post_type' => 'libro',
                'post_title' => $title,
            );

            $author = $instance->getAuthors($product);
            $existing = $instance->getExisting($isbn,$title); ;

            if(count($existing) == 0){
                try {
                    $book = new Book();
                    $book->setIsbn($data['isbn']);
                    $book->setTitle($title);
                    $book->setOriginalTitle($title);
                    $book->setYear($data['ano_publicacion']);
                    $book->setSinopsis($data['post_content']);
                    $book->setAuthor($author);
                    $book->setGenre($genre);

                    if(strlen($imageDir) > 5){
                        try {
                            $instance->retrieveAndAddImage($imageDir,$book,$file_to_read);
                        } catch(\Exception $ex){
                            $output->writeLn('<error>ERROR '.$ex->getMessage()."</error>");
                        }
                    }

                } catch(FileException $ex){
                    $output->writeLn('<error>Image ex: '.$ex->getMessage().'</error>');
                }

                $instance->em->persist($book);
                $output->writeLn('New: '.$book->getTitle().' '.$isbn);
                $newCount = $newCount +1;

                if($newCount % 20 == 0){
                    $this->em->flush();
                    $this->em->clear();
                    $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
                    $cacheDriver->deleteAll();
                }

            } else {
                $existingCount = $existingCount +1;
                $output->writeLn('EXISTING: '.$title.' '.$isbn);
            }
        });

        $parser->parse();
        $this->em->flush();
        $output->writeLn('New:'.$newCount.' Existing'.$existingCount);
    }

    public function retrieveAndAddImage($imageDir, $book,$file_to_read){
        $tempDir = $this->getContainer()->getParameter('kernel.root_dir').'/cache/image_temp/';

        if(!file_exists($tempDir))
            mkdir($tempDir);

        print_r('Getting '.$imageDir);
        $newName = uniqid();

        if(!is_null($imageDir) && filter_var($imageDir, FILTER_VALIDATE_URL) !== false){
            $imageData = file_get_contents($imageDir);
            if($imageData !== false){
                print_r("IMAGE Downloaded ".$imageDir."\n");
                $imageDir = $tempDir.$newName;
                if(file_put_contents($imageDir, $imageData) === false){
                    $imageDir = null;
                } else {
                    print_r("IMAGE Stored ".$imageDir."\n");
                }
            } else {
                print_r("IMAGE Download ERROR ".$imageDir."\n");
                $imageDir = null;
            }
        } else if(!is_null($imageDir)){
            $imageOrigin = dirname($file_to_read).'/'.$imageDir;
            if(!file_exists($imageOrigin)){
                print_r('IMAGE Invalid '.$imageDir."\n");
                $imageDir = null;
            }
            else {
                $imageDir = $tempDir.$newName;
                $result = copy($imageOrigin,$imageDir);
                print_r('IMAGE Copied '.$imageOrigin." ".$imageDir." \n");
                if($result === false || !file_exists($imageDir))
                    $imageDir = null;
            }
        }

        if(!is_null($imageDir)){
            $uploadedFile = new UploadedFile($imageDir,$newName,null,null,null,true);
            $book->setFile($uploadedFile);
        }
    }


    public function retrieveIsbn($product){
        $isbn = false;
        try {
            $isbn = $product->getIdentifier(
                PONIpar\ProductIdentifierProductSubitem::TYPE_GTIN13
            );

        } catch(\Exception $ex){
            print_r($ex->getMessage()."\n");
        }

        try {
            if(!$isbn){
                $isbn = $product->getIdentifier(
                    PONIpar\ProductIdentifierProductSubitem::TYPE_ISBN13
                );
            }
        } catch(\Exception $ex){
            print_r($ex->getMessage()."\n");
        }

        try {
            if(!$isbn){
                $isbn = $product->getIdentifier(
                    PONIpar\ProductIdentifierProductSubitem::TYPE_ISBN10
                );
            }
        } catch(\Exception $ex){
            print_r($ex->getMessage()."\n");
        }

        return $isbn;
    }


    public function safeGetData($product,$path){
        $nodes = $product->get($path);
        if(count($nodes) > 0)
            return $nodes[0]->nodeValue;
        else
            return null;
    }

    public function getExisting($isbn,$title){
        $slug = Sluggable\Urlizer::urlize($title, '-');
        $byIsbn = $this->bookRepo->findByIsbn($isbn);
        $byTitle = $this->bookRepo->findBySlug($slug);
        return array_merge($byIsbn,$byTitle);
    }

    public function getAuthors($product){
        $author = null;
        $nodes = $product->get('Contributor');
        foreach($nodes as $node){
            $role = $node->getElementsByTagName('ContributorRole')->item(0)->nodeValue;
            if($role == 'A01'){
                $name = false;
                $names = $node->getElementsByTagName('PersonNameInverted');
                if($names->length > 0){
                    $name =  implode(' ',array_reverse(explode(',',$names->item(0)->nodeValue)));
                }
                else {
                    $names = $node->getElementsByTagName('CorporateName');
                    if($names->length > 0){
                        $name =  $names->item(0)->nodeValue;
                    }
                }

                if($name){
                    $author = $this->authorRepo->findOneBySlug(Sluggable\Urlizer::urlize($name, '-'));
                    if(is_null($author)){
                        $author = new Author();
                        $author->setName($name);
                        $this->em->persist($author);
                    }
                }
            }
        }

        return $author;
    }

    public function getOnixCategory($onixCat){

        $cats = array(
            "A"=> "artes",
            "B"=> "biografias-y-memorias",
            "C"=> "educacion-e-idiomas",
            "D"=> "educacion-e-idiomas",
            "E"=> "educacion-e-idiomas",
            "F"=> "literatura",
            "G"=> "educacion-e-idiomas",
            "H"=> "humanidades-y-ciencias-sociales",
            "J"=> "Humanidades-y-ciencias-sociales",
            "K"=> "educacion-e-idiomas",
            "L"=> "educacion-e-idiomas",
            "M"=> "educacion-e-idiomas",
            "P"=> "educacion-e-idiomas",
            "R"=> "educacion-e-idiomas",
            "T"=> "educacion-e-idiomas",
            "U"=> "informatica-y-tecnologia",
            "V"=> "viajes-ocio-gastronomia-y-deportes",
            "W"=> "viajes-ocio-gastronomia-y-deportes",
            "Y"=> "infantil-y-juvenil",
            "AB"=> "artes",
            "AC"=> "artes",
            "AF"=> "artes",
            "AG"=> "artes",
            "AJ"=> "artes",
            "AK"=> "artes",
            "AM"=> "artes",
            "AN"=> "teatro",
            "AP"=> "viajes-ocio-gastronomia-y-deportes",
            "AS"=> "artes",
            "AV"=> "artes",
            "BG"=> "biografias-y-memorias",
            "BJ"=> "biografias-y-memorias",
            "BK"=> "biografias-y-memorias",
            "BM"=> "biografias-y-memorias",
            "BT"=> "no-ficcion",
            "CB"=> "educacion-e-idiomas",
            "CF"=> "educacion-e-idiomas",
            "CJ"=> "educacion-e-idiomas",
            "DB"=> "teatro-literatura",
            "DC"=> "poesia",
            "DD"=> "teatro-literatura",
            "DN"=> "no-ficcion",
            "DQ"=> "pioesia-y-antologias",
            "DS"=> "teatro-literatura",
            "EB"=> "educacion-e-idiomas",
            "EL"=> "educacion-e-idiomas",
            "ES"=> "educacion-e-idiomas",
            "FA"=> "literatura",
            "FC"=> "literatura",
            "FF"=> "negra-policiaca-e-intriga",
            "FH"=> "negra-policiaca-e-intriga",
            "FJ"=> "aventuras-literatura",
            "FK"=> "terror-y-misterio",
            "FL"=> "ciencia-ficcion-literatura",
            "FM"=> "fantasia-literatura",
            "FP"=> "erotica-literatura",
            "FQ"=> "literatura",
            "FR"=> "romantica-literatura",
            "GL"=> "artes",
            "HB"=> "historica-y-belica",
            "UB"=> "informatica-y-tecnologia",
            "UD"=> "informatica-y-tecnologia",
            "UK"=> "informatica-y-tecnologia",
            "UL"=> "informatica-y-tecnologia",
            "UM"=> "informatica-y-tecnologia",
            "UN"=> "informatica-y-tecnologia",
            "UQ"=> "informatica-y-tecnologia",
            "UR"=> "informatica-y-tecnologia",
            "UT"=> "informatica-y-tecnologia",
            "UY"=> "informatica-y-tecnologia",
            "YQ"=> "material-didactico-o-de-consulta");

        if(is_null($onixCat))
            return null;

        if(!array_key_exists($onixCat,$cats)){
            if(strlen($onixCat) > 1)
                return $this->getOnixCategory(substr($onixCat,0,strlen($onixCat)-1));
            else
                return null;
        } else
            return $cats[$onixCat];

    }
}
