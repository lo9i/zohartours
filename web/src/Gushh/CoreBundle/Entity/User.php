<?php

namespace Gushh\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\UserRepository")
 */
class User extends BaseUser
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
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_superadmin", type="boolean")
     */
    private $superadmin = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     */
    private $vip = false;

    /**
     * @ORM\ManyToOne(targetEntity="Agency", inversedBy="users")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id", nullable=true)
     */
    protected $agency;

    /**
     * @ORM\OneToMany(targetEntity="Search", mappedBy="user", cascade={"remove"})
     */
    protected $searches;

    /**
     * @ORM\OneToMany(targetEntity="CheckOut", mappedBy="user")
     */
    protected $checkOuts;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="operator")
     */
    protected $reservations;
    

    public function __construct()
    {
        parent::__construct();
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
     * Add searches
     *
     * @param \Gushh\CoreBundle\Entity\Search $searches
     * @return User
     */
    public function addSearch(\Gushh\CoreBundle\Entity\Search $searches)
    {
        $this->searches[] = $searches;

        return $this;
    }

    /**
     * Remove searches
     *
     * @param \Gushh\CoreBundle\Entity\Search $searches
     */
    public function removeSearch(\Gushh\CoreBundle\Entity\Search $searches)
    {
        $this->searches->removeElement($searches);
    }

    /**
     * Get searches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * Set agency
     *
     * @param \Gushh\CoreBundle\Entity\Agency $agency
     * @return User
     */
    public function setAgency(\Gushh\CoreBundle\Entity\Agency $agency = null)
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
     * Set superadmin
     *
     * @param string $superadmin
     * @return User
     */
    public function setSuperadmin($superadmin)
    {
        $this->superadmin = $superadmin;

        return $this;
    }

    /**
     * Get superadmin
     *
     * @return boolean 
     */
    public function getSuperadmin()
    {
        return $this->superadmin;
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
     * Add checkOuts
     *
     * @param \Gushh\CoreBundle\Entity\CheckOut $checkOuts
     * @return User
     */
    public function addCheckOut(\Gushh\CoreBundle\Entity\CheckOut $checkOuts)
    {
        $this->checkOuts[] = $checkOuts;

        return $this;
    }

    /**
     * Remove checkOuts
     *
     * @param \Gushh\CoreBundle\Entity\CheckOut $checkOuts
     */
    public function removeCheckOut(\Gushh\CoreBundle\Entity\CheckOut $checkOuts)
    {
        $this->checkOuts->removeElement($checkOuts);
    }

    /**
     * Get checkOuts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCheckOuts()
    {
        return $this->checkOuts;
    }

    /**
     * Add reservations
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservations
     * @return User
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
}
