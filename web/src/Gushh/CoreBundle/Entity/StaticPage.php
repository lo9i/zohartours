<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * StaticPage
 *
 * @ORM\Table(name="static_page")
 * @ORM\Entity
 */
class StaticPage
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
     * @ORM\Column(name="en_title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $enTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="es_title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $esTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="en_slug", type="string", length=255)
     * @Gedmo\Slug(fields={"enTitle"})
     */
    private $enSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="es_slug", type="string", length=255)
     * @Gedmo\Slug(fields={"esTitle"})
     */
    private $esSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="en_content", type="text")
     * @Assert\NotBlank()
     */
    private $enContent;

    /**
     * @var string
     *
     * @ORM\Column(name="es_content", type="text")
     * @Assert\NotBlank()
     */
    private $esContent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $enabled = true;

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
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    // public function setTranslatableLocale($locale)
    // {
    //     $this->locale = $locale;
    // }

    public function getLocale(){
        return $this->locale;
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
     * Get toString
     *
     * @return string 
     */
    public function __toString()
    {
        if ($this->locale == 'es') {
            return $this->esTitle;
        } else {
            return $this->enTitle;
        }
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        if ($this->locale == 'es') {
            return $this->esContent;
        } else {
            return $this->enContent;
        }
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return StaticPage
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return StaticPage
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
     * @return StaticPage
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
     * Set enTitle
     *
     * @param string $enTitle
     * @return StaticPage
     */
    public function setEnTitle($enTitle)
    {
        $this->enTitle = $enTitle;

        return $this;
    }

    /**
     * Get enTitle
     *
     * @return string 
     */
    public function getEnTitle()
    {
        return $this->enTitle;
    }

    /**
     * Set esTitle
     *
     * @param string $esTitle
     * @return StaticPage
     */
    public function setEsTitle($esTitle)
    {
        $this->esTitle = $esTitle;

        return $this;
    }

    /**
     * Get esTitle
     *
     * @return string 
     */
    public function getEsTitle()
    {
        return $this->esTitle;
    }

    /**
     * Set enSlug
     *
     * @param string $enSlug
     * @return StaticPage
     */
    public function setEnSlug($enSlug)
    {
        $this->enSlug = $enSlug;

        return $this;
    }

    /**
     * Get enSlug
     *
     * @return string 
     */
    public function getEnSlug()
    {
        return $this->enSlug;
    }

    /**
     * Set esSlug
     *
     * @param string $esSlug
     * @return StaticPage
     */
    public function setEsSlug($esSlug)
    {
        $this->esSlug = $esSlug;

        return $this;
    }

    /**
     * Get esSlug
     *
     * @return string 
     */
    public function getEsSlug()
    {
        return $this->esSlug;
    }

    /**
     * Set enContent
     *
     * @param string $enContent
     * @return StaticPage
     */
    public function setEnContent($enContent)
    {
        $this->enContent = $enContent;

        return $this;
    }

    /**
     * Get enContent
     *
     * @return string 
     */
    public function getEnContent()
    {
        return $this->enContent;
    }

    /**
     * Set esContent
     *
     * @param string $esContent
     * @return StaticPage
     */
    public function setEsContent($esContent)
    {
        $this->esContent = $esContent;

        return $this;
    }

    /**
     * Get esContent
     *
     * @return string 
     */
    public function getEsContent()
    {
        return $this->esContent;
    }
}
