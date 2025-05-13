<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gushh\CoreBundle\Classes\Util;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\ReservationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="hotel_file_id", type="string", length=255, unique=true)
     */
    private $hotelFileId;

    /**
     * @var string
     *
     * @ORM\Column(name="subtotal", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $subtotal;


    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="services_price", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $servicesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $total;

    /**
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="promo_discount_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $promoDiscountNet = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="taxes_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $taxesNet;

    /**
     * @var string
     *
     * @ORM\Column(name="total_net", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $totalNet;


    /**
     * @var integer
     *
     * @ORM\Column(name="adults", type="integer")
     */
    private $adults;

    /**
     * @var integer
     *
     * @ORM\Column(name="children", type="integer")
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="promotions", type="string", length=255, unique=true)
     */
    private $promotionsNames;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_on_request", type="boolean"))
     */
    private $isOnRequest = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cancellable", type="boolean")
     */
    private $isCancellable = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_blackedout", type="boolean")
     */
    private $isBlackedOut = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cancellable_until", type="datetime", nullable=true)
     */
    private $cancellableUntil;

//    /**
//     * @var decimal
//     *
//     * @ORM\Column(name="extra_price", type="decimal", precision=15, scale=2)
//     * @Assert\NotBlank()
//     * @Assert\Type(
//     *     type="numeric",
//     *     message="The value {{ value }} is not a valid {{ type }}."
//     * )
//     */
//    private $extraPrice = 0;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="extra_detail", type="string", length=255)
//     */
//    private $extraDetail;


    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $remarks;

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
     * @ORM\ManyToOne(targetEntity="ReservationStatus", inversedBy="reservations")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="ReservationPaymentStatus", inversedBy="reservations")
     * @ORM\JoinColumn(name="payment_status_id", referencedColumnName="id")
     */
    protected $paymentStatus;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $operator;

    /**
     * @ORM\OneToOne(targetEntity="CheckOut", mappedBy="reservation")
     */
    private $checkOutProcess;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="reservations")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="reservations")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     */
    protected $hotel;

    /**
     * @ORM\OneToOne(targetEntity="Invoice", mappedBy="reservation")
     */
    protected $invoice;

    /**
     * @ORM\OneToMany(targetEntity="ReservationPayment", mappedBy="reservation", cascade={"remove"})
     */
    protected $payments;

    /**
     * @ORM\OneToMany(targetEntity="ReservationDate", mappedBy="reservation", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $dates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->dates = new ArrayCollection();
    }
    /**
     * To string function
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->code;
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
     * Set code
     *
     * @return Reservation
     */
    public function setCode($id)
    {
        $code = Util::createID('ZT-', $id);

        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set hotel file id
     *
     * @return Reservation
     */
    public function setHotelFileId($hotelFileId)
    {
        $this->hotelFileId = $hotelFileId;

        return $this;
    }

    /**
     * Get hotel file id
     *
     * @return string
     */
    public function getHotelFileId()
    {
        return $this->hotelFileId;
    }

    /**
     * Set subtotal
     *
     * @param string $subtotal
     * @return Reservation
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return string 
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set commission
     *
     * @param string $commission
     * @return Reservation
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string 
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set servicesPrice
     *
     * @param string $servicesPrice
     * @return Reservation
     */
    public function setServicesPrice($servicesPrice)
    {
        $this->servicesPrice = $servicesPrice;

        return $this;
    }

    /**
     * Get servicesPrice
     *
     * @return string 
     */
    public function getServicesPrice()
    {
        return $this->servicesPrice;
    }

    /**
     * Set total
     *
     * @param string $total
     * @return Reservation
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
     * Set taxesNet
     *
     * @param string $taxesNet
     * @return Reservation
     */
    public function setTaxesNet($taxesNet)
    {
        $this->taxesNet = $taxesNet;

        return $this;
    }

    /**
     * Get taxesNet
     *
     * @return string
     */
    public function getTaxesNet()
    {
        return $this->taxesNet;
    }

    /**
     * Set promoDiscountNet
     *
     * @param string $promoDiscountNet
     * @return Reservation
     */
    public function setPromoDiscountNet($promoDiscountNet)
    {
        $this->promoDiscountNet = $promoDiscountNet;

        return $this;
    }

    /**
     * Get promoDiscountNet
     *
     * @return string
     */
    public function getPromoDiscountNet()
    {
        return $this->promoDiscountNet;
    }

    /**
     * Set priceNet
     *
     * @param string $priceNet
     * @return Reservation
     */
    public function setPriceNet($priceNet)
    {
        $this->priceNet = $priceNet;

        return $this;
    }

    /**
     * Get priceNet
     *
     * @return string
     */
    public function getPriceNet()
    {
        return $this->priceNet;
    }

    /**
     * Set totalNet
     *
     * @param string $totalNet
     * @return Reservation
     */
    public function setTotalNet($totalNet)
    {
        $this->totalNet = $totalNet;

        return $this;
    }

    /**
     * Get totalNet
     *
     * @return string
     */
    public function getTotalNet()
    {
        return $this->totalNet;
    }


//    /**
//     * Set extraPrice
//     *
//     * @param string $extraPrice
//     * @return Reservation
//     */
//    public function setExtraPrice($extraPrice)
//    {
//        $this->extraPrice = $extraPrice;
//
//        return $this;
//    }
//
//    /**
//     * Get extraPrice
//     *
//     * @return string
//     */
//    public function getExtraPrice()
//    {
//        return $this->extraPrice;
//    }
//
//
//    /**
//     * Set extraDetail
//     *
//     * @param string $extraDetail
//     * @return Reservation
//     */
//    public function setExtraDetail($extraDetail)
//    {
//        $this->extraDetail = $extraDetail;
//
//        return $this;
//    }
//
//    /**
//     * Get extraDetail
//     *
//     * @return string
//     */
//    public function getExtraDetail()
//    {
//        return $this->extraDetail;
//    }

    /**
     * Set room1Adults
     *
     * @param integer $adults
     * @return Search
     */
    public function setAdults($adults)
    {
        $this->adults = $adults;

        return $this;
    }

    /**
     * Get $adults
     *
     * @return integer
     */
    public function getAdults()
    {
        return $this->adults;
    }

    /**
     * Set room1Children
     *
     * @param integer $children
     * @return Search
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set cancellableUntil
     *
     * @param \DateTime $cancellableUntil
     * @return Date
     */
    public function setCancellableUntil($cancellableUntil)
    {
        $this->cancellableUntil = $cancellableUntil;

        return $this;
    }

    /**
     * Get cancellableUntil
     *
     * @return \DateTime
     */
    public function getCancellableUntil()
    {
        return $this->cancellableUntil;
    }


    /**
     * Set remarks
     *
     * @param string $remarks
     * @return Reservation
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Reservation
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
     * @return Reservation
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
     * Set promotionsNames
     *
     * @param \DateTime $promotionsNames
     * @return Reservation
     */
    public function setPromotionsNames($promotionsNames)
    {
        $this->promotionsNames = $promotionsNames;

        return $this;
    }

    /**
     * Get promotionsNames
     *
     * @return \DateTime
     */
    public function getPromotionsNames()
    {
        return $this->promotionsNames;
    }

    /**
     * Set isOnRequest
     *
     * @param boolean $isOnRequest
     * @return Reservation
     */
    public function setIsOnRequest($isOnRequest)
    {
        $this->isOnRequest = $isOnRequest;

        return $this;
    }

    /**
     * Get isOnRequest
     *
     * @return boolean
     */
    public function getIsOnRequest()
    {
        return $this->isOnRequest;
    }


    /**
     * Set isCancellable
     *
     * @param boolean $isCancellable
     * @return Reservation
     */
    public function setIsCancellable($isCancellable)
    {
        $this->isCancellable = $isCancellable;

        return $this;
    }

    /**
     * Get isCancellable
     *
     * @return boolean
     */
    public function getIsCancellable()
    {
        return $this->isCancellable;
    }


    /**
     * Set isBlackedOut
     *
     * @param boolean $isBlackedOut
     * @return Reservation
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
     * Set status
     *
     * @param \Gushh\CoreBundle\Entity\ReservationStatus $status
     * @return Reservation
     */
    public function setStatus(ReservationStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Gushh\CoreBundle\Entity\ReservationStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set operator
     *
     * @param \Gushh\CoreBundle\Entity\User $operator
     * @return Reservation
     */
    public function setOperator(User $operator = null)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return \Gushh\CoreBundle\Entity\User 
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set checkOutProcess
     *
     * @param \Gushh\CoreBundle\Entity\CheckOut $checkOutProcess
     * @return Reservation
     */
    public function setCheckOutProcess(CheckOut $checkOutProcess = null)
    {
        $this->checkOutProcess = $checkOutProcess;

        return $this;
    }

    /**
     * Get checkOutProcess
     *
     * @return \Gushh\CoreBundle\Entity\CheckOut
     */
    public function getCheckOutProcess()
    {
        return $this->checkOutProcess;
    }

    /**
     * Set checkOutProcess
     *
     * @param \Gushh\CoreBundle\Entity\Invoice $invoice
     * @return Reservation
     */
    public function setInvoice(Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Gushh\CoreBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }


    /**
     * Set paymentStatus
     *
     * @param \Gushh\CoreBundle\Entity\ReservationPaymentStatus $paymentStatus
     * @return Reservation
     */
    public function setPaymentStatus(ReservationPaymentStatus $paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return \Gushh\CoreBundle\Entity\ReservationPaymentStatus 
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set room
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Reservation
     */
    public function setRoom(Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Gushh\CoreBundle\Entity\Room 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set hotel
     *
     * @param \Gushh\CoreBundle\Entity\Hotel $hotel
     * @return Reservation
     */
    public function setHotel(Hotel $hotel = null)
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * Get hotel
     *
     * @return \Gushh\CoreBundle\Entity\Hotel 
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * Add payments
     *
     * @param ReservationPayment $payment
     * @return Reservation
     */
    public function addPayment(ReservationPayment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param ReservationPayment $payment
     */
    public function removePayment(ReservationPayment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentsAmount()
    {
        $paymentsAmount = 0;
        if( $this->payments != [] ) {
            foreach ($this->payments as $payment)
                $paymentsAmount += $payment->getAmount();
        }
        return $paymentsAmount;
    }

    /**
     * Get balance
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBalance()
    {
        return $this->total - $this->getPaymentsAmount();
    }

    /**
     * Add dates
     *
     * @param ReservationDate $reservationDate
     * @return Reservation
     */
    public function addReservationDate(ReservationDate $reservationDate)
    {
        $this->dates[] = $reservationDate;

        return $this;
    }

    /**
     * Remove date
     *
     * @param ReservationDate $reservationDate
     */
    public function removeReservationDate(ReservationPayment $reservationDate)
    {
        $this->dates->removeElement($reservationDate);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservationDates()
    {
        return $this->dates;
    }

}
