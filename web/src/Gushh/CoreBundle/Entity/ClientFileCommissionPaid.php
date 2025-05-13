<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClientFileCommissionPaid
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClientFileCommissionPaid
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="detail", type="text", nullable=true)
     */
    private $detail;

    /**
     * @var decimal
     *
     * @ORM\Column(name="amount", type="decimal", precision=15, scale=2)
     */
    private $amount;


    /**
     * @ORM\ManyToOne(targetEntity="ClientFile", inversedBy="commissions_paid")
     * @ORM\JoinColumn(name="client_file_id", referencedColumnName="id")
     */
    protected $clientFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ClientFileCommissionPaid
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set amount
     *
     * @param decimal $amount
     * @return ClientFileCommissionPaid
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return decimal
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ClientFileCommissionPaid
     */
    public function setDate($date)
    {
        $date = new \DateTime($date);
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ClientFileCommissionPaid
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ClientFileCommissionPaid
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set provider
     *
     * @param \Gushh\CoreBundle\Entity\Provider $provider
     * @return ClientFileCommissionPaid
     */
    public function setProvider(\Gushh\CoreBundle\Entity\Provider $provider = null)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Get provider
     *
     * @return \Gushh\CoreBundle\Entity\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set status
     *
     * @param \Gushh\CoreBundle\Entity\Provider $provider
     * @return ClientFileCommissionPaid
     */
    public function setClientFile(\Gushh\CoreBundle\Entity\ClientFile $client_file = null)
    {
        $this->clientFile = $client_file;
        return $this;
    }

    /**
     * Get status
     *
     * @return \Gushh\CoreBundle\Entity\ClientFile
     */
    public function getClientFile()
    {
        return $this->clientFile;
    }
}


