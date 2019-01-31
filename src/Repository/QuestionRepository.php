<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
        $dql = "SELECT q , s, m
                FROM App\Entity\Question q
                JOIN q.subjects s
                LEFT JOIN q.messages m
                WHERE q.status = 'debating'
                ORDER BY q.creationDate DESC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query ->setMaxResults(200);
        $query ->setFirstResult(0);

        $paginator = new Paginator($query);

        return $paginator;
    }


//option d'une requête CUSTOM avec le QUERYBUILDER

    public function findListQuestionsQB()
    {
        //il est relié explicitement à QUestion puisqu'on est dans le QuestionRepository.
        //on lui passe directement l'alias en argument
        $qb = $this->createQueryBuilder('q');

        $qb ->andWhere('q.status = :status')
            ->orderBy('q.creationDate', 'DESC')
            ->join('q.subjects', 's')
            ->leftJoin('q.messages', 'm')
            ->addSelect('s')
            ->setParameter(':status', 'debating') //je précise à quoi correspond le :status, comme en php pur
            ->setFirstResult(0)
            ->setMaxResults(200);


        $query = $qb->getQuery();
        $paginator = new Paginator($query);

        return $paginator;
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


    public function findClosedQuestions()
    {
        $qb = $this->createQueryBuilder('q');

        $qb ->andWhere('q.status = :status')
            ->orderBy('q.creationDate', 'DESC')
            ->join('q.subjects', 's')
            ->leftJoin('q.messages', 'm')
            ->addSelect('s')
            ->setParameter(':status', 'closed') //je précise à quoi correspond le :status, comme en php pur
            ->setFirstResult(0)
            ->setMaxResults(200);

        $query = $qb->getQuery();
        $paginator = new Paginator($query);

        return $paginator;
    }
}
