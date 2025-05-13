<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Room
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\RoomRepository")
 */
class Room
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
     * @ORM\Column(name="en_description", type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $enDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="es_description", type="text", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $esDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="capacity", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $capacity = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="lowStockWarning", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $lowStockWarning = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="square_feet", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $squareFeet = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
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
     * @var string
     *
     * @ORM\Column(name="video_url", type="string", length=1024) 
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $video;
    
    
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
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="rooms")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     */
    protected $hotel;

    /**
     * @ORM\OneToMany(targetEntity="Rate", mappedBy="room", cascade={"remove"})
     */
    protected $rates;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="room", cascade={"remove"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    protected $dates;

    /**
     * @ORM\OneToMany(targetEntity="RoomService", mappedBy="room", cascade={"remove"})
     */
    protected $services;

    /**
     * @ORM\OneToMany(targetEntity="RoomImage", mappedBy="room", cascade={"remove"})
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="Promotion", mappedBy="room", cascade={"remove"})
     */
    protected $promotions;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="room")
     */
    protected $reservations;

    /**
     * @ORM\ManyToOne(targetEntity="RoomContractType")
     * @ORM\JoinColumn(name="contract_type_id", referencedColumnName="id")
     */
    protected $contractType;


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
     * Constructor
     */
    public function __construct()
    {
        $this->rates        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dates        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->services     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images       = new \Doctrine\Common\Collections\ArrayCollection();
        $this->promotions   = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Room
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
     * Set enDescription
     *
     * @param string $enDescription
     * @return Room
     */
    public function setEnDescription($enDescription)
    {
        $this->enDescription = $enDescription;

        return $this;
    }

    /**
     * Get enDescription
     *
     * @return string
     */
    public function getEnDescription()
    {
        return $this->enDescription;
    }

    /**
     * Set esDescription
     *
     * @param string $esDescription
     * @return Room
     */
    public function setEsDescription($esDescription)
    {
        $this->esDescription = $esDescription;

        return $this;
    }

    /**
     * Get esDescription
     *
     * @return string
     */
    public function getEsDescription()
    {
        return $this->esDescription;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     * @return Room
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set squareFeet
     *
     * @param integer $squareFeet
     * @return Room
     */
    public function setSquareFeet($squareFeet)
    {
        $this->squareFeet = $squareFeet;

        return $this;
    }

    /**
     * Get squareFeet
     *
     * @return integer
     */
    public function getSquareFeet()
    {
        return $this->squareFeet;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Room
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
     * @return Room
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
     * Set name
     *
     * @param string $video
     * @return Room
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
    }
    

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Room
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
     * @return Room
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
     * Set hotel
     *
     * @param \Gushh\CoreBundle\Entity\Hotel $hotel
     * @return Room
     */
    public function setHotel(\Gushh\CoreBundle\Entity\Hotel $hotel = null)
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
     * Add rates
     *
     * @param \Gushh\CoreBundle\Entity\Rate $rates
     * @return Room
     */
    public function addRate(\Gushh\CoreBundle\Entity\Rate $rates)
    {
        $this->rates[] = $rates;

        return $this;
    }

    /**
     * Remove rates
     *
     * @param \Gushh\CoreBundle\Entity\Rate $rates
     */
    public function removeRate(\Gushh\CoreBundle\Entity\Rate $rates)
    {
        $this->rates->removeElement($rates);
    }

    /**
     * Get rates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Add dates
     *
     * @param \Gushh\CoreBundle\Entity\Date $dates
     * @return Room
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
     * Add services
     *
     * @param \Gushh\CoreBundle\Entity\RoomService $services
     * @return Room
     */
    public function addService(\Gushh\CoreBundle\Entity\RoomService $services)
    {
        $this->services[] = $services;

        return $this;
    }

    /**
     * Remove services
     *
     * @param \Gushh\CoreBundle\Entity\RoomService $services
     */
    public function removeService(\Gushh\CoreBundle\Entity\RoomService $services)
    {
        $this->services->removeElement($services);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Add images
     *
     * @param \Gushh\CoreBundle\Entity\RoomImage $images
     * @return Room
     */
    public function addImage(\Gushh\CoreBundle\Entity\RoomImage $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Gushh\CoreBundle\Entity\RoomImage $images
     */
    public function removeImage(\Gushh\CoreBundle\Entity\RoomImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add promotions
     *
     * @param \Gushh\CoreBundle\Entity\Promotion $promotions
     * @return Room
     */
    public function addPromotion(\Gushh\CoreBundle\Entity\Promotion $promotions)
    {
        $this->promotions[] = $promotions;

        return $this;
    }

    /**
     * Remove promotions
     *
     * @param \Gushh\CoreBundle\Entity\Promotion $promotions
     */
    public function removePromotion(\Gushh\CoreBundle\Entity\Promotion $promotions)
    {
        $this->promotions->removeElement($promotions);
    }

    /**
     * Get promotions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * Get enabledPromotions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEnabledPromotions()
    {
        $finalArray = [];

        $promotions = $this->promotions;

        foreach ($promotions as $promotion) :
            if ($promotion->getEnabled() == true) :
                $finalArray[] = $promotion;
            endif;
        endforeach;

        return $finalArray;
    }

    /**
     * Get combinable promotions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCombinablePromotions()
    {
        $finalArray = [];

        $promotions = $this->getEnabledPromotions();

        foreach ($promotions as $promotion) :
            if ($promotion->isCombinable()) :
                $finalArray[] = $promotion;
            endif;
        endforeach;

        return $finalArray;
    }

    /**
     * Get non combinable promotions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNonCombinablePromotions()
    {
        $finalArray = [];

        $promotions = $this->getEnabledPromotions();

        foreach ($promotions as $promotion) :
            if ($promotion->isNonCombinable()) :
                $finalArray[] = $promotion;
            endif;
        endforeach;

        return $finalArray;
    }

    /**
     * Get mandatory services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMandatoryServices()
    {
        $finalArray = [];

        $services = $this->services;

        foreach ($services as $service) {
            $type = $service->getServiceType()->getSlug();

            if ($type == 'mandatory') {
                array_push($finalArray, $service);
            }
        }

        return $finalArray;
    }

    /**
     * Get mandatory services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMandatoryServicesTotal($pax = 2, $nights = 1)
    {
        $total = 0;

        foreach ($this->getMandatoryServices() as $service) {
            $total = $total + $service->getSalePrice($pax, $nights);
        }

        return number_format($total, 2);
    }

    /**
     * Add reservations
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservations
     * @return Room
     */
    public function addReservation(\Gushh\CoreBundle\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;

        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservations
     */
    public function removeReservation(\Gushh\CoreBundle\Entity\Reservation $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Get lowStockWarning
     * @return
     */
    public function getLowStockWarning()
    {
        return $this->lowStockWarning;
    }

    /**
     * Set lowStockWarning
     * @return $this
     */
    public function setLowStockWarning($lowStockWarning)
    {
        $this->lowStockWarning = $lowStockWarning;
        return $this;
    }

    /**
     * Get contractType
     * @return
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * Set contractType
     * @return $this
     */
    public function setContractType($contractType)
    {
        $this->contractType = $contractType;
        return $this;
    }
}
