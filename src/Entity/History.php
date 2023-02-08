<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: HistoryRepository::class)]
#[Table(uniqueConstraints: [new UniqueConstraint(columns: ['user', 'book', 'chapter'])])]
class History
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $uuid;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(referencedColumnName: 'uuid', nullable: false, onDelete: 'cascade')]
    private User $user;

    #[ManyToOne(targetEntity: Book::class)]
    #[JoinColumn(referencedColumnName: 'uuid', nullable: false, onDelete: 'cascade')]
    private Book $book;

    #[ManyToOne(targetEntity: Chapter::class)]
    #[JoinColumn(referencedColumnName: 'uuid', nullable: false, onDelete: 'cascade')]
    private Chapter $chapter;

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    public function getChapter(): Chapter
    {
        return $this->chapter;
    }

    public function setChapter(Chapter $chapter): void
    {
        $this->chapter = $chapter;
    }
}
