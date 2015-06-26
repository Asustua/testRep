<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class
 *
 * @author patryk
 */
class upload {

    private $db;
    private $uploadPath;
    private $thumb;
    private $validExtensions = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp');
    private $maxSize = 200000;
    private $files = array();
    private $width = 1000;
    private $height = 1000;
    
    public function __construct($db, $aFiles, $sUploadPath = '', $lThumb = true) {
        $this->db = $db;
        if(!empty($sUploadPath)) {
            $this->uploadPath = $sUploadPath;
        } else {
            $this->uploadPath = ''; // Standard
        }
        $this->thumb = $lThumb;
        $lTest = checkFiles($aFiles);
        if(!$lTest) return 0;
    }
    
    public function checkFiles($aFiles) {
        if(empty($aFiles)) return 0;
        foreach($aFiles['name'] as $nKey => $sFilename) {
            if(empty($this->files['error'][$nKey]) and in_array($aFiles['type'][$nKey], $this->validExtensions)) {
                $this->files[] = array(
                    'filename'  => $sFilename,
                    'type'      => $aFiles['type'][$nKey],
                    'tmp_name'  => $aFiles['tmp_name'][$nKey],
                    'error'     => $aFiles['error'][$nKey],
                    'size'      => $aFiles['size'][$nKey]
                );
            } else {
                unlink($aFiles['tmp_name'][$nKey]);
            }
        }
    }
    
    public function saveImages($iUser_id, $sSwitch = '', $iId = 0) {
        foreach($this->files as $aImages) {
            $oImg = openImagE($aImages['tmp_name']);
            if($oImg === 0) continue;
        }
    }
    
    private function resizeImage($iImageWidth, $iImageHeight, $iNewWidth = 1000, $iNewHeight = 1000, $sOption='auto') {
        
    }
    
    private function getDimensions($iImageWidth, $iImageHeight, $iNewWidth = 1000, $iNewHeight = 1000, $option)
    {

       switch ($option)
        {
            case 'exact':
                $optimalWidth = $iNewWidth;
                $optimalHeight= $iNewHeight;
                break;
            case 'portrait':
                $optimalWidth = $this->getSizeByFixedHeight($iNewHeight);
                $optimalHeight= $iNewHeight;
                break;
            case 'landscape':
                $optimalWidth = $iNewWidth;
                $optimalHeight= $this->getSizeByFixedWidth($iNewWidth);
                break;
            case 'auto':
                $optionArray = $this->getSizeByAuto($iNewWidth, $iNewHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
            case 'crop':
                $optionArray = $this->getOptimalCrop($iNewWidth, $iNewHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    
    private function getSizeByFixedHeight($iImageWidth, $iImageHeight,$newHeight)
    {
        $ratio = $iImageWidth / $iImageHeight;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }

    private function getSizeByFixedWidth($iImageWidth, $iImageHeight,$newWidth)
    {
        $ratio = $iImageHeight / $iImageWidth;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }

    private function getSizeByAuto($iImageWidth, $iImageHeight,$newWidth, $newHeight)
    {
        if ($iImageHeight < $iImageWidth)
        // *** Image to be resized is wider (landscape)
        {
            $optimalWidth = $newWidth;
            $optimalHeight= $this->getSizeByFixedWidth($newWidth);
        }
        elseif ($iImageHeight > $iImageWidth)
        // *** Image to be resized is taller (portrait)
        {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight= $newHeight;
        }
        else
        // *** Image to be resizerd is a square
        {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight= $this->getSizeByFixedWidth($newWidth);
            } else if ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight= $newHeight;
            } else {
                // *** Sqaure being resized to a square
                $optimalWidth = $newWidth;
                $optimalHeight= $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    private function getOptimalCrop($iImageWidth, $iImageHeight,$newWidth, $newHeight)
    {

        $heightRatio = $iImageWidth / $newHeight;
        $widthRatio  = $iImageHeight /  $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = $iImageHeight / $optimalRatio;
        $optimalWidth  = $iImageHeight  / $optimalRatio;

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }
    /*
    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    {
        // *** Find center - this will be used for the crop
        $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
        $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

        //imagedestroy($this->imageResized);

        // *** Now crop from center to exact requested size
        $this->imageResized = imagecreatetruecolor($newWidth , $newHeight);
        imagecopyresampled($this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
    }
    */
    private function openImage($sFile) {
        $sMime = mime_content_type($sFile);
        if(!in_array($sMime, $this->validExtensions))   return 0;
        
        $oImg = false;
        
        switch($sMine) {
            case 'image/jpeg':
            case 'image/jpg':
                $oImg = @imagecreatefromjpeg($sFile);
            break;
            case 'image/png':
                $oImg = @imagecreatefrompng($sFile);
            break;
            case 'image/gif':
                $oImg = @imagecreatefromgif($sFile);
            break;
            case 'image/bmp':
                $oImg = $this->imagecreatefrombmp($sFile);
            break;
            default:
                unlink($sFile);
        }
        return $oImg;
    }
    
    private function imagecreatefrombmp( $cFilename )
    {
        $sFile = fopen( $cFilename, "rb" );
        $read = fread( $sFile, 10 );
        while( !feof( $sFile ) && $read != "" )
        {
            $read .= fread( $sFile, 1024 );
        }
        $temp = unpack( "H*", $read );
        $hex = $temp[1];
        $header = substr( $hex, 0, 104 );
        $body = str_split( substr( $hex, 108 ), 6 );
        if( substr( $header, 0, 4 ) == "424d" )
        {
            $header = substr( $header, 4 );
            // Remove some stuff?
            $header = substr( $header, 32 );
            // Get the width
            $width = hexdec( substr( $header, 0, 2 ) );
            // Remove some stuff?
            $header = substr( $header, 8 );
            // Get the height
            $height = hexdec( substr( $header, 0, 2 ) );
            unset( $header );
        }
        $x = 0;
        $y = 1;
        $image = imagecreatetruecolor( $width, $height );
        foreach( $body as $rgb )
        {
            $r = hexdec( substr( $rgb, 4, 2 ) );
            $g = hexdec( substr( $rgb, 2, 2 ) );
            $b = hexdec( substr( $rgb, 0, 2 ) );
            $color = imagecolorallocate( $image, $r, $g, $b );
            imagesetpixel( $image, $x, $height-$y, $color );
            $x++;
            if( $x >= $width )
            {
                $x = 0;
                $y++;
            }
        }
        return $image;
    }
    
}
