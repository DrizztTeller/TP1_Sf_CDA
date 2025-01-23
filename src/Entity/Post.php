<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, nullable: false)]
    #[Regex(
        pattern: '/^[a-zA-Z0-9_\s\-éèêëàâäîïôöùûüçñÑ&µ@$£€*%!?,;:\'".^°()#+\/]{2,80}$/',
        message: 'This field can only contain letters, numbers, underscores, hyphens and a few symbols : &, µ, @, $, £, €, *, %, !, ?, ;, :, \', ", ^, °, (, ), +, /, . and #'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Regex(
        pattern: '/^[a-zA-Z0-9_\s\-éèêëàâäîïôöùûüçñÑ&µ@$£€*%!?,;:\'".^°()#+\/]{2,}$/',
        message: 'This field can only contain letters, numbers, underscores, hyphens and a few symbols : &, µ, @, $, £, €, *, %, !, ?, ;, :, \', ", ^, °, (, ), +, /, . and #'
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ["default" => false])]
    #[Type('bool')]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
} // Do not write anything after this line
