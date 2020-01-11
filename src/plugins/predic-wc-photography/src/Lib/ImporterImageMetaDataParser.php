<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterImageMetaDataParserInterface;

/**
 * Class ImporterImageMetaDataParser
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterImageMetaDataParser extends ImporterImageMetaDataParserSchema implements ImporterImageMetaDataParserInterface
{
    /**
     * Path to an image file
     * @var string
     */
    private $imagePath;

    /**
     * Parse metadata from an image
     *
     * @param string $imagePath Path to the image
     */
    public function parse($imagePath)
    {
        // TODO: handle metadata reading i upisati sta je sta u gdoc dokument i odakle se vadi

        $this->imagePath = $imagePath;
        $this->setExifData();
        $this->setIptcData();
    }

    /**
     * Return camera model name
     * @return string
     */
    public function getCamera()
    {
        return $this->camera;
    }

    /**
     * Return image resolution in px
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Return image type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return image document name from editing program if set
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return image decription
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return image keywords in a single dimension non associative array
     * @return array
     */
    public function getKeywords()
    {
        return $this->keyWords;
    }

    /**
     * Return Timestamp of a photo taken
     * @return int
     */
    public function getCameraUploadDate()
    {
        return $this->cameraUploadDate;
    }

    /**
     * Set metadata which can be read using exif_read_data function
     */
    private function setExifData()
    {
        $metaData = exif_read_data($this->imagePath);

        // Model - Camera tj aparat - KORISTIM
        // COMPUTED[Height] - ovde je vrednost 5511 tj sirina  - KORISTIM
        // COMPUTED[Width] - ovde je vrednost 3674 tj visina - KORISTIM
        // MimeType - ovo je tip fajla - KORISTIM

        $this->camera          = isset($metaData['Model']) ? $metaData['Model'] : '';
        $this->resolution      = isset($metaData['COMPUTED']['Width']) && isset($metaData['COMPUTED']['Height']) ?
            ['width' => sanitize_text_field($metaData['COMPUTED']['Width']), 'height' => sanitize_text_field($metaData['COMPUTED']['Height'])] :
            [];
        $this->type     = isset($metaData['MimeType']) ? $metaData['MimeType'] : '';
    }

    /**
     * Set metadata which can be parsed using iptcparse funciton
     */
    private function setIptcData()
    {
        $size = getimagesize($this->imagePath, $info);
        $iptc = is_array($info) && isset($info["APP13"]) ? iptcparse($info["APP13"]) : [];

        // TODO: More from this shoot i More from this model
        // More from this shoot - custom taxonomy / jos treba odrediti odakle ce da se cita
        // More from this model - custom taxonomy  / jos treba odrediti odakle ce da se cita

        // 2#025 - keywords
        // 2#005 - - ProductName tj ako je podeseno u metadata kao Document title u Description tabu

        $this->name                 = isset($iptc['2#005'][0]) ? $iptc['2#005'][0] : '';
        $this->description          = isset($iptc['2#120'][0]) ? $iptc['2#120'][0] : '';
        $cameraUploadDate           = isset($iptc['2#055'][0]) ? $iptc['2#055'][0] : '';
        if (! empty($cameraUploadDate)) {
            try {
                $dateTime               = new \DateTime($iptc['2#055'][0]);
                $this->cameraUploadDate = $dateTime->getTimestamp();
            } catch (\Exception $e) {
                $this->cameraUploadDate = '';
            }
        }
        $this->keyWords      = isset($iptc['2#025']) ? $iptc['2#025'] : [];
    }
}
