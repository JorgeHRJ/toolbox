<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 * @ORM\Table("asset")
 */
class Asset
{
    const IMAGE_TYPE = 'image';
    const STAGES_ORIGIN = 'stages';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="asset_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="asset_path", type="string", length=255, nullable=false)
     */
    private string $path;

    /**
     * @ORM\Column(name="asset_filename", type="string", length=128, nullable=false)
     */
    private string $filename;

    /**
     * @ORM\Column(name="asset_type", type="string", length=8, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="asset_extension", type="string", length=8, nullable=false)
     */
    private string $extension;

    /**
     * @ORM\Column(name="asset_origin", type="string", length=32, nullable=false)
     */
    private string $origin;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="asset_uploaded_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $uploadedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }
}
