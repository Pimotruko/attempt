<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findByField($fieldName, $fieldValue)
    {
        $qb = $this->createQueryBuilder('s');

        if (empty($fieldValue)) {
            // If $fieldValue is empty, return all records without filtering
            $qb
                ->orderBy('s.id', 'ASC');
        }else{
            $qb->leftJoin('s.courses' , 'c');
            if ($fieldName === 'firstName') {
                $qb->Where($qb->expr()->like('s.firstName', ':fieldValue'));
            } elseif ($fieldName === 'lastName') {
                $qb->Where($qb->expr()->like('s.lastName', ':fieldValue'));
            } elseif ($fieldName === 'courses') {
                $qb->Where($qb->expr()->like('c.name', ':fieldValue'));
            }

            $qb->setParameter('fieldValue', $fieldValue . '%');
    
            // Order the results by student ID
            $qb->orderBy('s.id', 'ASC');
        }
        return $qb;
    }

//    /**
//     * @return Student[] Returns an array of Student objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
