<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Gushh\CoreBundle\Classes\Util;

/**
 * Date
 *
 * @ORM\Table(name="reservation_date")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\ReservationDateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ReservationDate
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
     * @var decimal
     *
     * @ORM\Column(name="night_tax", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $nightTax;

    /**
     * @var decimal
     *
     * @ORM\Column(name="night_occupancy_tax", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $nightOccupancyTax;

    /**
     * @var decimal
     *
     * @ORM\Column(name="price_net_remaining", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $priceNetRemaining;

    /**
     * @var decimal
     *
     * @ORM\Column(name="price_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $priceNet;

    /**
     * @var decimal
     *
     * @ORM\Column(name="mandatory_services", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $mandatoryServices;

    /**
     * @var decimal
     *
     * @ORM\Column(name="mandatory_services_remaining", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $mandatoryServicesRemaining;

    /**
     * @var decimal
     *
     * @ORM\Column(name="profit", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $profit;

    /**
     * @var decimal
     *
     * @ORM\Column(name="cancellation_penalty", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $cancellationPenalty;

    /**
     * @var boolean
     *
     * @ORM\Column(name="blackout", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $isBlackedOut = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="premium", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $isPremium = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="on_request", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $onRequest = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_promotion", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $isPromotion = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_free", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $isFree = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="date_with_discount", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $dateWithDiscount = false;

    /**
     * @ORM\ManyToOne(targetEntity="Reservation", inversedBy="dates")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    protected $reservation;

    /**
     * @ORM\ManyToOne(targetEntity="Date")
     * @ORM\JoinColumn(name="date_id", referencedColumnName="id")
     */
    protected $date;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalNet = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="total_with_services_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalWithServicesNet = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="price", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $price = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tax", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $tax = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="commission", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $commission = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $total = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_with_services", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalWithServices = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="total_with_services_and_commission", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalWithServicesAndCommission = 0;

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
     * To string function
     *
     * @return string
     */
    public function __toString()
    {
        return $this->date->format('Y-m-d H:i:s');
    }


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
     * Set date
     *
     * @param \Gushh\CoreBundle\Entity\Date $date
     * @return ReservationDate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \Gushh\CoreBundle\Entity\Date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nightTax
     *
     * @param decimal $nightTax
     * @return ReservationDate
     */
    public function setNightTax($nightTax)
    {
        $this->nightTax = $nightTax;

        return $this;
    }

    /**
     * Get nightTax
     *
     * @return decimal
     */
    public function getNightTax()
    {
        return $this->nightTax;
    }


    /**
     * Set nightOccupancyTax
     *
     * @param decimal $nightOccupancyTax
     * @return ReservationDate
     */
    public function setNightOccupancyTax($nightOccupancyTax)
    {
        $this->nightOccupancyTax = $nightOccupancyTax;

        return $this;
    }

    /**
     * Get nightOccupancyTax
     *
     * @return decimal
     */
    public function getNightOccupancyTax()
    {
        return $this->nightOccupancyTax;
    }

    /**
     * Set priceNetRemaining
     *
     * @param decimal $priceNetRemaining
     * @return ReservationDate
     */
    public function setPriceNetRemaining($priceNetRemaining)
    {
        $this->priceNetRemaining = $priceNetRemaining;

        return $this;
    }

    /**
     * Get priceNetRemaining
     *
     * @return decimal
     */
    public function getPriceNetRemaining()
    {
        return $this->priceNetRemaining;
    }

    /**
     * Set priceNet
     *
     * @param decimal $priceNet
     * @return ReservationDate
     */
    public function setPriceNet($priceNet)
    {
        $this->priceNet = $priceNet;

        return $this;
    }

    /**
     * Get priceNet
     *
     * @return decimal
     */
    public function getPriceNet()
    {
        return $this->priceNet;
    }

    /**
     * Set mandatoryServices
     *
     * @param decimal $mandatoryServices
     * @return ReservationDate
     */
    public function setMandatoryServices($mandatoryServices)
    {
        $this->mandatoryServices = $mandatoryServices;

        return $this;
    }

    /**
     * Get mandatoryServices
     *
     * @return decimal
     */
    public function getMandatoryServices()
    {
        return $this->mandatoryServices;
    }

    /**
     * Set mandatoryServicesRemaining
     *
     * @param decimal $mandatoryServicesRemaining
     * @return ReservationDate
     */
    public function setMandatoryServicesRemaining($mandatoryServicesRemaining)
    {
        $this->mandatoryServicesRemaining = $mandatoryServicesRemaining;

        return $this;
    }

    /**
     * Get mandatoryServicesRemaining
     *
     * @return decimal
     */
    public function getMandatoryServicesRemaining()
    {
        return $this->mandatoryServicesRemaining;
    }

    /**
     * Set profit
     *
     * @param decimal $profit
     * @return ReservationDate
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get profit
     *
     * @return decimal
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * Set cancellationPenalty
     *
     * @param decimal $cancellationPenalty
     * @return ReservationDate
     */
    public function setCancellationPenalty($cancellationPenalty)
    {
        $this->cancellationPenalty = $cancellationPenalty;

        return $this;
    }

    /**
     * Get cancellationPenalty
     *
     * @return decimal
     */
    public function getCancellationPenalty()
    {
        return $this->cancellationPenalty;
    }

    /**
     * Set totalNet
     *
     * @param decimal $totalNet
     * @return ReservationDate
     */
    public function setTotalNet($totalNet)
    {
        $this->totalNet = $totalNet;

        return $this;
    }

    /**
     * Get totalNet
     *
     * @return decimal
     */
    public function getTotalNet()
    {
        return $this->totalNet;
    }

    /**
     * Set totalWithServicesNet
     *
     * @param decimal $totalWithServicesNet
     * @return ReservationDate
     */
    public function setTotalWithServicesNet($totalWithServicesNet)
    {
        $this->totalWithServicesNet = $totalWithServicesNet;

        return $this;
    }

    /**
     * Get totalWithServicesNet
     *
     * @return decimal
     */
    public function getTotalWithServicesNet()
    {
        return $this->totalWithServicesNet;
    }

    /**
     * Set price
     *
     * @param decimal $price
     * @return ReservationDate
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return decimal
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set tax
     *
     * @param decimal $tax
     * @return ReservationDate
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return decimal
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set commission
     *
     * @param decimal $commission
     * @return ReservationDate
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return decimal
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set total
     *
     * @param decimal $total
     * @return ReservationDate
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return decimal
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set totalWithServices
     *
     * @param decimal $totalWithServices
     * @return ReservationDate
     */
    public function setTotalWithServices($totalWithServices)
    {
        $this->totalWithServices = $totalWithServices;

        return $this;
    }

    /**
     * Get totalWithServices
     *
     * @return decimal
     */
    public function getTotalWithServices()
    {
        return $this->totalWithServices;
    }

    /**
     * Set totalWithServicesAndCommission
     *
     * @param decimal $totalWithServicesAndCommission
     * @return ReservationDate
     */
    public function setTotalWithServicesAndCommission($totalWithServicesAndCommission)
    {
        $this->totalWithServicesAndCommission = $totalWithServicesAndCommission;

        return $this;
    }

    /**
     * Get totalWithServicesAndCommission
     *
     * @return decimal
     */
    public function getTotalWithServicesAndCommission()
    {
        return $this->totalWithServicesAndCommission;
    }

    /**
     * Set $isBlackedOut
     *
     * @param boolean $isBlackedOut
     * @return ReservationDate
     */
    public function setIsBlackedOut($isBlackedOut)
    {
        $this->isBlackedOut = $isBlackedOut;

        return $this;
    }

    /**
     * Get isBlackedOut
     *
     * @return boolean
     */
    public function getIsBlackedOut()
    {
        return $this->isBlackedOut;
    }

    /**
     * Set $isPremium
     *
     * @param boolean $isPremium
     * @return ReservationDate
     */
    public function setIsPremium($isPremium)
    {
        $this->isPremium = $isPremium;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getIsPremium()
    {
        return $this->isPremium;
    }

    /**
     * Set $onRequest
     *
     * @param boolean $onRequest
     * @return ReservationDate
     */
    public function setOnRequest($onRequest)
    {
        $this->onRequest = $onRequest;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getOnRequest()
    {
        return $this->onRequest;
    }

    /**
     * Set $isPromotion
     *
     * @param boolean $isPromotion
     * @return ReservationDate
     */
    public function setIsPromotion($isPromotion)
    {
        $this->isPromotion = $isPromotion;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getIsPromotion()
    {
        return $this->isPromotion;
    }

    /**
     * Set $isFree
     *
     * @param boolean $isFree
     * @return ReservationDate
     */
    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getIsFree()
    {
        return $this->isFree;
    }

    /**
     * Set $dateWithDiscount
     *
     * @param boolean $dateWithDiscount
     * @return ReservationDate
     */
    public function setDateWithDiscount($dateWithDiscount)
    {
        $this->dateWithDiscount = $dateWithDiscount;

        return $this;
    }

    /**
     * Get stopSell
     *
     * @return boolean
     */
    public function getDateWithDiscount()
    {
        return $this->dateWithDiscount;
    }

    /**
     * Set reservation
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservation
     * @return ReservationDate
     */
    public function setReservation(\Gushh\CoreBundle\Entity\Reservation $reservation = null)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return \Gushh\CoreBundle\Entity\Reservation
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Rate
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
     * @return Rate
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

    /*
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return ($this->date !== null) ? $this->date->getDay() : '';
    }
}
