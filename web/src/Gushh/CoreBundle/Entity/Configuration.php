<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gushh\CoreBundle\Entity\ConfigurationRepository")
 */
class Configuration
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
     * @ORM\Column(name="invoice_header", type="text")
     */
    private $invoiceHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_footer", type="text")
     */
    private $invoiceFooter;


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
     * Set invoiceHeader
     *
     * @param string $invoiceHeader
     * @return Configuration
     */
    public function setInvoiceHeader($invoiceHeader)
    {
        $this->invoiceHeader = $invoiceHeader;

        return $this;
    }

    /**
     * Get invoiceHeader
     *
     * @return string 
     */
    public function getInvoiceHeader()
    {
        return $this->invoiceHeader;
    }

    /**
     * Set invoiceFooter
     *
     * @param string $invoiceFooter
     * @return Configuration
     */
    public function setInvoiceFooter($invoiceFooter)
    {
        $this->invoiceFooter = $invoiceFooter;

        return $this;
    }

    /**
     * Get invoiceFooter
     *
     * @return string 
     */
    public function getInvoiceFooter()
    {
        return $this->invoiceFooter;
    }
}
