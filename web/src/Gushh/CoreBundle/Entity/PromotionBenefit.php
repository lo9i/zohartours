<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PromotionBenefit
 *
 * @ORM\Table(name="promotion_benefit")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\PromotionBenefitRepository")
 */
class PromotionBenefit
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
     * @ORM\Column(name="value", type="decimal", type="decimal", precision=15, scale=2)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="numeric",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $value;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cumulative", type="boolean")
     */
    private $cumulative = false;

    /**
     * @var string
     *
     * @ORM\Column(name="eachValue", type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $eachValue = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_limit", type="boolean")
     */
    private $hasLimit = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="limit_value", type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $limitValue = 1;

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
     * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="benefits")
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     */
    protected $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="PromotionBenefitType", inversedBy="benefits")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="PromotionBenefitValueType", inversedBy="benefits")
     * @ORM\JoinColumn(name="value_type_id", referencedColumnName="id")
     */
    protected $valueType;

    /**
     * @ORM\ManyToOne(targetEntity="RoomService", inversedBy="benefits")
     * @ORM\JoinColumn(name="room_service_id", referencedColumnName="id")
     */
    protected $roomService;

    /**
     * @ORM\ManyToOne(targetEntity="HotelService", inversedBy="benefits")
     * @ORM\JoinColumn(name="hotel_service_id", referencedColumnName="id")
     */
    protected $hotelService;



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
        $slug = $this->valueType->getSlug();

        if ($slug == 'net') {
            $value = "$ $this->value";
        } else {
            $value = "$this->value %";
        }

        $type = $this->type;
        $typeGroup = strtolower($this->type->getGroup());

        if ($typeGroup == 'discounts') {
            $discount = $this->type;
        } elseif ($typeGroup == 'hotel-services') {
            $discount = $this->hotelService;
        } elseif ($typeGroup == 'room-services') {
            $discount = $this->roomService;
        }

        $text = "$value off | $discount";

        return $text;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return PromotionBenefit
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PromotionBenefit
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
     * @return PromotionBenefit
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
     * Set promotion
     *
     * @param \Gushh\CoreBundle\Entity\Promotion $promotion
     * @return PromotionBenefit
     */
    public function setPromotion(\Gushh\CoreBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \Gushh\CoreBundle\Entity\Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Set type
     *
     * @param \Gushh\CoreBundle\Entity\PromotionBenefitType $type
     * @return PromotionBenefit
     */
    public function setType(\Gushh\CoreBundle\Entity\PromotionBenefitType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Gushh\CoreBundle\Entity\PromotionBenefitType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set valueType
     *
     * @param \Gushh\CoreBundle\Entity\PromotionBenefitValueType $valueType
     * @return PromotionBenefit
     */
    public function setValueType(\Gushh\CoreBundle\Entity\PromotionBenefitValueType $valueType = null)
    {
        $this->valueType = $valueType;

        return $this;
    }

    /**
     * Get valueType
     *
     * @return \Gushh\CoreBundle\Entity\PromotionBenefitValueType
     */
    public function getValueType()
    {
        return $this->valueType;
    }

    /**
     * Set roomService
     *
     * @param \Gushh\CoreBundle\Entity\RoomService $roomService
     * @return PromotionBenefit
     */
    public function setRoomService(\Gushh\CoreBundle\Entity\RoomService $roomService = null)
    {
        $this->roomService = $roomService;

        return $this;
    }

    /**
     * Get roomService
     *
     * @return \Gushh\CoreBundle\Entity\RoomService
     */
    public function getRoomService()
    {
        return $this->roomService;
    }

    /**
     * Set hotelService
     *
     * @param \Gushh\CoreBundle\Entity\HotelService $hotelService
     * @return PromotionBenefit
     */
    public function setHotelService(\Gushh\CoreBundle\Entity\HotelService $hotelService = null)
    {
        $this->hotelService = $hotelService;

        return $this;
    }

    /**
     * Get hotelService
     *
     * @return \Gushh\CoreBundle\Entity\HotelService
     */
    public function getHotelService()
    {
        return $this->hotelService;
    }

    /**
     * Set cumulative
     *
     * @param boolean $cumulative
     * @return PromotionBenefit
     */
    public function setCumulative($cumulative)
    {
        $this->cumulative = $cumulative;

        return $this;
    }

    /**
     * Get cumulative
     *
     * @return boolean 
     */
    public function getCumulative()
    {
        return $this->cumulative;
    }

    /**
     * Set eachValue
     *
     * @param integer $eachValue
     * @return PromotionBenefit
     */
    public function setEachValue($eachValue)
    {
        $this->eachValue = $eachValue;

        return $this;
    }

    /**
     * Get eachValue
     *
     * @return integer 
     */
    public function getEachValue()
    {
        return $this->eachValue;
    }

    /**
     * Set limitValue
     *
     * @param integer $limitValue
     * @return PromotionBenefit
     */
    public function setLimitValue($limitValue)
    {
        $this->limitValue = $limitValue;

        return $this;
    }

    /**
     * Get limitValue
     *
     * @return integer 
     */
    public function getLimitValue()
    {
        return $this->limitValue;
    }

    /**
     * Set hasLimit
     *
     * @param boolean $hasLimit
     * @return PromotionBenefit
     */
    public function setHasLimit($hasLimit)
    {
        $this->hasLimit = $hasLimit;

        return $this;
    }

    /**
     * Get hasLimit
     *
     * @return boolean 
     */
    public function getHasLimit()
    {
        return $this->hasLimit;
    }

    /**
     * Get is cumulative
     *
     * @return boolean
     */
    public function isCumulative()
    {
        return $this->cumulative;
    }

    /**
     * Get is cumulative
     *
     * @return boolean
     */
    public function hasLimit()
    {
        return $this->hasLimit;
    }
}
