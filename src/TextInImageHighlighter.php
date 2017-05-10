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
    private $matches;

    /**
     * Create new TextInImageHighlighter requires an Image resource 
     * and an array that gets passed to the VisionClient
     * http://googlecloudplatform.github.io/google-cloud-php/#/docs/cloud-vision/v0.3.0/vision/visionclient
     *
     * @param resource $image
     * @param array $VisionClientConfig
     */
    function __construct($imageStream, $VisionClientConfig = array()){

        if(gettype($imageStream) !== 'resource'){
            throw new \InvalidArgumentException('constructor expects first parameter to be an image stream. Got: ' . gettype($imageStream));
            return false;
        }

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

        return true;

    }

    /**
     * Find and Hightlight an String inside of the Image
     * Pass $strict = false to highlight every block that contains the given searchterm
     * 
     * @param String $search 
     * @param boolean $strict
     * 
     * @return $this
     */
    public function find($search = '', $strict = true){

        $matchingBlocks = array_filter($this->annotations, function($annotation) use ($search, $strict){

            if($strict){
                return $annotation->description() == $search;
            }else{
                return stripos($annotation->description(), $search) !== FALSE;
            }
            
        });

        $this->matches = array_values($matchingBlocks);

        return $this;

    }

    /**
     * Get Number of matches from last find()
     *
     * @return int
     */
    public function countMatches(){

        return count($this->matches);

    }

    /**
     * Hightlight the matches found by find()
     * 
     * @param array $color - [R,G,B]
     * @param int $lineThickness
     * 
     * @return number of highlighted matches
     */
    public function highlight($color = array(0, 0, 255), $lineThickness = 5){

        // Set a colour for the sides of the rectangle
        $color = call_user_func_array('imagecolorallocate', array_merge(array($this->image), $color));

        // Set width of the rectangle
        imagesetthickness($this->image, $lineThickness);

        foreach($this->matches as $annotationToHightlight){

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

        return count($this->matches);

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

