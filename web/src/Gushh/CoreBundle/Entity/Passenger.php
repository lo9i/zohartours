<?php


namespace Gushh\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 *
 * @ORM\Table(name="passenger")
 * @ORM\Entity
 */
class Passenger
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime")
     */
    private $birthDate;


    /**
     * @ORM\ManyToOne(targetEntity="PassengerTitle")
     * @ORM\JoinColumn(name="title_id", referencedColumnName="id")
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Agency")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     */
    protected $agency;

    /**
     * @ORM\ManyToMany(targetEntity="CheckOut", mappedBy="guests")
     */
    protected $checkOuts;


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


    public function __construct()
    {
        $this->checkOuts = new ArrayCollection();
    }

    /**
     * To string function
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set agency
     *
     * @param Agency $agency
     * @return User
     */
    public function setAgency(Agency $agency = null)
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * Get agency
     *
     * @return \Gushh\CoreBundle\Entity\Agency
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return Search
     */
    public function setBirthDate($birthDate)
    {
        $date = new \DateTime($birthDate);
        $this->birthDate = $date;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        if (!$this->birthDate) {
            return '';
        }

        return $this->birthDate->format('Y-m-d');
    }

    /**
     * Set guestTitle
     *
     * @param \Gushh\CoreBundle\Entity\PassengerTitle $guestTitle
     * @return CheckOut
     */
    public function setTitle(PassengerTitle $title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get guestTitle
     *
     * @return \Gushh\CoreBundle\Entity\PassengerTitle
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PassengerTitle
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
     * @return PassengerTitle
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


}

