<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * algo
 */
class algo
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nada;


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
     * Set nada
     *
     * @param string $nada
     * @return algo
     */
    public function setNada($nada)
    {
        $this->nada = $nada;

        return $this;
    }

    /**
     * Get nada
     *
     * @return string 
     */
    public function getNada()
    {
        return $this->nada;
    }
}
