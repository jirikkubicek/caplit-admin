<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity('username', "Uživatel s tímto uživatelským jménem již existuje")]
class Users implements PasswordAuthenticatedUserInterface, UserInterface, \Stringable, CRMEntityInterface
{
    /**
     * @var integer|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private string $username;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $password;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 100)]
    #[Assert\Email]
    #[Assert\NotNull]
    private ?string $email = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotNull]
    private ?string $name = null;

    /**
     * @var boolean|null
     */
    #[ORM\Column(name: "admin", nullable: true)]
    private ?bool $isAdmin = null;

    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function resetId(): self
    {
        $this->id = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array<int,string>
     */
    public function getRoles(): array
    {
        return $this->isAdmin() === true ? ["ROLE_ADMIN"] : ["ROLE_USER"];
    }

    /**
     * @return void
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @return boolean|null
     */
    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    /**
     * @param boolean|null $isAdmin
     * @return self
     */
    public function setAdmin(?bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
