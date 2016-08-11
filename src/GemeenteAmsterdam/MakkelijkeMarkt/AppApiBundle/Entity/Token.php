<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 *
 * @ORM\Entity(repositoryClass="TokenRepository")
 * @ORM\Table()
 */
class Token
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=36, nullable=false)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $uuid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $creationDate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $lifeTime;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", fetch="EAGER")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", nullable=true)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deviceUuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientApp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $clientVersion;

    /**
     * Init
     */
    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return number
     */
    public function getLifeTime()
    {
        return $this->lifeTime;
    }

    /**
     * @param number $lifeTime
     */
    public function setLifeTime($lifeTime)
    {
        $this->lifeTime = $lifeTime;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getDeviceUuid()
    {
        return $this->deviceUuid;
    }

    /**
     * @param string $deviceUuid
     */
    public function setDeviceUuid($deviceUuid = null)
    {
        $this->deviceUuid = $deviceUuid;
    }

    /**
     * @return string
     */
    public function getClientApp()
    {
        return $this->clientApp;
    }

    /**
     * @param string $clientApp
     */
    public function setClientApp($clientApp = null)
    {
        $this->clientApp = $clientApp;
    }

    /**
     * @return string
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * @param string $clientVersion
     */
    public function setClientVersion($clientVersion)
    {
        $this->clientVersion = $clientVersion;
    }
}