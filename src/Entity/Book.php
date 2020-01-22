<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     normalizationContext={
 *         "groups"={"read"}
 *     },
 *     denormalizationContext={
 *         "groups"={"write"}
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('BOOK_EDIT', previous_object)"},
 *         "delete"={"security"="is_granted('ROLE_USER') and object.author == user"},
 *         "patch"={"security"="is_granted('ROLE_USER') and object.author == user"}
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_USER')","denormalization_context"={
 *                 "groups"={"write"},
 *             },}
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "title": "partial",
 *     "description": "partial",
 *     "author.authorPseudonym": "partial"
 * })
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"read", "write"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var Media|null
     *
     * @ORM\ManyToOne(targetEntity=Media::class)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"write"})
     */
    private $coverImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCoverImage(): ?Media
    {
        return $this->coverImage;
    }

    public function setCoverImage(?Media $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @Groups({"read"})
     * @SerializedName("author")
     */
    public function getAuthorName(): string
    {
        $author = $this->getAuthor();

        return $author ? $author->getAuthorPseudonym() : '';
    }

    /**
     * @Groups({"read"})
     * @SerializedName("coverImage")
     */
    public function getCoverImageUrl(): string
    {
        $image = $this->getCoverImage();

        return $image ? $image->getContentUrl() : '';
    }
}
