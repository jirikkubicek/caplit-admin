<?php

namespace App\Repository;

use App\Entity\TextSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TextSection>
 *
 * @method TextSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextSection[]    findAll()
 * @method TextSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextSectionRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextSection::class);
    }

    /**
     * @param TextSection $entity
     * @param boolean $flush
     * @return void
     */
    public function save(TextSection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param TextSection $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(TextSection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
