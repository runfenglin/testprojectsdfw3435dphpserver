<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media
 *
 * @ORM\Table(name="tr_media")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Media
{

    const ENTRY_POINT = 'uploads';
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
     * @ORM\Column(name="path", type="string", length=255)
     * @Assert\NotBlank
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_size", type="integer")
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=32)
     */
    private $mimeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @Assert\File(maxSize="2000000")
     */
    private $file;
    
    private $temp;
    
    private $uploadDir = self::ENTRY_POINT;

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
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     * @return Media
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer 
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Media
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Media
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    public function getAbsolutePath()
    {
        return NULL === $this->path 
                        ? NULL 
                        : $this->_getUploadRootDir() . '/' . $this->path;
    }
    
    public function getWebPath()
    {
        return NULL === $this->path
                        ? NULL
                        : '/' . $this->path;
    }
    
    protected function _getUploadRootDir()
    {
        return __DIR__ . '/../../../../web';
    }
    
    public function setUploadDir($path)
    {
        $path = strtolower($path);
        if (0 !== strpos($path, self::ENTRY_POINT)) {
            $this->uploadDir = self::ENTRY_POINT . '/' . trim($path, '/');
        }
        else {
            $this->uploadDir = trim($path, '/');
        }
    }
    
    public function getUploadDir()
    {
        return $this->uploadDir;
    }
    
    
    /**
     * Sets file.
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = NULL)
    {
        $this->file = $file;
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            $this->temp = $this->getAbsolutePath();
        }

        $this->path = NULL;

        return $this;
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (NULL !== $this->getFile()) {
            
            $filename = pathinfo($this->getFile()->getClientOriginalName(), PATHINFO_FILENAME);
            
            $ext = $this->getFile()->guessExtension();
            
            $fullPath = $this->_getUploadRootDir() 
                        . '/' . $this->getUploadDir() 
                        . '/' . $filename . '.' 
                        . $ext;
            
            if (!$this->temp || $fullPath != $this->temp) {
                $i = 1;
                $uniqueName = $filename;
                
                while(file_exists($fullPath)) {
                    
                    $uniqueName = $filename . '_' . $i++;
                    
                    $fullPath = $this->_getUploadRootDir() 
                                . '/' . $this->getUploadDir() 
                                . '/' . $uniqueName . '.'
                                . $ext;
                    
                }
            }

            $this->setPath($this->getUploadDir() 
                           . '/' . $uniqueName . '.'
                           . $ext);
            $this->setMimeType($this->getFile()->getMimeType());
            $this->setFileSize($this->getFile()->getClientSize());
        }
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        
        $this->getFile()->move($this->getAbsolutePath());
        
        if (isset($this->temp)) {
            // delete old image
            unlink($this->temp);
            
            $this->temp = NULL;
        }
        
        $this->file = NULL;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
