<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PromotionConditionConditional
 *
 * @ORM\Table(name="promotion_condition_conditional")
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\PromotionConditionConditionalRepository")
 */
class PromotionConditionConditional
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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="group", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity="PromotionCondition", mappedBy="conditional")
     */
    protected $conditions;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    function __toString() {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PromotionConditionConditional
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
     * @return PromotionConditionConditional
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
     * Constructor
     */
    public function __construct()
    {
        $this->conditions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add conditions
     *
     * @param \Gushh\CoreBundle\Entity\PromotionCondition $conditions
     * @return PromotionConditionConditional
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
}
