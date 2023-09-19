<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime", name = "created_at")
     */
    private $createdDate;


    /**
     * @ORM\Column(type="datetime", name = "modifiedAt")
     */
    private $modifiedDate;

    public function getCreatedDate(): ?DateTime
    {
        return $this->createdDate;
    }

    public function getModifiedDate(): ?DateTime
    {
        return $this->modifiedDate;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateValue()
    {
        $this->createdDate = new \DateTime();
        $this->modifiedDate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setModifiedDateValue()
    {
        $this->modifiedDate = new \DateTime();
    }
}