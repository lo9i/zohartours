<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ServicePayType
 *
 * @ORM\Table(name="service_pay_type")
 * @ORM\Entity
 */
class ServicePayType
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
     */
    private $name;

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
     * @ORM\OneToMany(targetEntity="HotelService", mappedBy="servicePayType")
     */
    protected $hotelServices;

    /**
     * @ORM\OneToMany(targetEntity="RoomService", mappedBy="servicePayType")
     */
    protected $roomServices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hotelServices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roomServices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ServicePayType
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
     * Set slug
     *
     * @param string $slug
     * @return ServicePayType
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
     * @return ServicePayType
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
     * @return ServicePayType
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
     * Add hotelServices
     *
     * @param \Gushh\CoreBundle\Entity\HotelService $hotelServices
     * @return ServicePayType
     */
    public function addHotelService(\Gushh\CoreBundle\Entity\HotelService $hotelServices)
    {
        $this->hotelServices[] = $hotelServices;

        return $this;
    }

    /**
     * Remove hotelServices
     *
     * @param \Gushh\CoreBundle\Entity\HotelService $hotelServices
     */
    public function removeHotelService(\Gushh\CoreBundle\Entity\HotelService $hotelServices)
    {
        $this->hotelServices->removeElement($hotelServices);
    }

    /**
     * Get hotelServices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHotelServices()
    {
        return $this->hotelServices;
    }

    /**
     * Add roomServices
     *
     * @param \Gushh\CoreBundle\Entity\RoomService $roomServices
     * @return ServicePayType
     */
    public function addRoomService(\Gushh\CoreBundle\Entity\RoomService $roomServices)
    {
        $this->roomServices[] = $roomServices;

        return $this;
    }

    /**
     * Remove roomServices
     *
     * @param \Gushh\CoreBundle\Entity\RoomService $roomServices
     */
    public function removeRoomService(\Gushh\CoreBundle\Entity\RoomService $roomServices)
    {
        $this->roomServices->removeElement($roomServices);
    }

    /**
     * Get roomServices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoomServices()
    {
        return $this->roomServices;
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
}
