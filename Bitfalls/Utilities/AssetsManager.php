<?php

namespace Bitfalls\Utilities;
use Bitfalls\Interfaces\AssetFilter;

/**
 * The AssetManager can accept JS and CSS, combine them and output a filtered file.
 *
 * Class AssetsManager
 * @package Bitfalls\Utilities
 */
class AssetsManager {

    /** @var array  */
    protected $aJs = array();

    /** @var array  */
    protected $aCss = array();

    /** @var array  */
    protected $aJsFilters = array();

    /** @var array  */
    protected $aCssFilters = array();

    /** @var string */
    protected $sCssOutputFileName;

    /** @var string */
    protected $sJsOutputFileName;

    /** @var string  */
    protected $sJsPathPrefix = '/';

    /** @var string  */
    protected $sCssPathPrefix = '/';

    /** @var string */
    protected $sJsMinifyFolder;

    /** @var string */
    protected $sCssMinifyFolder;

    /**
     * Sets the prefix for the rendered file. For example, "www.domain.com/" would be valid.
     *
     * @param string $sString
     * @return $this
     */
    public function setJsPathPrefix($sString) {
        $this->sJsPathPrefix = $sString;
        return $this;
    }

    /**
     * Sets the prefix for the rendered file. For example, "www.domain.com/" would be valid.
     *
     * @param string $sString
     * @return $this
     */
    public function setCssPathPrefix($sString) {
        $this->sCssPathPrefix = $sString;
        return $this;
    }

    /**
     * Observes a set of files and calculates a hash of their names based on their paths and last modified times.
     *
     * @param array $aFiles
     * @return string
     */
    protected function calculateHash(array $aFiles) {
        $sString = '';
        foreach ($aFiles as $sFile => $bMinify) {
            $sString .= $sFile.filemtime($sFile);
        }
        return md5($sString);
    }

    /**
     * Sets the folder into which the final JS file will be saved.
     *
     * @param string $sFolder
     * @return $this
     * @throws \Exception
     */
    public function setJsMinifyFolder($sFolder) {
        if (!is_writable($sFolder)) {
            throw new \Exception('Folder "'.$sFolder.'" is not writable. Content will not be filterable.');
        }
        $this->sJsMinifyFolder = $sFolder;
        return $this;
    }

    /**
     * Sets the folder into which the final CSS file will be saved.
     *
     * @param string $sFolder
     * @return $this
     * @throws \Exception
     */
    public function setCssMinifyFolder($sFolder) {
        if (!is_writable($sFolder)) {
            throw new \Exception('Folder "'.$sFolder.'" is not writable. Content will not be filterable.');
        }
        $this->sCssMinifyFolder = $sFolder;
        return $this;
    }

    /**
     * Returns the JS minify folder with a trailing slash.
     * @return string
     */
    public function getJsMinifyFolder() {
        return rtrim($this->sJsMinifyFolder, '/').'/';
    }

    /**
     * Returns the CSS minify folder with a trailing slash.
     * @return string
     */
    public function getCssMinifyFolder() {
        return rtrim($this->sCssMinifyFolder, '/').'/';
    }

    /**
     * Returns the JS URL prefix (like domain) with trailing slash.
     * @return string
     */
    public function getJsPathPrefix() {
        return rtrim($this->sJsPathPrefix, '/').'/';
    }

    /**
     * Returns the CSS URL prefix (like domain) with trailing slash.
     * @return string
     */
    public function getCssPathPrefix() {
        return rtrim($this->sCssPathPrefix, '/').'/';
    }

    /**
     * Outputs the script tag with the up to this point added, joined and filtered JS files.
     * After outputting, the JS pool is emptied. If $bKeepFilters is false, the filters array is emptied too.
     */
    public function outputJs($bKeepFilters = true)  {
        $sHash = $this->calculateHash($this->aJs);
        $this->sJsOutputFileName = $sHash.'.js';

        if (!file_exists($this->getJsMinifyFolder().$this->sJsOutputFileName)) {
            $sString = '';
            foreach ($this->aJs as $sPath => $bFilter) {
                $sContents = file_get_contents($sPath);
                if ($bFilter) {
                    /** @var AssetFilter $oFilter */
                    foreach ($this->aJsFilters as $oFilter) {
                        $sContents = $oFilter->filter($sContents);
                    }
                }
                $sString .= $sContents;
            }
            if (empty($sString)) {
                throw new \Exception('Final JS file is empty!');
            }
            if (file_put_contents($this->getJsMinifyFolder().$this->sJsOutputFileName, $sString) === false) {
                throw new \Exception('Could not write to file!');
            }
        }

        if ($bKeepFilters === false) {
            $this->aJsFilters = array();
        }

        $this->aJs = array();

        echo '<script type="text/javascript" src="'.$this->getJsPathPrefix().$this->sJsOutputFileName.'"></script>';
        return $this;
    }

    /**
     * Outputs the script tag with the up to this point added, joined and filtered CSS files.
     * After outputting, the CSS pool is emptied. If $bKeepFilters is false, the filters array is emptied too.
     */
    public function outputCss($bKeepFilters = true)  {
        $sHash = $this->calculateHash($this->aCss);
        $this->sCssOutputFileName = $sHash.'.css';
        if (!file_exists($this->getCssMinifyFolder().$this->sCssOutputFileName)) {
            $sString = '';
            foreach ($this->aCss as $sPath => $bFilter) {
                $sContents = file_get_contents($sPath);
                if ($bFilter) {
                    /** @var AssetFilter $oFilter */
                    foreach ($this->aCssFilters as $oFilter) {
                        $sContents = $oFilter->filter($sContents);
                    }
                }
                $sString .= $sContents;
            }
            if (empty($sString)) {
                throw new \Exception('Final CSS file is empty!');
            }
            if (file_put_contents($this->getCssMinifyFolder().$this->sCssOutputFileName, $sString) === false) {
                throw new \Exception('Could not write to file!');
            }
        }

        if ($bKeepFilters === false) {
            $this->aCssFilters = array();
        }

        $this->aCss = array();

        echo '<link rel="stylesheet" href="'.$this->getCssPathPrefix().$this->sCssOutputFileName.'">';
        return $this;
    }

    /**
     * Adds a JS file into the JS pool. All the files will then be joined and filtered, if filter provided.
     *
     * @param string $sPath
     * @param bool $bFilter
     * @return $this
     * @throws \Exception
     */
    public function addJs($sPath, $bFilter = true) {
        if (!is_readable($sPath)) {
            throw new \Exception('Path "'.$sPath.'" is not readable to PHP.');
        }
        $this->aJs[$sPath] = $bFilter;
        return $this;
    }
    /**
     * Adds a CSS file into the CSS pool. All the files will then be joined and filtered, if filter provided.
     *
     * @param string $sPath
     * @param bool $bFilter
     * @return $this
     * @throws \Exception
     */
    public function addCss($sPath, $bFilter = true) {
        if (!is_readable($sPath)) {
            throw new \Exception('Path "'.$sPath.'" is not readable to PHP.');
        }
        $this->aCss[$sPath] = $bFilter;
        return $this;
    }

    /**
     * Adds a JS filter to the array of filters to be used.
     * When outputJs is called, content is pulled through all filters in the
     * order they were added in.
     *
     * @param AssetFilter $oFilter
     * @return $this
     */
    public function addJsFilter(AssetFilter $oFilter) {
        $this->aJsFilters[] = $oFilter;
        return $this;
    }

    /**
     * Adds a CSS filter to the array of filters to be used.
     * When outputCss is called, content is pulled through all filters in the
     * order they were added in.
     *
     * @param AssetFilter $oFilter
     * @return $this
     */
    public function addCssFilter(AssetFilter $oFilter) {
        $this->aCssFilters[] = $oFilter;
        return $this;
    }

}