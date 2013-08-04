<?php

namespace Bitfalls\Traits;

use Bitfalls\Phalcon\Injectable;

/**
 * Class ImageOwner
 * @package Bitfalls\Traits
 */
trait ImageOwner {

    /** @var string  */
    protected $imagSubfolder = '';

    /**
     * @return null|string
     */
    public function getPicturesDirectory()
    {
        if ($this->getId() == null) {
            return null;
        }

        if (empty($this->imageSubfolder)) {
            $this->imageSubfolder = strtolower(__CLASS__);
        }

        $sPicturesDir = $this->getDI()->get('config')->application->picturesDir;
        $sImagesSubfolder =$sPicturesDir.$this->imageSubfolder.'/';
        $sDir = $sImagesSubfolder . $this->getId() . '/';
        if (!file_exists($sDir)) {
            mkdir($sDir, 0755, true);
        }

        return $sDir;
    }

    /**
     * @param $sHash
     * @return bool
     */
    public function deleteImage($sHash) {
        $aImages = $this->getUploadedImages();
        foreach ($aImages as $aImage) {
            if (md5(md5($this->getId().$aImage['webpath'])) == $sHash) {
                return @unlink($aImage['realpath']);
            }
        }
        return false;
    }

    /**
     * @return int
     */
    public function getNumberOfUploadedImages()
    {
        $x = 0;
        if ($this->getPicturesDirectory() && is_readable($this->getPicturesDirectory())) {
            $dir = new \DirectoryIterator($this->getPicturesDirectory());
            /** @var \DirectoryIterator $file */
            foreach ($dir as $file) {
                if (in_array($file->getExtension(), array('jpg', 'jpeg', 'png', 'webp'))) {
                    $x++;
                }
            }
        }
        return $x;
    }

    /**
     * @return array
     */
    public function getUploadedImages() {
        $aResult = array();
        if ($this->getPicturesDirectory() && is_readable($this->getPicturesDirectory())) {
            $dir = new \DirectoryIterator($this->getPicturesDirectory());
            /** @var \DirectoryIterator $file */
            foreach ($dir as $file) {
                if (in_array($file->getExtension(), array('jpg', 'jpeg', 'png', 'webp'))) {
                    $aResult[] = array(
                        'realpath' => $file->getRealPath(),
                        'webpath' => '/'.explode('/public/', $file->getRealPath())[1],
                        'name' => $file->getBasename('.php')
                    );

                }
            }
        }
        return $aResult;
    }

}