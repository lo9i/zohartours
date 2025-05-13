<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Gushh\CoreBundle\Classes\Util;

/**
 * Hotel
 *
 * @ORM\Table(name="hotel")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\HotelRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Hotel
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
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=true)  
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $subtitle;

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
     * @var string
     *
     * @ORM\Column(name="en_voucher_note", type="text", nullable=true) 
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $enVoucherNote;

    /**
     * @var string
     *
     * @ORM\Column(name="es_voucher_note", type="text", nullable=true)    
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )     
     */
    private $esVoucherNote;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     * @Assert\NotBlank()     
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )     
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)  
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="continent", type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $zipCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="check_in", type="time")
     * @Assert\NotBlank()     
     * @Assert\DateTime()     
     */
    private $checkIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="check_out", type="time")
     * @Assert\NotBlank()     
     * @Assert\DateTime() 
     */
    private $checkOut;

    /**
     * @var string
     *
     * @ORM\Column(name="reservation_email", type="string", length=255)
     * @Assert\NotBlank()     
     * @Assert\Email() 
     */
    private $reservationEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="cancellation_email", type="string", length=255)
     * @Assert\NotBlank()     
     * @Assert\Email() 
     */
    private $cancellationEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="coords", type="string", length=255, nullable=true)
     */
    private $coords;

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
     * @var integer
     *
     * @ORM\Column(name="child_age", type="integer")
     * @Assert\NotBlank()     
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $childAge;

    /**
     * @var string
     *
     * @ORM\Column(name="stars", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()  
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $stars = 3.5;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     */
    private $vip = false;

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
     * @var string
     *
     * @ORM\Column(name="hotelbeds_id", type="string", length=1024) 
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $hotelbedsId;

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
     * @ORM\OneToMany(targetEntity="HotelContact", mappedBy="hotel", cascade={"remove"})
     */
    protected $contacts;

    /**
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="hotel", cascade={"remove"})
     */
    protected $contracts;

    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="hotel", cascade={"remove"})
     */
    protected $rooms;

    /**
     * @ORM\OneToMany(targetEntity="HotelImage", mappedBy="hotel", cascade={"remove"})
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="HotelService", mappedBy="hotel", cascade={"remove"})
     */
    protected $services;

    /**
     * @ORM\OneToMany(targetEntity="CancellationPolicy", mappedBy="hotel", cascade={"remove"})
     */
    protected $policies;

    /**
     * @ORM\ManyToOne(targetEntity="HotelCurrency", inversedBy="hotels")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="hotels")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $cityImage;

    /**
     * @ORM\ManyToMany(targetEntity="Amenity", inversedBy="hotels")
     * @ORM\JoinTable(name="hotel_amenities",
     *      joinColumns={@ORM\JoinColumn(name="hotel_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="amenity_id", referencedColumnName="id")}
     *      )
    */
    protected $amenities;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="hotel")
     */
    protected $reservations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contracts    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rooms        = new \Doctrine\Common\Collections\ArrayCollection();
        $this->services     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->policies     = new \Doctrine\Common\Collections\ArrayCollection();
        // $this->promotions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Hotel
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
     * Set video_url
     *
     * @param string $video
     * @return Hotel
     */
    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video_url
     *
     * @return string 
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set hotelbedsId
     *
     * @param string $hotelbeds_id
     * @return Hotel
     */
    public function setHotelbedsId($hotelbedsId)
    {
        $this->hotelbedsId = $hotelbedsId;

        return $this;
    }

    /**
     * Get hotelbedsId
     *
     * @return string 
     */
    public function getHotelbedsId()
    {
        return $this->hotelbedsId;
    }


    /**
     * Set enDescription
     *
     * @param string $enDescription
     * @return Hotel
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
     * @return Hotel
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
     * Set enDescription
     *
     * @param string $enVoucherNote
     * @return Hotel
     */
    public function setEnVoucherNote($enVoucherNote)
    {
        $this->enVoucherNote = $enVoucherNote;

        return $this;
    }

    /**
     * Get enVoucherNote
     *
     * @return string 
     */
    public function getEnVoucherNote()
    {
        return $this->enVoucherNote;
    }

    /**
     * Set esVoucherNote
     *
     * @param string $esVoucherNote
     * @return Hotel
     */
    public function setEsVoucherNote($esVoucherNote)
    {
        $this->esVoucherNote = $esVoucherNote;

        return $this;
    }

    /**
     * Get esVoucherNote
     *
     * @return string 
     */
    public function getEsVoucherNote()
    {
        return $this->esVoucherNote;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Hotel
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Hotel
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set region
     *
     * @param string region
     * @return Hotel
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Hotel
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Hotel
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Hotel
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     * @return Hotel
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set checkIn
     *
     * @param \DateTime $checkIn
     * @return Hotel
     */
    public function setCheckIn($checkIn)
    {

        $date = new \DateTime($checkIn);
        $this->checkIn = $date;

        return $this;
    }

    /**
     * Get checkIn
     *
     * @return \DateTime 
     */
    public function getCheckIn()
    {
        if (!$this->checkIn) {
            return '';
        }
        
        return $this->checkIn->format('H:i:s');
    }

    /**
     * Set checkOut
     *
     * @param \DateTime $checkOut
     * @return Hotel
     */
    public function setCheckOut($checkOut)
    {
        $date = new \DateTime($checkOut);
        $this->checkOut = $date;

        return $this;
    }

    /**
     * Get checkOut
     *
     * @return \DateTime 
     */
    public function getCheckOut()
    {

        if (!$this->checkOut) {
            return '';
        }

        return $this->checkOut->format('H:i:s');
    }

    /**
     * Set reservationEmail
     *
     * @param string $reservationEmail
     * @return Hotel
     */
    public function setReservationEmail($reservationEmail)
    {
        $this->reservationEmail = $reservationEmail;

        return $this;
    }

    /**
     * Get reservationEmail
     *
     * @return string 
     */
    public function getReservationEmail()
    {
        return $this->reservationEmail;
    }

    /**
     * Set cancellationEmail
     *
     * @param string $cancellationEmail
     * @return Hotel
     */
    public function setCancellationEmail($cancellationEmail)
    {
        $this->cancellationEmail = $cancellationEmail;

        return $this;
    }

    /**
     * Get cancellationEmail
     *
     * @return string 
     */
    public function getCancellationEmail()
    {
        return $this->cancellationEmail;
    }

    /**
     * Set coords
     *
     * @param string $coords
     * @return Hotel
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return string 
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Hotel
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
     * @return Hotel
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
     * Set phone
     *
     * @param string $phone
     * @return HotelContact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     * @return Hotel
     */
    public function setVip($vip)
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * Get vip
     *
     * @return boolean
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Hotel
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
     * @return Hotel
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
     * Add rooms
     *
     * @param \Gushh\CoreBundle\Entity\Room $rooms
     * @return Hotel
     */
    public function addRoom(\Gushh\CoreBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;

        return $this;
    }

    /**
     * Remove rooms
     *
     * @param \Gushh\CoreBundle\Entity\Room $rooms
     */
    public function removeRoom(\Gushh\CoreBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Add services
     *
     * @param \Gushh\CoreBundle\Entity\HotelService $services
     * @return Hotel
     */
    public function addService(\Gushh\CoreBundle\Entity\HotelService $services)
    {
        $this->services[] = $services;

        return $this;
    }

    /**
     * Remove services
     *
     * @param \Gushh\CoreBundle\Entity\HotelService $services
     */
    public function removeService(\Gushh\CoreBundle\Entity\HotelService $services)
    {
        $this->services->removeElement($services);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServices($type = null)
    {
        $services = $this->services;

        if ($type) {
            $arrayServices = [];
            foreach ($services as $service) {
                $serviceType = $service->getServiceType()->getSlug();
                if ($type == $serviceType) {
                    array_push($arrayServices, $service);
                }
            }

            $services = $arrayServices;
        }

        return $services;
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
     * Get mandatory services
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandatoryServices()
    {
        $finalArray = [];

        foreach ($this->services as $service)
            if ( $service->getServiceType()->getSlug() == 'mandatory')
                array_push($finalArray, $service);

        return $finalArray;
    }

    /**
     * Get mandatory services
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandatoryServicesTotal($pax = 2, $nights = 1)
    {
        $total    = 0;
        $services = $this->getMandatoryServices();

        foreach ($services as $service) {           
            $total = $total + $service->getSalePrice($pax, $nights);
        }

        return round($total, 2);
    }

    /**
     * Add images
     *
     * @param \Gushh\CoreBundle\Entity\HotelImage $images
     * @return Hotel
     */
    public function addImage(\Gushh\CoreBundle\Entity\HotelImage $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Gushh\CoreBundle\Entity\HotelImage $images
     */
    public function removeImage(\Gushh\CoreBundle\Entity\HotelImage $images)
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
     * Add amenities
     *
     * @param \Gushh\CoreBundle\Entity\Amenity $amenities
     * @return Hotel
     */
    public function addAmenity(\Gushh\CoreBundle\Entity\Amenity $amenities)
    {
        $this->amenities[] = $amenities;

        return $this;
    }

    /**
     * Remove amenities
     *
     * @param \Gushh\CoreBundle\Entity\Amenity $amenities
     */
    public function removeAmenity(\Gushh\CoreBundle\Entity\Amenity $amenities)
    {
        $this->amenities->removeElement($amenities);
    }

    /**
     * Get amenities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAmenities()
    {
        return $this->amenities;
    }

    /**
     * Set stars
     *
     * @param integer $stars
     * @return Hotel
     */
    public function setStars($stars)
    {
        $this->stars = $stars;

        return $this;
    }

    /**
     * Get stars
     *
     * @return integer 
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Hotel
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string 
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set childAge
     *
     * @param integer $childAge
     * @return Hotel
     */
    public function setChildAge($childAge)
    {
        $this->childAge = $childAge;

        return $this;
    }

    /**
     * Get childAge
     *
     * @return integer 
     */
    public function getChildAge()
    {
        return $this->childAge;
    }

    /**
     * Set currency
     *
     * @param \Gushh\CoreBundle\Entity\HotelCurrency $currency
     * @return Hotel
     */
    public function setCurrency(\Gushh\CoreBundle\Entity\HotelCurrency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \Gushh\CoreBundle\Entity\HotelCurrency 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add contacts
     *
     * @param \Gushh\CoreBundle\Entity\HotelContact $contacts
     * @return Hotel
     */
    public function addContact(\Gushh\CoreBundle\Entity\HotelContact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Gushh\CoreBundle\Entity\HotelContact $contacts
     */
    public function removeContact(\Gushh\CoreBundle\Entity\HotelContact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Add contracts
     *
     * @param \Gushh\CoreBundle\Entity\Contract $contracts
     * @return Hotel
     */
    public function addContract(\Gushh\CoreBundle\Entity\Contract $contracts)
    {
        $this->contracts[] = $contracts;

        return $this;
    }

    /**
     * Remove contracts
     *
     * @param \Gushh\CoreBundle\Entity\Contract $contracts
     */
    public function removeContract(\Gushh\CoreBundle\Entity\Contract $contracts)
    {
        $this->contracts->removeElement($contracts);
    }

    /**
     * Get contracts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    // /**
    //  * Add promotions
    //  *
    //  * @param \Gushh\CoreBundle\Entity\HotelPromotion $promotions
    //  * @return Hotel
    //  */
    // public function addPromotion(\Gushh\CoreBundle\Entity\HotelPromotion $promotions)
    // {
    //     $this->promotions[] = $promotions;

    //     return $this;
    // }

    // /**
    //  * Remove promotions
    //  *
    //  * @param \Gushh\CoreBundle\Entity\HotelPromotion $promotions
    //  */
    // public function removePromotion(\Gushh\CoreBundle\Entity\HotelPromotion $promotions)
    // {
    //     $this->promotions->removeElement($promotions);
    // }

    // /**
    //  * Get promotions
    //  *
    //  * @return \Doctrine\Common\Collections\Collection 
    //  */
    // public function getPromotions()
    // {
    //     return $this->promotions;
    // }

    /**
     * Set continent
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return Hotel
     */
    public function setContinent()
    { 
        $country = $this->country;
        $continent = Util::getContinent($country);            

        $this->continent = $continent;

        return $this;
    }

    /**
     * Get continent
     *
     * @return string 
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Set cityImage
     *
     * @param \Gushh\CoreBundle\Entity\City $cityImage
     * @return Hotel
     */
    public function setCityImage(\Gushh\CoreBundle\Entity\City $cityImage = null)
    {
        $this->cityImage = $cityImage;

        return $this;
    }

    /**
     * Get cityImage
     *
     * @return \Gushh\CoreBundle\Entity\City 
     */
    public function getCityImage()
    {
        return $this->cityImage;
    }

    /**
     * Add reservations
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservations
     * @return Hotel
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
     * Add policies
     *
     * @param \Gushh\CoreBundle\Entity\CancellationPolicy $policies
     * @return Hotel
     */
    public function addPolicy(\Gushh\CoreBundle\Entity\CancellationPolicy $policies)
    {
        $this->policies[] = $policies;

        return $this;
    }

    /**
     * Remove policies
     *
     * @param \Gushh\CoreBundle\Entity\CancellationPolicy $policies
     */
    public function removePolicy(\Gushh\CoreBundle\Entity\CancellationPolicy $policies)
    {
        $this->policies->removeElement($policies);
    }

    /**
     * Get policies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPolicies()
    {
        return $this->policies;
    }
}

