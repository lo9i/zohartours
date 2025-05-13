<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Invoice
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Invoice
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
     * @ORM\Column(name="payer_name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerName;

    /**
     * @var string
     *
     * @ORM\Column(name="payer_address", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="payer_zip_code", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerZipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="payer_city", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerCity;


    /**
     * @var string
     *
     * @ORM\Column(name="payer_state", type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerState;

    /**
     * @var string
     *
     * @ORM\Column(name="payer_country", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerCountry;
    /**
     * @var string
     *
     * @ORM\Column(name="payer_tax_id", type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $payerTaxId = '';


    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=15, scale=2)
     */
    private $total;

    /**
     * @ORM\OneToOne(targetEntity="Reservation", inversedBy="invoice")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    protected $reservation;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $items;

    /**
     * @ORM\ManyToOne(targetEntity="InvoiceStatus", inversedBy="invoices")
     * @ORM\JoinColumn(name="invoice_status_id", referencedColumnName="id")
     */
    protected $status;

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
     * Set total
     *
     * @param string $total
     * @return Invoice
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal()
    {
        return $this->total;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Invoice
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set payerName
     *
     * @param string $payerName
     * @return Invoice
     */
    public function setPayerName($payerName)
    {
        $this->payerName = $payerName;

        return $this;
    }

    /**
     * Get payerName
     *
     * @return string
     */
    public function getPayerName()
    {
        return $this->payerName;
    }

    /**
     * Set payerZipCode
     *
     * @param string $payerZipCode
     * @return Invoice
     */
    public function setPayerZipCode($payerZipCode)
    {
        $this->payerZipCode = $payerZipCode;

        return $this;
    }

    /**
     * Get payerZipCode
     *
     * @return string
     */
    public function getPayerZipCode()
    {
        return $this->payerZipCode;
    }
    /**
     * Set payerAddress
     *
     * @param string $payerAddress
     * @return Invoice
     */
    public function setPayerAddress($payerAddress)
    {
        $this->payerAddress = $payerAddress;

        return $this;
    }

    /**
     * Get payerAddress
     *
     * @return string
     */
    public function getPayerAddress()
    {
        return $this->payerAddress;
    }

    /**
     * Set payerCit
     *
     * @param string $payerCity
     * @return Invoice
     */
    public function setPayerCity($payerCity)
    {
        $this->payerCity = $payerCity;

        return $this;
    }

    /**
     * Get payerCity
     *
     * @return string
     */
    public function getPayerCity()
    {
        return $this->payerCity;
    }

    /**
     * Set payerState
     *
     * @param string $payerState
     * @return Invoice
     */
    public function setPayerState($payerState)
    {
        $this->payerState = $payerState;

        return $this;
    }

    /**
     * Get payerState
     *
     * @return string
     */
    public function getPayerState()
    {
        return $this->payerState;
    }

    /**
     * Set payerCountry
     *
     * @param string $payerCountry
     * @return Invoice
     */
    public function setPayerCountry($payerCountry)
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    /**
     * Get payerCountry
     *
     * @return string
     */
    public function getPayerCountry()
    {
        return $this->payerCountry;
    }

    /**
     * Set payerTaxId
     *
     * @param string $payerTaxId
     * @return Invoice
     */
    public function setPayerTaxId($payerTaxId)
    {
        $this->payerTaxId = $payerTaxId;

        return $this;
    }

    /**
     * Get payerTaxId
     *
     * @return string
     */
    public function getPayerTaxId()
    {
        if($this->payerTaxId)
            return $this->payerTaxId;
        return '';
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Invoice
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
     * @return Invoice
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
     * Add items
     *
     * @param \Gushh\CoreBundle\Entity\InvoiceItem $items
     * @return Invoice
     */
    public function addItem(\Gushh\CoreBundle\Entity\InvoiceItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Gushh\CoreBundle\Entity\InvoiceItem $items
     */
    public function removeItem(\Gushh\CoreBundle\Entity\InvoiceItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set status
     *
     * @param \Gushh\CoreBundle\Entity\InvoiceStatus $status
     * @return Invoice
     */
    public function setStatus(\Gushh\CoreBundle\Entity\InvoiceStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Gushh\CoreBundle\Entity\InvoiceStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reservation
     *
     * @param Reservation $reservation
     * @return Invoice
     */
    public function setReservation(\Gushh\CoreBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return string
     */
    public function getReservation()
    {
        return $this->reservation;
    }
}


