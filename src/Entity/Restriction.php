<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Interface RestrictionInterface
 * @package App\Entity

 * @ORM\Table(name="restriction")
 * @ORM\Entity(repositoryClass="App\Repository\RestrictionRepository")
 */
class Restriction implements RestrictionInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\Type(type="string")
     */
    protected string $code;

    /**
     * @ORM\Column(name="effective_on", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected DateTime $effectiveOn;

    /**
     * @ORM\Column(name="issuer", type="string", length=50)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     */
    protected string $issuer;

    /**
     * @ORM\Column(name="card_set", type="string", length=20)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     */
    protected string $cardSet;

    /**
     * @ORM\Column(name="contents", type="json")
     *
     * @Assert\Type(type="array")
     */
    protected array $contents;

    /**
     * @ORM\Column(name="active", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    protected bool $active;

    /**
     * @ORM\Column(name="version", type="string", length=20)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     */
    protected string $version;

    /**
     * @inheritdoc
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function setEffectiveOn(DateTime $effectiveOn): void
    {
        $this->effectiveOn = $effectiveOn;
    }
    /**
     * @inheritdoc
     */
    public function getEffectiveOn(): DateTime
    {
        return $this->effectiveOn;
    }

    /**
     * @inheritdoc
     */
    public function setIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    /**
     * @inheritdoc
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @inheritdoc
     */
    public function setCardSet(string $cardSet): void
    {
        $this->cardSet = $cardSet;
    }

    /**
     * @inheritdoc
     */
    public function getCardSet(): string
    {
        return $this->cardSet;
    }

    /**
     * @inheritdoc
     */
    public function setContents(array $contents): void
    {
        $this->contents = $contents;
    }

    /**
     * @inheritdoc
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * @inheritdoc
     */
    public function setActive(bool $active): void
    {
        $this->active =$active;
    }

    /**
     * @inheritdoc
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @inheritdoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}