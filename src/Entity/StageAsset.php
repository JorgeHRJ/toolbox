<?php

namespace App\Entity;

use App\Repository\StageAssetRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=StageAssetRepository::class)
 * @ORM\Table(name="stageasset")
 */
class StageAsset
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="stageasset_id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="stageasset_title", type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=Stage::class, inversedBy="assets")
     * @ORM\JoinColumn(name="stageasset_stage", referencedColumnName="stage_id", nullable=false)
     */
    private ?Stage $stage;

    /**
     * @ORM\ManyToOne(targetEntity=Asset::class)
     * @ORM\JoinColumn(name="stageasset_asset", referencedColumnName="asset_id", nullable=false)
     */
    private Asset $asset;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="stageasset_created_at", type="datetime", nullable=false)
     */
    private \DateTimeInterface $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
