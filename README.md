# PHP Highlight Text in Image

> Lets you check for and highlight strings in images utilizing [Googles Cloud Vision API](https://packagist.org/packages/google/cloud-vision)


## Installation

```
$ composer require janrop/highlight-test-in-image
```

Before using you need to download a [Google Authentication Details .json file](https://developers.google.com/identity/protocols/application-default-credentials#howtheywork) and reference it in the `GOOGLE_APPLICATION_CREDENTIALS` environment variable:

```php
putenv('GOOGLE_APPLICATION_CREDENTIALS=./path_to/google_application_credentials.json');
```

## Usage

```php
use Janrop\TextInImageHighlighter;
```

```php

$image = fopen(__DIR__ . '/Lorem_Ipsum_Helvetica.png', 'r');

$highlighter = new \Janrop\TextInImageHighlighter($image);

# Check if string exists in image:
if($highlighter->findAndHighlight('Test')){
    # String "Test" in image
}

# Draw red rectange with a 3 px width border around every text block equaling to or containing the test string
$highlighter->findAndHighlight('Test', false, [255, 0, 0], 3);

# Save image to jpeg
imagejpeg($highlighter->getImage(), "annotated2.jpg");
```