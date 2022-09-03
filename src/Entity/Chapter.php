<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $uuid;

    #[ManyToOne(targetEntity: Book::class)]
    #[JoinColumn(referencedColumnName: 'uuid', nullable: false, onDelete: 'cascade')]
    private Book $book;

    /**
     * @var Collection<int, Page>
     */
    #[OneToMany(mappedBy: 'chapter', targetEntity: Page::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $pages;

    #[Assert\NotBlank]
    #[Column(type: Types::STRING, nullable: false)]
    private string $title;

    #[Column(type: Types::DATE_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * @param Collection<int, Page> $pages
     */
    public function setPages(Collection $pages): void
    {
        $this->pages = $pages;
    }

    public function addPage(Page $page): void
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setChapter($this);
        }
    }

    public function removePage(Page $page): void
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
