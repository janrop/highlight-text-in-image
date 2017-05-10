<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Janrop\TextInImageHighlighter;

/**
 * @covers TextInImageHighlighter
 */
final class TextInImageHighlighterTest extends TestCase {

    private $highlighter;

    public function setUp(){
        
        putenv('GOOGLE_APPLICATION_CREDENTIALS=./tests/google_application_credentials.json');

        $image = fopen(__DIR__ . '/Lorem_Ipsum_Helvetica.png', 'r');

        $this->highlighter = new \Janrop\TextInImageHighlighter($image);

    }

    public function testRequiresImageStreamAsParameter(){

        $this->expectException(\InvalidArgumentException::class);

        new \Janrop\TextInImageHighlighter(null);

    }

    public function testManipulatesImage(){
        
        $imageBefore = $this->_clone_img_resource($this->highlighter->getImage());

        $this->highlighter->find('ipsum', true)->highlight([255, 0, 0], 3);

        $this->assertNotEquals($imageBefore, $this->highlighter->getImage());

    }

    public function testFindOccurences(){

        $this->assertGreaterThan(1, $this->highlighter->find('Lorem', true)->countMatches());

    }

    function _clone_img_resource($img) {

        //Get width from image.
        $w = imagesx($img);
        //Get height from image.
        $h = imagesy($img);
        //Get the transparent color from a 256 palette image.
        $trans = imagecolortransparent($img);

        //If this is a true color image...
        if (imageistruecolor($img)) {

            $clone = imagecreatetruecolor($w, $h);
            imagealphablending($clone, false);
            imagesavealpha($clone, true);

        }
        //If this is a 256 color palette image...
        else {

            $clone = imagecreate($w, $h);

            //If the image has transparency...
            if($trans >= 0) {

                $rgb = imagecolorsforindex($img, $trans);

                imagesavealpha($clone, true);
                $trans_index = imagecolorallocatealpha($clone, $rgb['red'], $rgb['green'], $rgb['blue'], $rgb['alpha']);
                imagefill($clone, 0, 0, $trans_index);

            }
        }

        //Create the Clone!!
        imagecopy($clone, $img, 0, 0, 0, 0, $w, $h);

        return $clone;

    }

}
