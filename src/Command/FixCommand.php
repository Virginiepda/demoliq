<?php

namespace App\Command;

use App\Entity\Question;
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

        $conn=$this->em->getConnection();
        //désactive la vérification des clés étrangères
        $conn->query('SET FOREIGN_KEY_CHECKS = 0');
        $conn->query('TRUNCATE question');
        $conn->query('TRUNCATE message');

        $conn->query('SET FOREIGN_KEY_CHECKS = 1');

        for($i=0; $i<100; $i++){
            $question = new Question();
            $question->setTitle($faker->sentence(6));
            $question->setDescription($faker->paragraph(15));
            $question->setStatus($faker->randomElement(['debating', 'voting', 'closed']));
            $question->setSupports($faker->optional(0.5, 0)->numberBetween(0, 9000));
            $question->setCreationDate($faker->dateTimeBetween('- 1 year', 'now') );

            $this->em->persist($question);
        }

        $this->em->flush();
        $io->success("goody, goody");


    }
}
