<?php

namespace App\Repository;

use App\Entity\SchoolYear;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return Tag[] Returns an array of Tag objects
     */
    public function findByNotNullDescription(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.description IS NOT null')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Tag[] Returns an array of Tag objects
     */
    public function findByNullDescription(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.description IS null')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * This method finds all tags which contain a given keyword in the name or the 
     * description
     * @param string $keyword The keyword to search for
     * @return Tag[] Returns an array of Tag objects
     */
    public function findByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :keyword')
            ->orWhere('t.description LIKE :keyword')
            ->setParameter('keyword', "%$keyword%")
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * This method finds tags based on a school year, by making inner joins with 
     * students and with tags.
     * @param SchoolYear $schoolYear The school year for which we want to find the tags
     * @return Tag[] Returns an array of Tag objects
     */
    public function findBySchoolYear(SchoolYear $schoolYear): array
    {
        // Le query builder ci-dessous génère le code SQL suivant :
        // SELECT tag.id, tag.name, tag.description
        // FROM `tag`
        // INNER JOIN student_tag ON tag.id = student_tag.tag_id
        // INNER JOIN student ON student.id = student_tag.student_id
        // INNER JOIN school_year ON school_year.id = student.school_year_id
        // WHERE school_year.id = 123
        // GROUP BY tag.id, tag.name, tag.description
        // ORDER BY tag.name;

        return $this->createQueryBuilder('t')
            ->innerJoin('t.students', 'stud')
            ->innerJoin('stud.schoolYear', 'sy')
            ->andWhere('sy = :schoolYear')
            ->setParameter('schoolYear', $schoolYear)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Tag[] Returns an array of Tag objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tag
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
