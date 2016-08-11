<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author maartendekeizer
 * @copyright Gemeente Amsterdam, Datalab
 *
 * @ORM\Entity(repositoryClass="SollicitatieRepository")
 * @ORM\Table(indexes={
 *  @ORM\Index(name="sollicitatieSollicitatieNummer", columns={"sollicitatie_nummer"}),
 *  @ORM\Index(name="sollicitatieMarktSollicitatieNummer", columns={"markt_id", "sollicitatie_nummer"}),
 *  @ORM\Index(name="sollicitatiePerfectViewNumber", columns={"perfect_view_nummer"})
 * })
 */
class Sollicitatie
{
    /**
     * @var string
     */
    const STATUS_SOLL = 'soll';

    /**
     * @var string
     */
    const STATUS_VPL = 'vpl';

    /**
     * @var string
     */
    const STATUS_VKK = 'vkk';

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Markt
     * @ORM\ManyToOne(targetEntity="Markt", fetch="LAZY", inversedBy="sollicitaties")
     * @ORM\JoinColumn(name="markt_id", referencedColumnName="id", nullable=false)
     */
    private $markt;

    /**
     * @var Koopman
     * @ORM\ManyToOne(targetEntity="Koopman", fetch="LAZY", inversedBy="sollicitaties")
     * @ORM\JoinColumn(name="koopman_id", referencedColumnName="id", nullable=false)
     */
    private $koopman;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sollicitatieNummer;

    /**
     * @var string
     * @ORM\Column(type="string", length=4, nullable=false)
     */
    private $status;

    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $vastePlaatsen;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=true, name="aantal_3meter_kramen")
     */
    private $aantal3MeterKramen;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=true, name="aantal_4meter_kramen")
     */
    private $aantal4MeterKramen;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalExtraMeters;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aantalElektra;

    /**
     * @var number
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $aantalAfvaleilanden;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $krachtstroom;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inschrijfDatum;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $doorgehaald;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $doorgehaaldReden;

    /**
     * @var number
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfectViewNummer;

    /**
     * Init
     */
    public function __construct()
    {
        $this->doorgehaald = false;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt
     */
    public function getMarkt()
    {
        return $this->markt;
    }

    /**
     * @param Markt $markt
     */
    public function setMarkt(Markt $markt = null)
    {
        $this->markt = $markt;
    }

    /**
     * @return \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Koopman
     */
    public function getKoopman()
    {
        return $this->koopman;
    }

    /**
     * @param Koopman $koopman
     */
    public function setKoopman(Koopman $koopman = null)
    {
        $this->koopman = $koopman;
    }

    /**
     * @return number
     */
    public function getSolliciatieNummer()
    {
        return $this->sollicitatieNummer;
    }

    /**
     * @param number $sollicitatieNummer
     */
    public function setSollicitatieNummer($sollicitatieNummer)
    {
        $this->sollicitatieNummer = $sollicitatieNummer;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (in_array($status, [self::STATUS_SOLL, self::STATUS_VKK, self::STATUS_VPL]) === false)
            throw new \InvalidArgumentException();
        $this->status = $status;
    }

    /**
     * @return string[]
     */
    public function getVastePlaatsen()
    {
        if (count($this->vastePlaatsen) === 1 && $this->vastePlaatsen[0] === '')
            return [];
        return $this->vastePlaatsen;
    }

    /**
     * @param string[] $vastePlaatsen
     */
    public function setVastePlaatsen(array $vastePlaatsen)
    {
        $this->vastePlaatsen = $vastePlaatsen;
    }

    /**
     * @return number
     */
    public function getAantal3MeterKramen()
    {
        return $this->aantal3MeterKramen;
    }

    /**
     * @param number $aantal3MeterKramen
     */
    public function setAantal3MeterKramen($aantal3MeterKramen)
    {
        $this->aantal3MeterKramen = $aantal3MeterKramen;
    }

    /**
     * @return number
     */
    public function getAantal4MeterKramen()
    {
        return $this->aantal4MeterKramen;
    }

    /**
     * @param number $aantal4MeterKramen
     */
    public function setAantal4MeterKramen($aantal4MeterKramen)
    {
        $this->aantal4MeterKramen = $aantal4MeterKramen;
    }

    /**
     * @return number
     */
    public function getAantalExtraMeters()
    {
        return $this->aantalExtraMeters;
    }

    /**
     * @param number $aantalExtraMeters
     */
    public function setAantalExtraMeters($aantalExtraMeters)
    {
        $this->aantalExtraMeters = $aantalExtraMeters;
    }

    /**
     * @return number
     */
    public function getAantalElektra()
    {
        return $this->aantalElektra;
    }

    /**
     * @param number $aantalElektra
     */
    public function setAantalElektra($aantalElektra)
    {
        $this->aantalElektra = $aantalElektra;
    }

    /**
     * @return boolean
     */
    public function getKrachtstroom()
    {
        return $this->krachtstroom;
    }

    /**
     * @param boolean $krachtstroom
     */
    public function setKrachtstroom($krachtstroom)
    {
        $this->krachtstroom = $krachtstroom;
    }

    /**
     * @return \DateTime
     */
    public function getInschrijfDatum()
    {
        return $this->inschrijfDatum;
    }

    /**
     * @param \DateTime $inschrijfDatum
     */
    public function setInschrijfDatum(\DateTime $inschrijfDatum)
    {
        $this->inschrijfDatum = $inschrijfDatum;
    }

    /**
     * @return boolean
     */
    public function getDoorgehaald()
    {
        return $this->doorgehaald;
    }

    /**
     * @param boolean $doorgehaald
     */
    public function setDoorgehaald($doorgehaald)
    {
        $this->doorgehaald = $doorgehaald;
    }

    /**
     * @return string
     */
    public function getDoorgehaaldReden()
    {
        return $this->doorgehaaldReden;
    }

    /**
     * @param string $doorgehaaldReden
     */
    public function setDoorgehaaldReden($doorgehaaldReden)
    {
        $this->doorgehaaldReden = $doorgehaaldReden;
    }

    /**
     * @return number
     */
    public function getPerfectViewNummer()
    {
        return $this->perfectViewNummer;
    }

    /**
     * @param number $perfectViewNummer
     */
    public function setPerfectViewNummer($perfectViewNummer)
    {
        $this->perfectViewNummer = $perfectViewNummer;
    }

    /**
     * Get sollicitatieNummer
     *
     * @return integer
     */
    public function getSollicitatieNummer()
    {
        return $this->sollicitatieNummer;
    }

    /**
     * Set aantalAfvaleilanden
     *
     * @param integer $aantalAfvaleilanden
     *
     * @return Sollicitatie
     */
    public function setAantalAfvaleilanden($aantalAfvaleilanden)
    {
        $this->aantalAfvaleilanden = $aantalAfvaleilanden;

        return $this;
    }

    /**
     * Get aantalAfvaleilanden
     *
     * @return integer
     */
    public function getAantalAfvaleilanden()
    {
        return $this->aantalAfvaleilanden;
    }
}