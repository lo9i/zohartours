<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * RoomService
 *
 * @ORM\Table(name="room_service")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\RoomServiceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class RoomService
{
    protected $nextAction;

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
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $description;

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
    private $price;

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
    private $tax;

    /**
     * @var string
     *
     * @ORM\Column(name="sale_price", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $salePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="net_cost", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $netCost;    

    /**
     * @var string
     *
     * @ORM\Column(name="net_profit", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $netProfit;  

    /**
     * @var string
     *
     * @ORM\Column(name="net_tax", type="decimal", precision=15, scale=2)
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )  
     */
    private $netTax;  

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
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

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
     * @ORM\ManyToOne(targetEntity="ProfitType", inversedBy="roomServices")
     * @ORM\JoinColumn(name="profit_type_id", referencedColumnName="id")
     */
    protected $profitType;

    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="services")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * @ORM\ManyToOne(targetEntity="ServiceType", inversedBy="roomServices")
     * @ORM\JoinColumn(name="service_type_id", referencedColumnName="id")
     */
    protected $serviceType;

    /**
     * @ORM\ManyToOne(targetEntity="ServicePayType", inversedBy="roomServices")
     * @ORM\JoinColumn(name="service_pay_type_id", referencedColumnName="id")
     */
    protected $servicePayType;

    /**
     * @ORM\OneToMany(targetEntity="PromotionBenefit", mappedBy="roomService", cascade={"remove"})
     */
    protected $benefits;

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
     * To string function
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Service
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
     * Set description
     *
     * @param string $description
     * @return Service
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return RoomService
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set tax
     *
     * @param string $tax
     * @return RoomService
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return RoomService
     */
    public function preSetNetPrices()
    {

        $this->netTax   = $this->price * ($this->tax / 100);

        $this->netCost  = $this->price + $this->netTax;

        if ($this->profitType->getSlug() == 'net') {
            $profit = $this->profit;
        } else {
            $profit = $this->netCost * ($this->profit / 100);
        }

        $this->salePrice = $this->netCost + $profit; // Util::roundNumber($total);

        $this->netProfit = $this->salePrice - $this->netCost;

        return $this;
    }

    /**
     * Get salePrice
     *
     * @return string 
     */
    public function getSalePrice($paxes = 1, $nights = 1)
    {
        $type = $this->servicePayType->getSlug();

        if ($type == 'unique-per-room') {
            $total = $this->salePrice;
        } elseif ($type == 'unique-per-person') {
            $total = $this->salePrice * $paxes;
        } elseif ($type == 'per-night-per-room') {
            $total = $this->salePrice * $nights;
        } elseif ($type == 'per-night-per-person') {
            $total = $this->salePrice * $paxes * $nights;
        }

        return round($total, 2);
    }    

    /**
     * Get netCost
     *
     * @return string 
     */
    public function getNetCost($count = 1)
    {
        $total = $this->netCost * $count;

        return round($total, 2);
    }

    /**
     * Get netProfit
     *
     * @return RoomService
     */
    public function getNetProfit($count = 1)
    {
        $total = $this->netProfit * $count;

        return round($total, 2);
    }

    /**
     * Get netTax
     *
     * @return string 
     */
    public function getNetTax($count = 1)
    {
        $total = $this->netTax * $count;

        return round($total, 2);
    }      

    /**
     * Set profit
     *
     * @param string $profit
     * @return RoomService
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
     * Set enabled
     * @param boolean $enabled
     * @return Service
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
     * Set slug
     *
     * @param string $slug
     * @return Service
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Service
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
     * @return Service
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
     * @return Service
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
     * Set serviceType
     *
     * @param \Gushh\CoreBundle\Entity\ServiceType $serviceType
     * @return Service
     */
    public function setServiceType(\Gushh\CoreBundle\Entity\ServiceType $serviceType = null)
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    /**
     * Get serviceType
     *
     * @return \Gushh\CoreBundle\Entity\ServiceType 
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * Set servicePayType
     *
     * @param \Gushh\CoreBundle\Entity\ServicePayType $servicePayType
     * @return Service
     */
    public function setServicePayType(\Gushh\CoreBundle\Entity\ServicePayType $servicePayType = null)
    {
        $this->servicePayType = $servicePayType;

        return $this;
    }

    /**
     * Get servicePayType
     *
     * @return \Gushh\CoreBundle\Entity\ServicePayType 
     */
    public function getServicePayType()
    {
        return $this->servicePayType;
    }

    /**
     * Total
     *
     * @return Service 
     */
    public function getTotal($paxes = 1, $nights = 1, $profit = true)
    {
        $total          = $this->getNetPrice($paxes, $nights, true);
        // $profit         = $this->getNetProfit();
        // $total          = $price + $profit;

        return $total;
        
    }

    public function getNextAction()
    {
        return $this->nextAction;
    }

    public function setNextAction($nextAction)
    {
        $this->nextAction = $nextAction;
        return $this;
    }

    /**
     * Set room
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Service
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
     * Constructor
     */
    public function __construct()
    {
        $this->benefits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add benefits
     *
     * @param \Gushh\CoreBundle\Entity\PromotionBenefit $benefits
     * @return RoomService
     */
    public function addBenefit(\Gushh\CoreBundle\Entity\PromotionBenefit $benefits)
    {
        $this->benefits[] = $benefits;

        return $this;
    }

    /**
     * Remove benefits
     *
     * @param \Gushh\CoreBundle\Entity\PromotionBenefit $benefits
     */
    public function removeBenefit(\Gushh\CoreBundle\Entity\PromotionBenefit $benefits)
    {
        $this->benefits->removeElement($benefits);
    }

    /**
     * Get benefits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBenefits()
    {
        return $this->benefits;
    }

}
