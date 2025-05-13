<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rate
 *
 * @ORM\Table(name="rate")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\RateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Rate
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
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
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="occupancy_tax", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $occupancyTax = 0;

    /**
     * @var string
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
     * @var string
     *
     * @ORM\Column(name="price_triple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceTriple = 0;  

    /**
     * @var string
     *
     * @ORM\Column(name="price_quadruple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceQuadruple = 0; 

    /**
     * @var string
     *
     * @ORM\Column(name="price_quintuple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceQuintuple = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="price_sextuple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceSextuple = 0;   

    /**
     * @var string
     *
     * @ORM\Column(name="price_septuple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceSeptuple = 0; 

    /**
     * @var string
     *
     * @ORM\Column(name="price_octuple", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $priceOctuple = 0; 

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $enabled = true;

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
     * @ORM\ManyToOne(targetEntity="ProfitType", inversedBy="rates")
     * @ORM\JoinColumn(name="profit_type_id", referencedColumnName="id")
     */
    protected $profitType;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="rates")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="mondayRate", cascade={"remove"})
     */
    protected $mondayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="tuesdayRate", cascade={"remove"})
     */
    protected $tuesdayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="wednesdayRate", cascade={"remove"})
     */
    protected $wednesdayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="thursdayRate", cascade={"remove"})
     */
    protected $thursdayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="fridayRate", cascade={"remove"})
     */
    protected $fridayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="saturdayRate", cascade={"remove"})
     */
    protected $saturdayDates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="sundayRate", cascade={"remove"})
     */
    protected $sundayDates;

    /**
     * @ORM\ManyToOne(targetEntity="Label", inversedBy="rates")
     * @ORM\JoinColumn(name="label_id", referencedColumnName="id")
     */
    protected $label;


    /**
     * To string function
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->name;
    }
    
    public function getMandatoryServicesTotal()
    {
        $mandatoryServicesTotal = $this->getRoom()->getHotel()->getMandatoryServicesTotal();
        
        return $mandatoryServicesTotal;
    }

    public function getHotelMandatoryServices($pax = 2, $nights = 1)
    {
        return $this->getRoom()->getHotel()->getMandatoryServicesTotal($pax, $nights);
    }

    public function getRoomMandatoryServices($pax = 2, $nights = 1)
    {
        return $this->getRoom()->getMandatoryServicesTotal($pax, $nights);
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
     * Set name
     *
     * @param string $name
     * @return Rate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Rate
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice($pax = 2)
    {
        // Fall through is on propose
        $total = 0;
        switch ($pax) {
            case 8:
                $total = $this->priceOctuple;
            case 7:
                $total = $total + $this->priceSeptuple;
            case 6:
                $total = $total + $this->priceSextuple;
            case 5:
                $total = $total + $this->priceQuintuple;
            case 4:
                $total = $total + $this->priceQuadruple;
            case 3:
                $total = $total + $this->priceTriple;
            default:
                $total = $total + $this->price;
                break;
        }

        return round($total, 2);
    }

    /**
     * Set tax
     *
     * @param string $tax
     * @return Rate
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return string 
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Get salePrice
     *
     * @return string 
     */
    public function getSalePrice($pax = 2)
    {
        $total = $this->getNetCost($pax) + $this->getNetProfit($pax);        

        return round($total, 2);
    }    

    /**
     * Get netCost
     *
     * @return string 
     */
    public function getNetCost($pax = 2)
    {

        $total = $this->getPrice($pax) + $this->getNetTax($pax) + $this->occupancyTax;

        return round($total, 2);
    }

    /**
     * Get netProfit
     *
     * @return RoomService
     */
    public function getNetProfit($pax = 2)
    {
        return round(($this->profitType->getSlug() == 'net')
                    ? $this->profit
                    : $this->getNetCost($pax) * ($this->profit / 100)
                    , 2);
    }

    /**
     * Get netTax
     *
     * @return string 
     */
    public function getNetTax($pax = 2)
    {
        return round($this->getPrice($pax) * ($this->tax / 100), 2);
    }          

    /**
     * Set profit
     *
     * @param string $profit
     * @return Rate
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get profit
     *
     * @return string 
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Rate
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
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

    /**
     * Set profitType
     *
     * @param \Gushh\CoreBundle\Entity\ProfitType $profitType
     * @return Rate
     */
    public function setProfitType(\Gushh\CoreBundle\Entity\ProfitType $profitType = null)
    {
        $this->profitType = $profitType;

        return $this;
    }

    /**
     * Get profitType
     *
     * @return \Gushh\CoreBundle\Entity\ProfitType 
     */
    public function getProfitType()
    {
        return $this->profitType;
    }

    /**
     * Set room
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Rate
     */
    public function setRoom(\Gushh\CoreBundle\Entity\Room $room = null)
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
     * Add dates
     *
     * @param \Gushh\CoreBundle\Entity\Date $dates
     * @return Rate
     */
    public function addDate(\Gushh\CoreBundle\Entity\Date $dates)
    {
        $this->dates[] = $dates;

        return $this;
    }

    /**
     * Remove dates
     *
     * @param \Gushh\CoreBundle\Entity\Date $dates
     */
    public function removeDate(\Gushh\CoreBundle\Entity\Date $dates)
    {
        $this->dates->removeElement($dates);
    }

    /**
     * Get dates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Set label
     *
     * @param \Gushh\CoreBundle\Entity\Label $label
     * @return Rate
     */
    public function setLabel(\Gushh\CoreBundle\Entity\Label $label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return \Gushh\CoreBundle\Entity\Label 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set salePrice
     *
     * @param string $salePrice
     * @return Rate
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * Set occupancyTax
     *
     * @param string $occupancyTax
     * @return Rate
     */
    public function setOccupancyTax($occupancyTax)
    {
        $this->occupancyTax = $occupancyTax;

        return $this;
    }

    /**
     * Get occupancyTax
     *
     * @return string 
     */
    public function getOccupancyTax()
    {
        return $this->occupancyTax;
    }

    /**
     * Add mondayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $mondayDates
     * @return Rate
     */
    public function addMondayDate(\Gushh\CoreBundle\Entity\Date $mondayDates)
    {
        $this->mondayDates[] = $mondayDates;

        return $this;
    }

    /**
     * Remove mondayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $mondayDates
     */
    public function removeMondayDate(\Gushh\CoreBundle\Entity\Date $mondayDates)
    {
        $this->mondayDates->removeElement($mondayDates);
    }

    /**
     * Get mondayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMondayDates()
    {
        return $this->mondayDates;
    }

    /**
     * Add tuesdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $tuesdayDates
     * @return Rate
     */
    public function addTuesdayDate(\Gushh\CoreBundle\Entity\Date $tuesdayDates)
    {
        $this->tuesdayDates[] = $tuesdayDates;

        return $this;
    }

    /**
     * Remove tuesdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $tuesdayDates
     */
    public function removeTuesdayDate(\Gushh\CoreBundle\Entity\Date $tuesdayDates)
    {
        $this->tuesdayDates->removeElement($tuesdayDates);
    }

    /**
     * Get tuesdayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTuesdayDates()
    {
        return $this->tuesdayDates;
    }

    /**
     * Add wednesdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $wednesdayDates
     * @return Rate
     */
    public function addWednesdayDate(\Gushh\CoreBundle\Entity\Date $wednesdayDates)
    {
        $this->wednesdayDates[] = $wednesdayDates;

        return $this;
    }

    /**
     * Remove wednesdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $wednesdayDates
     */
    public function removeWednesdayDate(\Gushh\CoreBundle\Entity\Date $wednesdayDates)
    {
        $this->wednesdayDates->removeElement($wednesdayDates);
    }

    /**
     * Get wednesdayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWednesdayDates()
    {
        return $this->wednesdayDates;
    }

    /**
     * Add thursdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $thursdayDates
     * @return Rate
     */
    public function addThursdayDate(\Gushh\CoreBundle\Entity\Date $thursdayDates)
    {
        $this->thursdayDates[] = $thursdayDates;

        return $this;
    }

    /**
     * Remove thursdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $thursdayDates
     */
    public function removeThursdayDate(\Gushh\CoreBundle\Entity\Date $thursdayDates)
    {
        $this->thursdayDates->removeElement($thursdayDates);
    }

    /**
     * Get thursdayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThursdayDates()
    {
        return $this->thursdayDates;
    }

    /**
     * Add fridayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $fridayDates
     * @return Rate
     */
    public function addFridayDate(\Gushh\CoreBundle\Entity\Date $fridayDates)
    {
        $this->fridayDates[] = $fridayDates;

        return $this;
    }

    /**
     * Remove fridayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $fridayDates
     */
    public function removeFridayDate(\Gushh\CoreBundle\Entity\Date $fridayDates)
    {
        $this->fridayDates->removeElement($fridayDates);
    }

    /**
     * Get fridayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFridayDates()
    {
        return $this->fridayDates;
    }

    /**
     * Add saturdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $saturdayDates
     * @return Rate
     */
    public function addSaturdayDate(\Gushh\CoreBundle\Entity\Date $saturdayDates)
    {
        $this->saturdayDates[] = $saturdayDates;

        return $this;
    }

    /**
     * Remove saturdayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $saturdayDates
     */
    public function removeSaturdayDate(\Gushh\CoreBundle\Entity\Date $saturdayDates)
    {
        $this->saturdayDates->removeElement($saturdayDates);
    }

    /**
     * Get saturdayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSaturdayDates()
    {
        return $this->saturdayDates;
    }

    /**
     * Add sundayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $sundayDates
     * @return Rate
     */
    public function addSundayDate(\Gushh\CoreBundle\Entity\Date $sundayDates)
    {
        $this->sundayDates[] = $sundayDates;

        return $this;
    }

    /**
     * Remove sundayDates
     *
     * @param \Gushh\CoreBundle\Entity\Date $sundayDates
     */
    public function removeSundayDate(\Gushh\CoreBundle\Entity\Date $sundayDates)
    {
        $this->sundayDates->removeElement($sundayDates);
    }

    /**
     * Get sundayDates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSundayDates()
    {
        return $this->sundayDates;
    }

    /**
     * Set extraPersonPrice
     *
     * @param string $extraPersonPrice
     * @return Rate
     */
    public function setExtraPersonPrice($extraPersonPrice)
    {
        $this->extraPersonPrice = $extraPersonPrice;

        return $this;
    }

    /**
     * Get extraPersonPrice
     *
     * @return string 
     */
    public function getExtraPersonPrice()
    {
        return $this->extraPersonPrice;
    }


    /**
     * Set priceTriple
     *
     * @param string $priceTriple
     * @return Rate
     */
    public function setPriceTriple($priceTriple)
    {
        $this->priceTriple = $priceTriple;

        return $this;
    }

    /**
     * Get priceTriple
     *
     * @return string 
     */
    public function getPriceTriple()
    {
        return $this->priceTriple;
    }

    /**
     * Set priceQuadruple
     *
     * @param string $priceQuadruple
     * @return Rate
     */
    public function setPriceQuadruple($priceQuadruple)
    {
        $this->priceQuadruple = $priceQuadruple;

        return $this;
    }

    /**
     * Get priceQuadruple
     *
     * @return string 
     */
    public function getPriceQuadruple()
    {
        return $this->priceQuadruple;
    }

    /**
     * Set priceQuintuple
     *
     * @param string $priceQuintuple
     * @return Rate
     */
    public function setPriceQuintuple($priceQuintuple)
    {
        $this->priceQuintuple = $priceQuintuple;

        return $this;
    }

    /**
     * Get priceQuintuple
     *
     * @return string 
     */
    public function getPriceQuintuple()
    {
        return $this->priceQuintuple;
    }

    /**
     * Set priceSextuple
     *
     * @param string $priceSextuple
     * @return Rate
     */
    public function setPriceSextuple($priceSextuple)
    {
        $this->priceSextuple = $priceSextuple;

        return $this;
    }

    /**
     * Get priceSextuple
     *
     * @return string 
     */
    public function getPriceSextuple()
    {
        return $this->priceSextuple;
    }

    /**
     * Set priceSeptuple
     *
     * @param string $priceSeptuple
     * @return Rate
     */
    public function setPriceSeptuple($priceSeptuple)
    {
        $this->priceSeptuple = $priceSeptuple;

        return $this;
    }

    /**
     * Get priceSeptuple
     *
     * @return string 
     */
    public function getPriceSeptuple()
    {
        return $this->priceSeptuple;
    }

    /**
     * Set priceOctuple
     *
     * @param string $priceOctuple
     * @return Rate
     */
    public function setPriceOctuple($priceOctuple)
    {
        $this->priceOctuple = $priceOctuple;

        return $this;
    }

    /**
     * Get priceOctuple
     *
     * @return string 
     */
    public function getPriceOctuple()
    {
        return $this->priceOctuple;
    }
}
