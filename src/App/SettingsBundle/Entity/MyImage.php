<?php

namespace App\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MyImage
 *
 * @ORM\Table(name="my_image")
 * @ORM\Entity(repositoryClass="App\SettingsBundle\Repository\MyImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MyImage
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var bool
     *
     * @ORM\Column(name="isEnabled", type="boolean", nullable=true)
     */
    private $isEnabled;

    private $file = null;
    private $tmp;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
    private $description;
    
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
     * Set filename
     *
     * @param string $filename
     *
     * @return MyImage
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return MyImage
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    
    public function getFile()
	{
		return $this->file;
	}

	public function setFile( $file )
	{
		$this->file = $file;
        $this->tmp = $this->filename;
        $this->setFilename(uniqid());
//        if(!is_dir($this->getUploadRootDir())){
//            mkdir($this->getUploadRootDir());
//        }
//
//		if( is_file( $this->getUploadRootDir().$this->filename ) )
//		{
//			unlink( $this->getUploadRootDir().$this->filename );
//		}
//
//		//
//		$this->filename = $file->getClientOriginalName();
//		$this->file->move( $this->getUploadRootDir(), $this->filename );
//
		return $this;
	}
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
        return __DIR__ . '/../../../../web/uploads/myimage/';
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
        $this->setFilename($filename);
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
        copy($this->getTmpUploadRootDir().$this->filename, $this->getFullImagePath());
        unlink($this->getTmpUploadRootDir().$this->filename);
    }
    public function getFullImagePath() {
        return null === $this->filename ? null : $this->getUploadRootDir(). $this->filename;
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
     * Set description
     *
     * @param string $description
     *
     * @return MyImage
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
