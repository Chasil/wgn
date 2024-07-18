<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Settings
 *
 * @author wojciech przygoda
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="App\SettingsBundle\Repository\SettingsRepository")
 */
class Settings
{
    /**
     * @var int settings id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string page title
     *
     * @ORM\Column(name="pageTitle", type="string", length=255, nullable=true)
     */
    private $pageTitle;

    /**
    * @var string h1
    *
    * @ORM\Column(name="h1", type="string", length=255, nullable=true)
    */
    private $h1;

    /**
     * @var string meta keywords
     *
     * @ORM\Column(name="metaKeywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;
    /**
     * @var string meta description
     *
     * @ORM\Column(name="metaDescription", type="string", length=255, nullable=true)
     */
    private $metaDescription;
    /**
     * @var string meta description
     *
     * @ORM\Column(name="pageDescription", type="text", nullable=true)
     */
    private $pageDescription;
    /**
     * @var string meta description
     *
     * @ORM\Column(name="pageArchiveDescription", type="text", nullable=true)
     */
    private $pageArchiveDescription;
    /**
     * @var string company country
     *
     * @ORM\Column(name="companyCountry", type="string", length=255, nullable=true)
     */
    private $companyCountry;
    /**
     * @var string company zip code
     *
     * @ORM\Column(name="companyZipCode", type="string", length=255, nullable=true)
     */
    private $companyZipCode;
    /**
     * @var string company city
     *
     * @ORM\Column(name="companyCity", type="string", length=255, nullable=true)
     */
    private $companyCity;
    /**
     * @var string company street
     *
     * @ORM\Column(name="companyStreet", type="string", length=255, nullable=true)
     */
    private $companyStreet;
    /**
     * @var string company phone
     *
     * @ORM\Column(name="companyPhone", type="string", length=255, nullable=true)
     */
    private $companyPhone;
    /**
     * @var string company email
     *
     * @ORM\Column(name="companyEmail", type="string", length=255, nullable=true)
     */
    private $companyEmail;
    /**
     * @var string menuBgColor
     *
     * @ORM\Column(name="menuBgColor", type="string", length=7, nullable=true)
     */
    private $menuBgColor;
    /**
     * @var string menuTextColor
     *
     * @ORM\Column(name="menuTextColor", type="string", length=7, nullable=true)
     */
    private $menuTextColor;
    /**
     * @var string searchBgColor
     *
     * @ORM\Column(name="searchBgColor", type="string", length=7, nullable=true)
     */
    private $searchBgColor;
    /**
     * @var string searchTextColor
     *
     * @ORM\Column(name="searchTextColor", type="string", length=7, nullable=true)
     */
    private $searchTextColor;
    /**
     * @var string searchButtonBgColor
     *
     * @ORM\Column(name="searchButtonBgColor", type="string", length=7, nullable=true)
     */
    private $searchButtonBgColor;
    /**
     * @var string searchButtonHoverBgColor
     *
     * @ORM\Column(name="searchButtonHoverBgColor", type="string", length=7, nullable=true)
     */
    private $searchButtonHoverBgColor;
    /**
     * @var string searchButtonTextColor
     *
     * @ORM\Column(name="searchButtonTextColor", type="string", length=7, nullable=true)
     */
    private $searchButtonTextColor;
    /**
     * @var string footerBgColor
     *
     * @ORM\Column(name="footerTextColor", type="string", length=7, nullable=true)
     */
    private $footerTextColor;
    /**
     * @var string footerBgColor
     *
     * @ORM\Column(name="footerBgColor", type="string", length=7, nullable=true)
     */
    private $footerBgColor;
    /**
    * @var string h1Color
    *
    * @ORM\Column(name="h1Color", type="string", length=7, nullable=true)
    */
    private $h1Color;
    /**
     * @var string h1TextColor
     *
     * @ORM\Column(name="h1TextColor", type="string", length=7, nullable=true)
     */
    private $h1TextColor;
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
     * @param string $pageTitle page title
     *
     * @return Settings
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
    * Set h1
    *
    * @param string $h1 page title
    *
    * @return Settings
    */
    public function setH1($h1)
    {
        $this->h1 = $h1;

        return $this;
    }

    /**
    * Get h1
    *
    * @return string
    */
    public function getH1()
    {
        return $this->h1;
    }

    /**
     * Set metaKeyWords
     *
     * @param string $metaKeywords meta keywords
     *
     * @return Settings
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }
    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }
    /**
     * Set metaDescription
     *
     * @param string $metaDescription meta description
     *
     * @return Settings
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }
    /**
     * Get companyCountry
     *
     * @return string
     */
    public function getCompanyCountry() {
        return $this->companyCountry;
    }
    /**
     * Set companyCountry
     *
     * @param string $companyCountry company country
     *
     * @return Settings
     */
    public function setCompanyCountry($companyCountry) {
        $this->companyCountry = $companyCountry;
        return $this;
    }
    /**
     * Get companyZipCode
     *
     * @return string
     */
    public function getCompanyZipCode() {
        return $this->companyZipCode;
    }
    /**
     * Set companyZipCode
     *
     * @param string $companyZipCode company zip code
     *
     * @return Settings
     */
    public function setCompanyZipCode($companyZipCode) {
        $this->companyZipCode = $companyZipCode;
        return $this;
    }
    /**
     * Get companyCity
     *
     * @return string
     */
    public function getCompanyCity() {
        return $this->companyCity;
    }
    /**
     * Set companyCity
     *
     * @param string $companyCity company city
     *
     * @return Settings
     */
    public function setCompanyCity($companyCity) {
        $this->companyCity = $companyCity;
        return $this;
    }
    /**
     * Get companyStreet
     *
     * @return string
     */
    public function getCompanyStreet() {
        return $this->companyStreet;
    }
    /**
     * Set companyStreet
     *
     * @param string $companyStreet company street
     *
     * @return Settings
     */
    public function setCompanyStreet($companyStreet) {
        $this->companyStreet = $companyStreet;
        return $this;
    }
    /**
     * Get companyPhone
     *
     * @return string
     */
    public function getCompanyPhone() {
        return $this->companyPhone;
    }
    /**
     * Set companyPhone
     *
     * @param string $companyPhone company phone
     *
     * @return Settings
     */
    public function setCompanyPhone($companyPhone) {
        $this->companyPhone = $companyPhone;
        return $this;
    }
    /**
     * Get companyEmail
     *
     * @return string
     */
    public function getCompanyEmail() {
        return $this->companyEmail;
    }
    /**
     * Set companyEmail
     *
     * @param string $companyEmail company email
     *
     * @return Settings
     */
    public function setCompanyEmail($companyEmail) {
        $this->companyEmail = $companyEmail;
        return $this;
    }
    /**
     * Get menuBgColor
     *
     * @return string
     */
    public function getMenuBgColor() {
        return $this->menuBgColor;
    }
    /**
     * Get searchBgColor
     *
     * @return string
     */
    public function getSearchBgColor() {
        return $this->searchBgColor;
    }
    /**
     * Get searchButtonBgColor
     *
     * @return string
     */
    public function getSearchButtonBgColor() {
        return $this->searchButtonBgColor;
    }
    /**
     * Set menuBgColor
     *
     * @param string $menuBgColor menu background color
     *
     * @return Settings
     */
    public function setMenuBgColor($menuBgColor) {
        $this->menuBgColor = $menuBgColor;
        return $this;
    }
    /**
     * Set searchBgColor
     *
     * @param string $searchBgColor search background color
     *
     * @return Settings
     */
    public function setSearchBgColor($searchBgColor) {
        $this->searchBgColor = $searchBgColor;
        return $this;
    }
    /**
     * Set searchButtonBgColor
     *
     * @param string $searchButtonBgColor search button background color
     *
     * @return Settings
     */
    public function setSearchButtonBgColor($searchButtonBgColor) {
        $this->searchButtonBgColor = $searchButtonBgColor;
        return $this;
    }
    /**
     * Get footerBgColor
     *
     * @return string
     */
    public function getFooterBgColor() {
        return $this->footerBgColor;
    }
    /**
     * Set footerBgColor
     *
     * @param string $footerBgColor footer background color
     *
     * @return Settings
     */
    public function setFooterBgColor($footerBgColor) {
        $this->footerBgColor = $footerBgColor;
        return $this;
    }
    /**
     * Get footerBgColor
     *
     * @return string
     */
    public function getMenuTextColor() {
        return $this->menuTextColor;
    }
    /**
     * Get footerBgColor
     *
     * @return string
     */
    public function getSearchTextColor() {
        return $this->searchTextColor;
    }
    /**
     * Get footerBgColor
     *
     * @return string
     */
    public function getSearchButtonTextColor() {
        return $this->searchButtonTextColor;
    }
    /**
     * Get footerBgColor
     *
     * @return string
     */
    public function getFooterTextColor() {
        return $this->footerTextColor;
    }
    /**
     * Set menuTextColor
     *
     * @param string $menuTextColor footer background color
     *
     * @return Settings
     */
    public function setMenuTextColor($menuTextColor) {
        $this->menuTextColor = $menuTextColor;
        return $this;
    }
    /**
     * Set searchTextColor
     *
     * @param string $searchTextColor footer background color
     *
     * @return Settings
     */
    public function setSearchTextColor($searchTextColor) {
        $this->searchTextColor = $searchTextColor;
        return $this;
    }
    /**
     * Set searchButtonTextColor
     *
     * @param string $searchButtonTextColor footer background color
     *
     * @return Settings
     */
    public function setSearchButtonTextColor($searchButtonTextColor) {
        $this->searchButtonTextColor = $searchButtonTextColor;
        return $this;
    }
    /**
     * Set footerTextColor
     *
     * @param string $footerTextColor footer text color
     *
     * @return Settings
     */
    public function setFooterTextColor($footerTextColor) {
        $this->footerTextColor = $footerTextColor;
        return $this;
    }
    public function getSearchButtonHoverBgColor() {
        return $this->searchButtonHoverBgColor;
    }

    public function setSearchButtonHoverBgColor($searchButtonHoverBgColor) {
        $this->searchButtonHoverBgColor = $searchButtonHoverBgColor;
        return $this;
    }
    /**
    * Get h1Color
    *
    * @return string
    */
    public function getH1Color() {
        return $this->h1Color;
    }
    /**
    * Set h1Color
    *
    * @param string $h1Color h1 background color
    * @return Settings
    */
    public function setH1Color($h1Color) {
        $this->h1Color = $h1Color;
        return $this;
    }

    /**
     * Get h1TextColor
     *
     * @return string
     */
    public function getH1TextColor() {
        return $this->h1TextColor;
    }

    /**
     * Set h1TextColor
     *
     * @param string $h1TextColor h1 background color
     *
     * @return Settings
     */
    public function setH1TextColor($h1TextColor) {
        $this->h1TextColor = $h1TextColor;
        return $this;
    }

    public function getPageDescription() {
        return $this->pageDescription;
    }

    public function setPageDescription($pageDescription) {
        $this->pageDescription = $pageDescription;
        return $this;
    }

    /**
     * @return string
     */
    public function getPageArchiveDescription()
    {
        return $this->pageArchiveDescription;
    }

    /**
     * @param string $pageArchiveDescription
     * @return Settings
     */
    public function setPageArchiveDescription(string $pageArchiveDescription)
    {
        $this->pageArchiveDescription = $pageArchiveDescription;

        return $this;
    }

    /**
     * Variables to array
     *
     * @return array
     */
    public function toArray(){
        return get_object_vars($this);
    }
}

