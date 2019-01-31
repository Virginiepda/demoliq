<?php

namespace App\Command;

use App\Entity\Question;
use App\Entity\Subject;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCommand extends Command
{
    protected static $defaultName = 'app:fix';
    protected $em = null;

    public function __construct(EntityManagerInterface $em, ?string $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('mettre des fausses données dans la base de données');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        //on va créer une question ici, que l'on va mettre dans une boucle, afin de pouvoir remplir la base de données
        //mais on duplique toujours les mêmes données, du coup , on va utiliser la librairie faker

        $faker = \Faker\Factory::create();

        $io->text("truncating all tables....");

        $conn=$this->em->getConnection();
        //désactive la vérification des clés étrangères
        $conn->query('SET FOREIGN_KEY_CHECKS = 0');
        $conn->query('TRUNCATE question');
        $conn->query('TRUNCATE message');
        $conn->query('TRUNCATE subject');
        $conn->query('TRUNCATE question_subject');

        $conn->query('SET FOREIGN_KEY_CHECKS = 1');

        $subjects = [
            "Affaires étrangères",
            "Affaires européennes",
            "Agriculture, alimentation, pêche",
            "Ruralité",
            "Aménagement du territoire",
            "Économie et finance",
            "Culture",
            "Communication",
            "Défense",
            "Écologie et développement durable",
            "Transports",
            "Logement",
            "Éducation",
            "Intérieur",
            "Outre-mer et collectivités territoriales",
            "Immigration",
            "Justice et Libertés",
            "Travail",
            "Santé",
            "Démocratie"
        ];

        $io->text("now loading all fixtures...");

        //on va y mettre nos objets subject pour pouvoir les utiliser
        $subjectsArray = [];

        foreach ($subjects as $name){
            $subject = new Subject();
            $subject->setName($name);
            $this->em->persist($subject);

            $subjectsArray[]= $subject;
        }

        for($i=0; $i<100; $i++){
            $question = new Question();
            $question->setTitle($faker->sentence(6));
            $question->setDescription($faker->paragraph(15));
            $question->setStatus($faker->randomElement(['debating', 'voting', 'closed']));
            $question->setSupports($faker->optional(0.5, 0)->numberBetween(0, 9000));
            $question->setCreationDate($faker->dateTimeBetween('- 1 year', 'now') );

            //ajoute entre 1 à 3 sujets
            $num = mt_rand(1,3);
            for($b=0; $b < $num; $b++){
                $s = $faker->randomElement($subjectsArray);
                $question->addSubject($s);
            }


            //ajoute des messages sur les questions
            $messageNumber = mt_rand(0,20);
            for ($m = 0; $m < $messageNumber; $m++){
                $message = new Message();
                $message->setQuestion($question);
                $message->setClaps($faker->optional(0.5, 0)->numberBetween(0,5000));
                $message->setDateCreated($faker->dateTimeBetween($question->getCreationDate()));
                $message->setIsPublished($faker->boolean(95));
                $message->setContent($faker->paragraphs($nb = mt_rand(1,3), $asText = true));
                $this->em->persist($message);
            }


            $this->em->persist($question);
        }


        $this->em->flush();
        $io->success("goody, goody");


    }
}
