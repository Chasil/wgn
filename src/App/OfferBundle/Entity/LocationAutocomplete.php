<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LocationAutocomplete
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="location_autocomplete",indexes={
 *     @ORM\Index(columns={"name"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"province"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"district"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"city"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"section"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"subsection"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\OfferBundle\Entity\LocationAutocompleteRepository")
 */
class LocationAutocomplete
{
    /**
     * @var int Location autocomplete id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string province
     *
     * @ORM\Column(name="province", type="string", length=255,nullable=true)
     */
    private $province;
    /**
     * @var string district
     *
     * @ORM\Column(name="district", type="string", length=255,nullable=true)
     */
    private $district;
    /**
     * @var string city
     *
     * @ORM\Column(name="city", type="string", length=255,nullable=true)
     */
    private $city;
    /**
     * @var string uniqueKey
     *
     * @ORM\Column(name="uniqueKey", type="string", length=255,nullable=true)
     */
    private $uniqueKey;
    /**
     * @var string oldUniqueKey
     *
     * @ORM\Column(name="oldUniqueKey", type="string", length=255,nullable=true)
     */
    private $oldUniqueKey;
    /**
     * @var string section
     *
     * @ORM\Column(name="section", type="string", length=255,nullable=true)
     */
    private $section;
    /**
     * @var string subsection
     *
     * @ORM\Column(name="subsection", type="string", length=255,nullable=true)
     */
    private $subsection;
    /**
     * @var string street
     *
     * @ORM\Column(name="street", type="string", length=255,nullable=true)
     */
    private $street;
    /**
     * @var string is main
     *
     * @ORM\Column(name="isMain", type="string", length=255,nullable=true)
     */
    private $isMain;
    /**
     * @var string lat
     *
     * @ORM\Column(name="lat", type="string", length=255,nullable=true)
     */
    private $lat;

    /**
     * @var string lng
     *
     * @ORM\Column(name="lng", type="string", length=255,nullable=true)
     */
    private $lng;


    /**
     * @var int
     * @ORM\Column(name="display_counter",type="integer", nullable=true)
     */
    private $displayCounter;

    public function __construct()
    {
        $this->displayCounter = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name name
     *
     * @return LocationIndex
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
     * Set lat
     *
     * @param string $lat lat
     *
     * @return LocationIndex
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng lng
     *
     * @return LocationIndex
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }
    /**
     * Get province
     *
     * @return string
     */
    public function getProvince() {
        return $this->province;
    }
    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict() {
        return $this->district;
    }
    /**
     * Get city
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }
    /**
     * Get section
     *
     * @return string
     */
    public function getSection() {
        return $this->section;
    }
    /**
     * Get street
     *
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }
    /**
     * Set province
     *
     * @param string $province province
     *
     * @return LocationIndex
     */
    public function setProvince($province) {
        $this->province = $province;
        return $this;
    }
    /**
     * Set district
     *
     * @param string $district district
     *
     * @return LocationIndex
     */
    public function setDistrict($district) {
        $this->district = $district;
        return $this;
    }
    /**
     * Set city
     *
     * @param string $city city
     *
     * @return LocationIndex
     */
    public function setCity($city) {
        $this->city = $city;
        return $this;
    }
    /**
     * Set section
     *
     * @param string $section section
     *
     * @return LocationIndex
     */
    public function setSection($section) {
        $this->section = $section;
        return $this;
    }
    /**
     * Set street
     *
     * @param string $screet street
     *
     * @return LocationIndex
     */
    public function setStreet($screet) {
        $this->street = $screet;
        return $this;
    }
    /**
     * Get subsection
     *
     * @return string
     */
    public function getSubsection() {
        return $this->subsection;
    }
    /**
     * Set subsection
     *
     * @param string $subsection subsection
     *
     * @return LocationIndex
     */
    public function setSubsection($subsection) {
        $this->subsection = $subsection;
        return $this;
    }
    /**
     * Get isMain
     *
     * @return string
     */
    public function getIsMain() {
        return $this->isMain;
    }
    /**
     * Set isMain
     *
     * @param string $isMain is main
     *
     * @return LocationIndex
     */
    public function setIsMain($isMain) {
        $this->isMain = $isMain;
        return $this;
    }
    public function getUniqueKey() {
        return $this->uniqueKey;
    }

    public function setUniqueKey($uniqueKey) {
        $this->uniqueKey = $uniqueKey;
        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayCounter(): int
    {
        return $this->displayCounter;
    }

    /**
     * @param int $displayCounter
     * @return LocationAutocomplete
     */
    public function setDisplayCounter(int $displayCounter): LocationAutocomplete
    {
        $this->displayCounter = $displayCounter;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}

