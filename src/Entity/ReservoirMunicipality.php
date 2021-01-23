<?php

namespace App\Entity;

use App\Library\Traits\Entity\TimestampableTrait;
use App\Repository\ReservoirMunicipalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservoirMunicipalityRepository::class)
 * @ORM\Table(name="reservoirmunicipality")
 */
class ReservoirMunicipality
{
    use TimestampableTrait;

    const LA_PALMA_MUNICIPALITES = [
        'Santa Cruz de La Palma', 'Puntallana', 'San Andrés y Sauces', 'Barlovento', 'Garafía', 'Puntagorda',
        'Tijarafe', 'Tazacorte', 'Los Llanos de Aridane', 'El Paso', 'Fuencaliente', 'Mazo', 'Breña Baja', 'Breña Alta'
    ];

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="reservoirmunicipality_id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reservoirmunicipality_name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var Collection|null
     *
     * @ORM\OneToMany(targetEntity=Reservoir::class, mappedBy="municipality")
     */
    private $reservoirs;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="reservoirmunicipality_created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Assert\Type("\DateTimeInterface")
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="reservoirmunicipality_modified_at", type="datetime", nullable=true)
     */
    private $modifiedAt;

    public function __construct()
    {
        $this->reservoirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Reservoir[]
     */
    public function getReservoirs(): Collection
    {
        return $this->reservoirs;
    }

    public function addReservoir(Reservoir $reservoir): self
    {
        if (!$this->reservoirs->contains($reservoir)) {
            $this->reservoirs[] = $reservoir;
            $reservoir->setMunicipality($this);
        }

        return $this;
    }

    public function removeReservoir(Reservoir $reservoir): self
    {
        if ($this->reservoirs->removeElement($reservoir)) {
            // set the owning side to null (unless already changed)
            if ($reservoir->getMunicipality() === $this) {
                $reservoir->setMunicipality(null);
            }
        }

        return $this;
    }
}
