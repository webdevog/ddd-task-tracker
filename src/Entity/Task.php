<?php

namespace App\Entity;

use App\Model\Event\TaskCreated;
use App\Model\Event\TaskUpdated;
use App\Model\Event\TaskRemoved;
use App\Model\RaiseEventsInterface;
use App\Model\RaiseEventsTrait;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Task implements RaiseEventsInterface
{
    use RaiseEventsTrait;

    public const STATUS_NEW = 1;
    public const STATUS_COMPLETED = 2;

    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    public function __construct()
    {
        // Generate ID before the event
        $this->id = Uuid::uuid4();
        $this->status = self::STATUS_NEW;
        $this->createdAt = new \DateTimeImmutable();

        $this->raise(new TaskCreated($this));
    }

    public function getId(): ?string
    {
        return $this->id->toString();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @ORM\PostUpdate
     */
    public function onUpdate()
    {
        $this->raise(new TaskUpdated($this));
    }

    /**
     * @ORM\PreRemove
     */
    public function onRemove()
    {
        $this->raise(new TaskRemoved($this));
    }
}
