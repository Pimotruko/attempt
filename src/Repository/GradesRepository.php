<?php

namespace App\Repository;

use App\Entity\Grades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grades>
 *
 * @method Grades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grades[]    findAll()
 * @method Grades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grades::class);
    }

    public function findByField($fieldName, $fieldValue)
    {
        $qb = $this->createQueryBuilder('g');

        if (empty($fieldValue)) {
            // If $fieldValue is empty, return all records without filtering
            $qb
                ->orderBy('g.id', 'ASC');
        }else{
            $qb->leftJoin('g.course' , 'c');
            $qb->leftJoin('g.student' , 's');
            if ($fieldName === 'student.firstName') {
                $qb->Where($qb->expr()->like('s.firstName', ':fieldValue'));
            } elseif ($fieldName === 'student.lastName') {
                $qb->Where($qb->expr()->like('s.lastName', ':fieldValue'));
            } elseif ($fieldName === 'course') {
                $qb->Where($qb->expr()->like('c.name', ':fieldValue'));
            }

            $qb->setParameter('fieldValue', $fieldValue . '%');

            $qb->orderBy('g.id', 'ASC');
        }
        return $qb;
    }

//    /**
//     * @return Grades[] Returns an array of Grades objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Grades
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
