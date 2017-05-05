<?php

/*
 * Highlight Text in Image
 *
 * (c) Jan Ropertz <janropertz@gmail.com>
 * 
 */

namespace Janrop;

use Google\Cloud\Vision\VisionClient;

class TextInImageHighlighter {

    private $image;
    private $annotations;

    /**
     * Create new TextInImageHighlighter requires an Image resource 
     * and an array that gets passed to the VisionClient
     * http://googlecloudplatform.github.io/google-cloud-php/#/docs/cloud-vision/v0.3.0/vision/visionclient
     *
     * @param resource $image
     * @param array $VisionClientConfig
     */
    function __construct($imageStream, $VisionClientConfig = array()){

        $imageData = stream_get_contents($imageStream);
        $this->image = imagecreatefromstring($imageData);

        $vision = new VisionClient($VisionClientConfig);

        $image = $vision->image($imageData, ['TEXT_DETECTION']);

        $result = $vision->annotate($image);

        $annotations = $result->text();

        // Remove annotation that wraps all other annotations
        if(count($annotations) > 1){
            unset($annotations[0]);
        }

        $this->annotations = $annotations;

    }

    /**
     * Find and Hightlight an String inside of the Image
     * Pass $strict = false to highlight every block that contains the given searchterm
     * 
     * @param String $search 
     * @param boolean $strict
     * @param array $color - RGB
     * @param int $lineThickness
     * 
     * @return int number of highlighted blocks
     */
    public function findAndHighlight($search = '', $strict = true, $color = array(0, 0, 255), $lineThickness = 5){

        $matchingBlocks = array_filter($this->annotations, function($annotation) use ($search, $strict){

            if($strict){
                return $annotation->description() == $search;
            }else{
                return stripos($annotation->description(), $search) !== FALSE;
            }
            
        });

        $matchingBlocks = array_values($matchingBlocks);

        // Set a colour for the sides of the rectangle
        $color = call_user_func_array('imagecolorallocate', array_merge(array($this->image), $color));

        // Set width of the rectangle
        imagesetthickness($this->image, $lineThickness);

        foreach($matchingBlocks as $annotationToHightlight){

            // Draw the rectangle from the top-left to the bottom-right.
            imagerectangle(
                $this->image, 
                $annotationToHightlight->info()['boundingPoly']['vertices'][0]['x'] - $lineThickness, 
                $annotationToHightlight->info()['boundingPoly']['vertices'][0]['y'] - $lineThickness, 
                $annotationToHightlight->info()['boundingPoly']['vertices'][2]['x'] + $lineThickness, 
                $annotationToHightlight->info()['boundingPoly']['vertices'][2]['y'] + $lineThickness, 
                $color
            );

        }

        return count($matchingBlocks);
    }

    /**
     * Get Image Resource
     *
     * @return resource Image
     */
    public function getImage(){
        return $this->image;
    }
}

