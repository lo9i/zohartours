<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

use Gushh\CoreBundle\Classes\Util;

/**
 * Search
 *
 * @ORM\Table(name="search")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\SearchRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Search
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
     * @ORM\Column(name="hotel", type="string", length=255)
     */

    private $hotel = '';
    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city = '';

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255, nullable=true)
     */
    private $state = '';

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="continent", type="string", length=255)
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="search_code", type="string", length=255)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="rooms", type="integer")
     */
    private $rooms = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="nights", type="integer")
     */
    private $nights;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="check_in", type="datetime")
     */
    private $checkIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="check_out", type="datetime")
     */
    private $checkOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="room_1_adults", type="integer")
     */
    private $room1Adults;

    /**
     * @var integer
     *
     * @ORM\Column(name="room_1_children", type="integer")
     */
    private $room1Children;

    /**
     * @var array
     *
     * @ORM\Column(name="room_1_children_age", type="json_array")
     */
    private $room1ChildrenAge;


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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="searches")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="CheckOut", mappedBy="search")
     */
    private $checkOutProcess;

    /**
     * Get id
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
     * Set hotel
     *
     * @param string $hotel
     * @return Search
     */
    public function setHotel($hotel)
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * Get hotel
     *
     * @return string
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Search
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
     * @return Search
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
     * @return Search
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
     * Set continent
     *
     * @return Search
     */
    public function setContinent($continent = null)
    {
        if($continent == null) {
            $country = $this->country;
            $continent = Util::getContinent($country);
        }

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
     * Set country
     *
     * @param string $region
     * @return Search
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set rooms
     *
     * @param integer $rooms
     * @return Search
     */
    public function setRooms($rooms)
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * Get rooms
     *
     * @return integer
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Set checkIn
     *
     * @param \DateTime $checkIn
     * @return Search
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

        return $this->checkIn->format('Y-m-d');
    }

    public function getCheckInDate()
    {
        return $this->checkIn;
    }

    /**
     * Set checkOut
     *
     * @param \DateTime $checkOut
     * @return Search
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
    public function getCheckOutDate()
    {
        return $this->checkOut;
    }

    public function getCheckOut()
    {
        if (!$this->checkOut) {
            return '';
        }

        return $this->checkOut->format('Y-m-d');
    }

    /**
     * Set room1Adults
     *
     * @param integer $room1Adults
     * @return Search
     */
    public function setRoom1Adults($room1Adults)
    {
        $this->room1Adults = $room1Adults;

        return $this;
    }

    /**
     * Get room1Adults
     *
     * @return integer
     */
    public function getRoom1Adults()
    {
        return $this->room1Adults;
    }

    /**
     * Set room1Children
     *
     * @param integer $room1Children
     * @return Search
     */
    public function setRoom1Children($room1Children)
    {
        $this->room1Children = $room1Children;

        return $this;
    }

    /**
     * Get room1Children
     *
     * @return integer
     */
    public function getRoom1Children()
    {
        return $this->room1Children;
    }

    /**
     * Set room1ChildrenAge
     *
     * @param array $room1ChildrenAge
     * @return Search
     */
    public function setRoom1ChildrenAge($room1ChildrenAge)
    {
        if (!is_array($room1ChildrenAge)) {
            $room1ChildrenAge = explode(",", $room1ChildrenAge);
        }

        $this->room1ChildrenAge = $room1ChildrenAge;

        return $this;
    }

    /**
     * Get room1ChildrenAge
     * @param integer $child
     * @return array
     */
    public function getRoom1ChildrenAge($child = null, $limit = null)
    {
        if ($child) {

            foreach ($this->room1ChildrenAge as $key => $age) {
                if ($key <= $child - 1) {
                    return (int) $age;
                }
            }
        } else {
            if ($limit)
                return array_slice($this->room1ChildrenAge, 0, $limit);

            return $this->room1ChildrenAge ? join(',', $this->room1ChildrenAge) : [];
        }
    }

//    /**
//     * Get room1ChildrenAge
//     * @param integer $child
//     * @return array
//     */
//    public function getRoom1ChildrenAge($child = null, $limit = null)
//    {
//        if ($child) {
//
//            foreach ($this->room1ChildrenAge as $key => $age) {
//                if ($key <= $child - 1) {
//                    return (int) $age;
//                }
//            }
//        } else {
//            if ($limit)
//                return array_slice($this->room1ChildrenAge, 0, $limit);
//
//            return $this->room1ChildrenAge;
//        }
//    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Search
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
     * @return Search
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
     * Set user
     *
     * @param \Gushh\CoreBundle\Entity\User $user
     * @return Search
     */
    public function setUser(\Gushh\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Gushh\CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set nights
     *
     * @param integer $nights
     * @return Search
     */
    public function setNights($nights)
    {

        $this->nights = $nights;

        return $this;
    }

    /**
     * Get nights
     *
     * @return integer
     */
    public function getNights()
    {
        return $this->nights;
    }


    /**
     * Set setCode
     * @ORM\PrePersist()
     * @return Search
     */
    public function setCode()
    {

        $this->code = Util::makeCode(null, null, 'auto', 0, 16);

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
     * Set checkOutProcess
     *
     * @param \Gushh\CoreBundle\Entity\CheckOut $checkOutProcess
     * @return Search
     */
    public function setCheckOutProcess(\Gushh\CoreBundle\Entity\CheckOut $checkOutProcess = null)
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
}

