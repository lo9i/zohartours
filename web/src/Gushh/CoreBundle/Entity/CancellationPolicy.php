<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CancellationPolicy
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\CancellationPolicyRepository")
 */
class CancellationPolicy
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
     * @var integer
     *
     * @ORM\Column(name="cut_off", type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * ) 
     */
    private $cutOff = 7;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalty", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )      
     */
    private $penalty;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="policies")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     */
    protected $hotel;

    /**
     * @ORM\OneToMany(targetEntity="Date", mappedBy="cancellationPolicy")
     */
    protected $dates;

    /**
     * @ORM\ManyToOne(targetEntity="CancellationPolicyType", inversedBy="cancellationPolicies")
     * @ORM\JoinColumn(name="cancellation_policy_type_id", referencedColumnName="id")
     */
    protected $cancellationPolicyType;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cutOff
     *
     * @param integer $cutOff
     * @return CancellationPolicy
     */
    public function setCutOff($cutOff)
    {
        $this->cutOff = $cutOff;

        return $this;
    }

    /**
     * Get cutOff
     *
     * @return integer 
     */
    public function getCutOff()
    {
        return $this->cutOff;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CancellationPolicy
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
     * @return CancellationPolicy
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
     * @return CancellationPolicy
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
     * Constructor
     */
    public function __construct()
    {
        $this->dates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add dates
     *
     * @param \Gushh\CoreBundle\Entity\Date $dates
     * @return CancellationPolicy
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
     * Set cancellationPolicyType
     *
     * @param \Gushh\CoreBundle\Entity\CancellationPolicyType $cancellationPolicyType
     * @return CancellationPolicy
     */
    public function setCancellationPolicyType(\Gushh\CoreBundle\Entity\CancellationPolicyType $cancellationPolicyType = null)
    {
        $this->cancellationPolicyType = $cancellationPolicyType;

        return $this;
    }

    /**
     * Get cancellationPolicyType
     *
     * @return \Gushh\CoreBundle\Entity\CancellationPolicyType 
     */
    public function getCancellationPolicyType()
    {
        return $this->cancellationPolicyType;
    }

    /**
     * Set penalty
     *
     * @param string $penalty
     * @return CancellationPolicy
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;

        return $this;
    }

    /**
     * Get penalty
     *
     * @return string 
     */
    public function getPenalty()
    {
        return $this->penalty;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CancellationPolicy
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
     * To string function
     *
     * @return string 
     */
    public function __toString()
    {
        return $this->name;
    }
}
