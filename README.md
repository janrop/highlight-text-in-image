# PHP Highlight Text in Image

> Lets you check for and highlight strings in images utilizing [Googles Cloud Vision API](https://packagist.org/packages/google/cloud-vision)


## Installation

```
$ composer require janrop/highlight-text-in-image
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

# Check if String "Foo" exists in document.
# If it does  highlight it with a green border.
# If not highlight all Blocks containing "Bar" with a red border.
if($highlighter->find('Foo')->countMatches()){
    $highlighter->highlight([0, 255, 0], 3);
}else{
    $highlighter->find('Bar', false)
                 ->highlight([255, 0, 0], 3);
}

# Save image to jpeg
imagejpeg($highlighter->getImage(), "annotated.jpg");
```