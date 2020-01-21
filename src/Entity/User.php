<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ApiResource(
 *     itemOperations={
 *         "auth"={
 *             "route_name"="api_auth",
 *             "method" = "post",
 *             "normalization_context"={
 *                 "groups"={"auth"},
 *                 "swagger_definition_name": "Auth"
 *             },
 *             "openapi_context" = {
 *                 "example_value"="one",
 *                 "enum" = {"one", "two"},
 *                 "parameters" = {
 *                      {
 *                          "type": "object",
 *                          "name" = "credentials",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "email" = {"type" = "string"},
 *                                  "password" = {"type" = "string"},
 *                              }
 *                          },
 *                          "in" = "body",
 *                          "required" = "true",
 *                          "description" = "User's credentials"
 *                      },
 *                  },
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "Successful login attempt, returning a new token",
 *                          "content" = {
 *                              "application/json" = {
 *                                  "schema" = {
 *                                      "type" = "object",
 *                                      "properties" = {
 *                                          "token" = {"type" = "string"},
 *                                      }
 *                                  },
 *                                  "example" = {
 *                                      "token" = "eyJ0eXAiOiJKV1QiLCJhbGciOiF33iJ0904N8f7IX9Pd0",
 *                                  },
 *                              }
 *                          }
 *                      }
 *                  },
 *                  "summary" = "Performs a login attempt, returning a valid JWT token on success",
 *                  "requestBody" = {
 *                      "content" = {
 *                          "application/json" = {
 *                              "schema" = {
 *                                  "type" = "object",
 *                                  "properties" = {
 *                                      "email" = {"type" = "string"},
 *                                      "password" = {"type" = "string"},
 *                                  }
 *                              },
 *                              "example" = {
 *                                  "email" = "j.k@rowling.com",
 *                                  "password" = "1111111"
 *                              }
 *                          },
 *                      },
 *                  },
 *             },
 *
 *         }
 *     },
 *     collectionOperations={}
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorPseudonym;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="author")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAuthorPseudonym(): ?string
    {
        return $this->authorPseudonym;
    }

    public function setAuthorPseudonym(string $authorPseudonym): self
    {
        $this->authorPseudonym = $authorPseudonym;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
