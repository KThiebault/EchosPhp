<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Book>
 */
final class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Paginator<array-key, Book>
     */
    public function findPaginatedBooksForAuthor(int $firstResult, int $maxResult, User $author): Paginator
    {
        return new Paginator(
            $this->createQueryBuilder('b')
                ->where('b.author = :author')
                ->setParameter('author', $author)
                ->orderBy('b.createdAt', 'DESC')
                ->setFirstResult($firstResult)
                ->setMaxResults($maxResult)
                ->getQuery()
        );
    }
}
