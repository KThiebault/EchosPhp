<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use App\Type\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Tag>
 */
final class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findPublished(string $uuid): ?Tag
    {
        return $this->findPublishedQuery($uuid)->getOneOrNullResult();
    }

    /**
     * @return array<array-key, Tag>
     */
    public function findAllPublished(): array
    {
        return $this->findPublishedQuery()->getResult();
    }

    private function findPublishedQuery(?string $uuid = null): Query
    {
        $queryBuilder = $this->createQueryBuilder('t');

        if (null !== $uuid) {
            $queryBuilder
                ->where('t.uuid = :uuid')
                ->setParameter('uuid', $uuid);
        }

        return $queryBuilder
            ->join('t.books', 'b')
            ->addSelect('b')
            ->andWhere('b.state = :state')
            ->setParameter('state', State::Published)
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery();
    }
}
