<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
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
 *         "delete"={"security"="is_granted('BOOK_DELETE', object)"},
 *         "patch"={"security"="is_granted('BOOK_EDIT', object)"}
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('BOOK_ADD')"}
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
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      allowEmptyString = false,
     * )
     * @Assert\NotNull()
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     * @Assert\Length(
     *      min = 1,
     *      max = 2000,
     *      allowEmptyString = false,
     * )
     * @Assert\NotNull()
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"read", "write"})
     * @Assert\Type(type="numeric")
     * @Assert\GreaterThan(value = 0)
     * @Assert\LessThan(value = 9999999999)
     * @Assert\NotNull()
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
