<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Article
 *
 * @author wojciech przygoda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\ArticleBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    /**
     * @var integer article id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string legacy id
     *
     * @ORM\Column(name="legacyId", type="string", length=255,nullable=true)
     */
    private $legacyId;
    /**
     * @var string legacy category
     *
     * @ORM\Column(name="legacyCategory", type="string", length=255,nullable=true)
     */
    private $legacyCategory;
    /**
     * @var string name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var string name
     *
     * @ORM\Column(name="meta_title", type="string", length=255,nullable=true)
     */
    private $metaTitle;
    /**
     * @var string name
     *
     * @ORM\Column(name="slug", type="string", length=255,nullable=true)
     */
    private $slug;

    /**
     * @var string meta description
     *
     * @ORM\Column(name="metaDesc", type="string", length=255,nullable=true)
     */
    private $metaDesc;

    /**
     * @var string meta keywords
     *
     * @ORM\Column(name="metaKeywords", type="string", length=255,nullable=true)
     */
    private $metaKeywords;
    /**
     * @var string intro
     *
     * @ORM\Column(name="intro", type="text",nullable=true)
     */
    private $intro;
    /**
     * @var string content
     *
     * @ORM\Column(name="content", type="text",nullable=true)
     */
    private $content;
    /**
     * @var boolean is url
     *
     * @ORM\Column(name="isUrl", type="boolean")
     */
    private $isUrl;
    /**
     * @var string url
     *
     * @ORM\Column(name="url", type="string", length=255,nullable=true)
     */
    private $url;
    /**
     * @var string source
     *
     * @ORM\Column(name="source", type="string", length=255,nullable=true)
     */
    private $source;
    /**
     * @var datetime create date
     *
     * @ORM\Column(name="createDate", type="datetime")
     */
    private $createDate;
    /**
     * @var datetime publish date
     *
     * @ORM\Column(name="publishDate", type="datetime")
     */
    private $publishDate;
    /**
     * @var boolean is publish
     *
     * @ORM\Column(name="isPublish", type="boolean")
     */
    private $isPublish;
    /**
     * @var boolean is publish on main page
     *
     * @ORM\Column(name="isPublishOnMain", type="boolean",nullable=true)
     */
    private $isPublishOnMain;
    /**
     * @var integer ordering
     *
     * @ORM\Column(name="ordering", type="integer")
     */
    private $ordering;
    /**
     * @var ArticleCategory article category
     *
     * @ORM\ManyToOne(targetEntity="ArticleCategory", inversedBy="articles")
     */

    protected $category;
    /**
     * @var Blog blog
     *
     * @ORM\ManyToOne(targetEntity="Blog", inversedBy="articles")
     */

    protected $blog;
    /**
     * @var User owner
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $owner;
    /**
     * @var string main photo name
     *
     * @ORM\Column(name="mainPhoto", type="string", length=255,nullable=true)
     */
    private $mainPhoto;
    /**
     * @var ArticleImage[] collection of article images
     *
     * @ORM\OneToMany(targetEntity="ArticleImage", mappedBy="article",cascade={"persist", "remove"})
     * @ORM\OrderBy({"ordering" = "DESC"})
     */
    protected $images;
    /**
     *
     * @var Tag collection of tags
     *
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles",cascade={"persist"})
     * @ORM\JoinTable(name="article_tags",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;
     /**
     * @var UploadedFile image file
     * @Assert\File( maxSize = "10240k", mimeTypesMessage = "Please upload a valid Image")
     */
    private $file;
    /**
     *
     * @var string tmp file name
     */
    private $tmp;

    /**
     *
     * @var array list of changes
     */
    private $changes = array();

    /**
     * @var boolean disallow robots
     *
     * @ORM\Column(name="disallowRobots", type="boolean")
     */
    private $disallowRobots;
    /**
     * @var string
     *
     * @ORM\Column(name="followAttribute", type="string", length=255)
     */
    private $followAttribute;

    /**
     * Constructor
     */
    function __construct() {
        $this->createDate = new \DateTime();
        $this->publishDate = new \DateTime();
        $this->isPublish = true;
        $this->isUrl = false;
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
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
     * Get legacyId
     *
     * @return string
     */
    public function getLegacyId() {
        return $this->legacyId;
    }
    /**
     * Set legacyId
     *
     * @param string $legacyId article legacy id
     * @return Article
     */
    public function setLegacyId($legacyId) {
        $this->legacyId = $legacyId;
        return $this;
    }

    /**
     * Get legacyId
     *
     * @return string
     */
    public function getLegacyCategory() {
        return $this->legacyCategory;
    }

    /**
     * Set legacyCategory
     *
     * @param string $legacyCategory article legacy category
     * @return Article
     */
    public function setLegacyCategory($legacyCategory) {
        $this->legacyCategory = $legacyCategory;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name article name
     * @return Article
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set metaDesc
     *
     * @param string $metaDesc article meta description
     * @return Article
     */
    public function setMetaDesc($metaDesc)
    {
        $this->metaDesc = $metaDesc;

        return $this;
    }

    /**
     * Get metaDesc
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->metaDesc;
    }

    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords article meta keywords
     * @return Article
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

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
     * Set content
     *
     * @param string $content article content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set source
     *
     * @param string $source article cource
     * @return Article
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $source;
    }

    /**
     * Get source
     *
     * @return string article source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get intro
     *
     * @return string intro
     */
    public function getIntro() {
        return $this->intro;
    }

    /**
     * Set intro
     *
     * @param string $intro intro
     * @return Article
     */
    public function setIntro($intro) {
        $this->intro = $intro;
        return $this;
    }

    /**
     * Get category
     *
     * @return ArticleCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param ArticleCategory $category article cateogty
     * @return Article
     */
    public function setCategory($category) {
        $this->changes['category'][] = $this->category;
        $this->changes['category'][] = $category;
        $this->category = $category;
        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime create date
     */
    public function getCreateDate() {
        return $this->createDate;
    }

    /**
     * Get publishDate
     *
     * @return \DateTime publish date
     */
    public function getPublishDate() {
        return $this->publishDate;
    }

    /**
     * Set createDate
     *
     * @param \DateTime  $createDate create date
     * @return Article
     */
    public function setCreateDate($createDate) {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate publish date
     * @return Article
     */
    public function setPublishDate($publishDate) {
        $this->publishDate = $publishDate;
        return $this;
    }

    /**
     * Get mainPhoto
     *
     * @return string main photo name
     */
    public function getMainPhoto() {
        return $this->mainPhoto;
    }

    /**
     * Set mainPhoto
     *
     * @param string $mainPhoto main photo name
     * @return Article
     */
    public function setMainPhoto($mainPhoto) {
        $this->mainPhoto = $mainPhoto;
        return $this;
    }

    /**
     * Get isPublish
     *
     * @return string $isPublish is publish
     */
    public function getIsPublish() {
        return $this->isPublish;
    }

    /**
     * Set name
     *
     * @param bool $isPublish
     * @return Article
     */
    public function setIsPublish($isPublish) {
        $this->isPublish = $isPublish;
        return $this;
    }

    /**
     * Get ordering
     *
     * @return int
     */
    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * Set ordering
     *
     * @param int $ordering ordering
     * @return Article
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;
        return $this;
    }

    /**
     * Get isPublishOnMain
     *
     * @return bool
     */
    public function getIsPublishOnMain() {
        return $this->isPublishOnMain;
    }

    /**
     * Set isPublishOnMain
     *
     * @param bool $isPublishOnMain is publish on main page
     * @return Article
     */
    public function setIsPublishOnMain($isPublishOnMain) {
        $this->isPublishOnMain = $isPublishOnMain;
        return $this;
    }


    /**
     * Get images
     *
     * @return ArticleImages[]
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Set images
     *
     * @param ArticleImages[] $images article gallery
     * @return Article
     */
    public function setImages($images) {
        $this->images = $images;
        return $this;
    }

    /**
     * Get tags
     *
     * @return Tag[]
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param Tag[] $tags article tags
     * @return Article
     */
    public function setTags($tags) {
        $this->tags = $tags;
        return $this;
    }
    /**
     * Add tag to article
     *
     * @param Tag $tag article tag
     */
    public function addTag(Tag $tag)
    {
        $tag->addArticle($this); // synchronously updating inverse side
        $this->tags[] = $tag;
    }
    /**
     * Remove tag from article
     *
     * @param Tag $tag article tag
     */
    public function removeTag($tag) {
      $this->tags->removeElement($tag);
    }

    /**
     * Get isUrl
     *
     * @return bool
     */
    public function getIsUrl() {
        return $this->isUrl;
    }

    /**
     * Set isUrl
     *
     * @param bool $isUrl is article
     * @return Article
     */
    public function setIsUrl($isUrl) {
        $this->isUrl = $isUrl;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url article url
     * @return Article
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * Set owner
     *
     * @param User $owner
     * @return Article
     */
    public function setOwner($owner) {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     * @return Article
     */
    public function setFile(UploadedFile $file = null) {

        $this->file = $file;

        $this->tmp = $this->mainPhoto;
        $this->setMainPhoto(uniqid());

    }
    /**
     * Increment ordering
     *
     * @return Article
     */
    public function incrementOrdering(){
        $this->ordering++;
        return $this;
    }

    /**
     * Decrement ordering
     * @return Article
     */
    public function decrementOrdering(){
        $this->ordering--;
        return $this;
    }

    /**
     *
     * Check if value of field is changed
     *
     * @param string $field
     * @return bool
     */
    public function isChange($field){
        return isset($this->changes[$field]);
    }

    /**
     * Get changes
     *
     * @return array
     */
    public function getChanges(){
        return $this->changes;
    }

    /**
     * Get full image path
     *
     * @return null|string
     */
    public function getFullImagePath() {
        return null === $this->mainPhoto ? null : $this->getUploadRootDir(). $this->mainPhoto;
    }

    /**
     * Get upload root dir
     *
     * @return string
     */
    protected function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return $this->getTmpUploadRootDir().$this->getId()."/";
    }

    /**
     * Get tmp upload root dir
     *
     * @return string
     */
    protected function getTmpUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/uploads/articles/';
    }

    /**
     * Upload image
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function uploadImage() {

        // the file property can be empty if the field is not required

        if (null === $this->file) {
            return;
        }
        $filename = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        if(!$this->id){
            $this->file->move($this->getTmpUploadRootDir(), $filename);
        }else{

            if(is_file($this->getUploadRootDir().$this->tmp)){
              unlink($this->getUploadRootDir().$this->tmp);
            }

            $this->file->move($this->getUploadRootDir(), $filename);
        }
        $this->setMainPhoto($filename);
    }

    /**
     * Move image to article directory
     *
     * @ORM\PostPersist()
     */
    public function moveImage()
    {
        if (null === $this->file) {
            return;
        }
        if(!is_dir($this->getUploadRootDir())){
            mkdir($this->getUploadRootDir());
        }
        copy($this->getTmpUploadRootDir().$this->mainPhoto, $this->getFullImagePath());
        @unlink($this->getTmpUploadRootDir().$this->mainPhoto);
    }

    /**
     * Remove image
     *
     * @ORM\PreRemove()
     */
    public function removeImage()
    {
        if($this->mainPhoto != null){
          @unlink($this->getFullImagePath());
        }

        @rmdir($this->getUploadRootDir());
    }

    /**
     * @return Blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param Blog $blog
     * @return Article
     */
    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     * @return Article
     */
    public function setMetaTitle($metaTitle): Article
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get disallowRobots
     *
     * @return bool
     */
    public function getDisallowRobots() {
        return $this->disallowRobots;
    }

    /**
     * Set disallowRobots
     *
     * @param bool $disallowRobots disalow robots
     */
    public function setDisallowRobots($disallowRobots) {
        $this->disallowRobots = $disallowRobots;
        return $this;
    }

    /**
     * Get disallowRobots
     *
     * @return bool
     */
    public function isDisallowRobots() {
        return $this->disallowRobots;
    }

    /**
     * @return string
     */
    public function getFollowAttribute()
    {
        return $this->followAttribute;
    }

    /**
     * @param string $followAttribute
     * @return Article
     */
    public function setFollowAttribute(string $followAttribute): Article
    {
        $this->followAttribute = $followAttribute;

        return $this;
    }




}
