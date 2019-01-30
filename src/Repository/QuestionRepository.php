<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

//option d'une requête CUSTOM avec le dql
    public function findListQuestions()
    {

        //on va faire un peu comme avec SQL
        //il y a de la doc sur dql dans Doctrine
        //je ne fais pas un SELECT FROM de ma table, mais de ma CLASSE
        $dql = "SELECT q , s
                FROM App\Entity\Question q
                JOIN q.subjects s
                WHERE q.status = 'debating'
                ORDER BY q.dateCreated DESC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query ->setMaxResults(200);
        $questions = $query->getResult();

        return $questions;
    }


//option d'une requête CUSTOM avec le QUERYBUILDER

    public function findListQuestionsQB()
    {
        //il est relié explicitement à QUestion puisqu'on est dans le QuestionRepository.
        //on lui passe directement l'alias en argument
        $qb = $this->createQueryBuilder('q');

        $qb ->andWhere('q.status = :status')
            ->orderBy('q.dateCreated', 'DESC')
            ->join('q.subjects', 's')
            ->addSelect('s')
            ->setParameter(':status', 'debating') //je précise à quoi correspond le :status, comme en php pur
            ->setFirstResult(0)
            ->setMaxResults(200);


        $query = $qb->getQuery();
        $questions = $query->getResult();

        return $questions;
    }


    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
