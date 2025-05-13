<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Carbon\Carbon;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\PromotionRepository")
 */
class Promotion
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from", type="datetime")
     */
    private $validFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to", type="datetime")
     */
    private $validTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="period_from", type="datetime")
     */
    private $periodFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="period_to", type="datetime")
     */
    private $periodTo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="combinable", type="boolean")
     */
    private $combinable = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="non_refundable", type="boolean")
     */
    private $nonRefundable = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_monday", type="boolean")
     */
    private $availableMonday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_tuesday", type="boolean")
     */
    private $availableTuesday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_wednesday", type="boolean")
     */
    private $availableWednesday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_thursday", type="boolean")
     */
    private $availableThursday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_friday", type="boolean")
     */
    private $availableFriday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_saturday", type="boolean")
     */
    private $availableSaturday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_Sunday", type="boolean")
     */
    private $availableSunday = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_in_premium_dates", type="boolean")
     */
    private $availableInPremiumDates = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available_in_stop_sell_dates", type="boolean")
     */
    private $availableInStopSellDates = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="cut_off", type="integer")
     */
    private $cutOff = 0;


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
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="promotions")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     */
    protected $room;

    /**
     * @ORM\OneToMany(targetEntity="PromotionCondition", mappedBy="promotion", cascade={"remove"})
     */
    protected $conditions;

    /**
     * @ORM\OneToMany(targetEntity="PromotionBenefit", mappedBy="promotion", cascade={"remove"})
     */
    protected $benefits;

    /**
     * @ORM\OneToMany(targetEntity="PromotionExceptionPeriod", mappedBy="promotion", cascade={"remove", "persist"}, fetch="EAGER")
     */
    protected $exceptions;

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
     * @return Promotion
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
     * @return Promotion
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
     * Set validFrom
     *
     * @param \DateTime $validFrom
     * @return Promotion
     */
    public function setValidFrom($validFrom)
    {
        $validFrom = new \DateTime($validFrom);

        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        if (!$this->validFrom) {
            return '';
        }

        return $this->validFrom->format('Y-m-d H:i:s');
    }

    /**
     * Set validTo
     *
     * @param \DateTime $validTo
     * @return Promotion
     */
    public function setValidTo($validTo)
    {
        $validTo = new Carbon($validTo);

        $this->validTo = $validTo->endOfDay();

        return $this;
    }

    /**
     * Get validTo
     *
     * @return \DateTime
     */
    public function getValidTo()
    {
        if (!$this->validTo) {
            return '';
        }

        return $this->validTo->format('Y-m-d H:i:s');
    }

    /**
     * Set periodFrom
     *
     * @param \DateTime $periodFrom
     * @return Promotion
     */
    public function setPeriodFrom($periodFrom)
    {
        $periodFrom = new \DateTime($periodFrom);

        $this->periodFrom = $periodFrom;

        return $this;
    }

    /**
     * Get periodFrom
     *
     * @return \DateTime
     */
    public function getPeriodFrom()
    {
        if (!$this->periodFrom) {
            return '';
        }

        return $this->periodFrom->format('Y-m-d H:i:s');
    }

    /**
     * Set periodTo
     *
     * @param \DateTime $periodTo
     * @return Promotion
     */
    public function setPeriodTo($periodTo)
    {
        $periodTo = new Carbon($periodTo);

        $this->periodTo = $periodTo->endOfDay();

        return $this;
    }

    /**
     * Get periodTo
     *
     * @return \DateTime
     */
    public function getPeriodTo()
    {
        if (!$this->periodTo) {
            return '';
        }

        return $this->periodTo->format('Y-m-d H:i:s');
    }

    /**
     * Set combinable
     *
     * @param boolean $combinable
     * @return Promotion
     */
    public function setCombinable($combinable)
    {
        $this->combinable = $combinable;

        return $this;
    }

    /**
     * Get combinable
     *
     * @return boolean
     */
    public function getCombinable()
    {
        return $this->combinable;
    }

    /**
     * Set availableInPremiumDates
     *
     * @param boolean $availableInPremiumDates
     * @return Promotion
     */
    public function setAvailableInPremiumDates($availableInPremiumDates)
    {
        $this->availableInPremiumDates = $availableInPremiumDates;

        return $this;
    }

    /**
     * Get availableInPremiumDates
     *
     * @return boolean
     */
    public function getAvailableInPremiumDates()
    {
        return $this->availableInPremiumDates;
    }

    /**
     * Set availableInStopSellDates
     *
     * @param boolean $availableInStopSellDates
     * @return Promotion
     */
    public function setAvailableInStopSellDates($availableInStopSellDates)
    {
        $this->availableInStopSellDates = $availableInStopSellDates;

        return $this;
    }

    /**
     * Get availableInStopSellDates
     *
     * @return boolean
     */
    public function getAvailableInStopSellDates()
    {
        return $this->availableInStopSellDates;
    }

    /**
     * Set cutOff
     *
     * @param integer $cutOff
     * @return Date
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
     * Constructor
     */
    public function __construct()
    {
        $this->conditions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->benefits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->exceptions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Promotion
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
     * @return Promotion
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
     * Add conditions
     *
     * @param \Gushh\CoreBundle\Entity\PromotionCondition $conditions
     * @return Promotion
     */
    public function addCondition(\Gushh\CoreBundle\Entity\PromotionCondition $conditions)
    {
        $this->conditions[] = $conditions;

        return $this;
    }

    /**
     * Remove conditions
     *
     * @param \Gushh\CoreBundle\Entity\PromotionCondition $conditions
     */
    public function removeCondition(\Gushh\CoreBundle\Entity\PromotionCondition $conditions)
    {
        $this->conditions->removeElement($conditions);
    }

    /**
     * Get conditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Get logicConditions
     *
     * @return Condition
     */
    public function getLogicCondition()
    {
        $allConditions = $this->conditions;

        foreach ($allConditions as $condition) :
            $group = $condition->getExpression()->getGroup();

            if ($group === 'logic') :
                return $condition;
            endif;
        endforeach;

        return;
    }

    /**
     * Get comparationConditions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComparationConditions()
    {
        $comparationConditions = [];

        foreach ($this->conditions as $condition) :
            $group = $condition->getExpression()->getGroup();

            if ($group === 'comparation') :
                array_push($comparationConditions, $condition);
            endif;
        endforeach;

        return $comparationConditions;
    }

    /**
     * Add exception
     *
     * @param \Gushh\CoreBundle\Entity\PromotionExceptionPeriod $exceptionPeriod
     * @return Promotion
     */
    public function addException(\Gushh\CoreBundle\Entity\PromotionExceptionPeriod $exceptionPeriod)
    {
        $this->exceptions[] = $exceptionPeriod;

        return $this;
    }

    /**
     * Remove exception
     *
     * @param \Gushh\CoreBundle\Entity\PromotionExceptionPeriod $exceptionPeriod
     */
    public function removeException(\Gushh\CoreBundle\Entity\PromotionExceptionPeriod $exceptionPeriod)
    {
        $this->exceptions->removeElement($exceptionPeriod);
    }

    /**
     * Get exceptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
    /**
     * Set exceptions
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Promotion
     */
    public function setExceptions(\Doctrine\Common\Collections\ArrayCollection $newExceptions = null)
    {
        $this->exceptions = ($newExceptions == null) ? new \Doctrine\Common\Collections\ArrayCollection()
                                : $newExceptions;

        return $this;
    }
    /**
     * Set room
     *
     * @param \Gushh\CoreBundle\Entity\Room $room
     * @return Promotion
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
     * Add benefits
     *
     * @param \Gushh\CoreBundle\Entity\PromotionBenefit $benefits
     * @return Promotion
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
        $allBenefits = $this->benefits;
        $arrayBenefits = [];

        foreach ($allBenefits as $benefit) {
            array_push($arrayBenefits, $benefit);
        }

        return $arrayBenefits;
    }

    /**
     * Set nonRefundable
     *
     * @param boolean $nonRefundable
     * @return Promotion
     */
    public function setNonRefundable($nonRefundable)
    {
        $this->nonRefundable = $nonRefundable;

        return $this;
    }

    /**
     * Get nonRefundable
     *
     * @return boolean
     */
    public function getNonRefundable()
    {
        return $this->nonRefundable;
    }

    /**
     * Set availableMonday
     *
     * @param boolean $availableMonday
     * @return Promotion
     */
    public function setAvailableMonday($availableMonday)
    {
        $this->availableMonday = $availableMonday;

        return $this;
    }

    /**
     * Get availableMonday
     *
     * @return boolean
     */
    public function getAvailableMonday()
    {
        return $this->availableMonday;
    }

    /**
     * Set availableTuesday
     *
     * @param boolean $availableTuesday
     * @return Promotion
     */
    public function setAvailableTuesday($availableTuesday)
    {
        $this->availableTuesday = $availableTuesday;

        return $this;
    }

    /**
     * Get availableTuesday
     *
     * @return boolean
     */
    public function getAvailableTuesday()
    {
        return $this->availableTuesday;
    }

    /**
     * Set availableWednesday
     *
     * @param boolean $availableWednesday
     * @return Promotion
     */
    public function setAvailableWednesday($availableWednesday)
    {
        $this->availableWednesday = $availableWednesday;

        return $this;
    }

    /**
     * Get availableWednesday
     *
     * @return boolean
     */
    public function getAvailableWednesday()
    {
        return $this->availableWednesday;
    }

    /**
     * Set availableThursday
     *
     * @param boolean $availableThursday
     * @return Promotion
     */
    public function setAvailableThursday($availableThursday)
    {
        $this->availableThursday = $availableThursday;

        return $this;
    }

    /**
     * Get availableThursday
     *
     * @return boolean
     */
    public function getAvailableThursday()
    {
        return $this->availableThursday;
    }

    /**
     * Set availableFriday
     *
     * @param boolean $availableFriday
     * @return Promotion
     */
    public function setAvailableFriday($availableFriday)
    {
        $this->availableFriday = $availableFriday;

        return $this;
    }

    /**
     * Get availableFriday
     *
     * @return boolean
     */
    public function getAvailableFriday()
    {
        return $this->availableFriday;
    }

    /**
     * Set availableSaturday
     *
     * @param boolean $availableSaturday
     * @return Promotion
     */
    public function setAvailableSaturday($availableSaturday)
    {
        $this->availableSaturday = $availableSaturday;

        return $this;
    }

    /**
     * Get availableSaturday
     *
     * @return boolean
     */
    public function getAvailableSaturday()
    {
        return $this->availableSaturday;
    }

    /**
     * Set availableSunday
     *
     * @param boolean $availableSunday
     * @return Promotion
     */
    public function setAvailableSunday($availableSunday)
    {
        $this->availableSunday = $availableSunday;

        return $this;
    }

    /**
     * Get availableSunday
     *
     * @return boolean
     */
    public function getAvailableSunday()
    {
        return $this->availableSunday;
    }

    /**
    * Get enabled
    * @return bool
    */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
    * Set enabled
    *
    * @param boolean $enabled
    * @return Promotion
    */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }
    /**
     * Get getAvailableDays
     *
     * @return array
     */
    public function getAvailableDays()
    {
        $days = [];

        if ($this->availableMonday) {
            $days[] = 'Mon';
        }
        if ($this->availableTuesday) {
            $days[] = 'Tue';
        }
        if ($this->availableWednesday) {
            $days[] = 'Wed';
        }
        if ($this->availableThursday) {
            $days[] = 'Thu';
        }
        if ($this->availableFriday) {
            $days[] = 'Fri';
        }
        if ($this->availableSaturday) {
            $days[] = 'Sat';
        }
        if ($this->availableSunday) {
            $days[] = 'Sun';
        }

        return $days;
    }

    public function isCombinable()
    {
        return ($this->getCombinable() == true);
    }

    public function isNonCombinable()
    {
        return ($this->getCombinable() != true);
    }
}
