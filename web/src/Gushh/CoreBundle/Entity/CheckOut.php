<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CheckOut
 *
 * @ORM\Table(name="check_out")
 * @ORM\Entity
 */
class CheckOut
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
     * @ORM\Column(name="notes", type="string", length=255, nullable=true)
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )     
     */
    private $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $confirmed = false;    

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="checkOuts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Search", inversedBy="checkOutProcess")
     * @ORM\JoinColumn(name="search_id", referencedColumnName="id")
     */
    protected $search;

    /**
     * @ORM\OneToOne(targetEntity="Reservation", inversedBy="checkOutProcess")
     * @ORM\JoinColumn(name="reservation_id", referencedColumnName="id")
     */
    protected $reservation;

    /**
     * @ORM\ManyToMany(targetEntity="Passenger", inversedBy="checkOuts", cascade={"remove", "persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="checkout_passenger",
     *      joinColumns={@ORM\JoinColumn(name="checkout_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="passenger_id", referencedColumnName="id")}
     *      )
     */
    private $guests;

    /**
     * @ORM\ManyToMany(targetEntity="Promotion", fetch="EAGER")
     * @ORM\JoinTable(name="checkout_promotion",
     *      joinColumns={@ORM\JoinColumn(name="checkout_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="promotion_id", referencedColumnName="id")}
     *      )
     */
    private $promotions;

    public function __construct() {
        $this->guests = new ArrayCollection();
        $this->promotions = new ArrayCollection();
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
     * @return CheckOut
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
     * Set notes
     *
     * @param string $notes
     * @return CheckOut
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CheckOut
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
     * @return CheckOut
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
     * @return CheckOut
     */
    public function setUser(User $user = null)
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
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return CheckOut
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function __toString()
    {
        return 'checkout: ' . $this->id;
    }

    /**
     * Set reservation
     *
     * @param \Gushh\CoreBundle\Entity\Reservation $reservation
     * @return CheckOut
     */
    public function setReservation(Reservation $reservation = null)
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
     * Set search
     *
     * @param \Gushh\CoreBundle\Entity\Search $search
     * @return CheckOut
     */
    public function setSearch(Search $search = null)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return \Gushh\CoreBundle\Entity\Search 
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getGuestFullName()
    {
        if($this->guests == null || $this->guests->isEmpty() || $this->guests[0] == null)
        return '';
         return $this->guests[0]->getName() . ' ' . $this->guests[0]->getLastname();
    }

    /**
     * Get guests
     *
     * @return ArrayCollection of \Gushh\CoreBundle\Entity\Passenger
     */
    public function getGuests()
    {
        return $this->guests;
    }

    public function addGuest(Passenger $passenger) {
        $this->guests[] = $passenger;
    }

    public function removeGuest(Passenger $passenger) {
        $this->guests->removeElement($passenger);
    }

    /**
     * Get guests
     *
     * @return ArrayCollection of \Gushh\CoreBundle\Entity\Promotion
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion) {
        $this->promotions[] = $promotion;
    }

    public function removePromotion(Promotion $promotion) {
        $this->promotions->removeElement($promotion);
    }
}
