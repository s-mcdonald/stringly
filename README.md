# PHP Stringly
[![Source](https://img.shields.io/badge/source-S_McDonald-blue.svg)](https://github.com/s-mcdonald/stringly)

`Stringly` is a String wrapper for PHP which allows for fluent usage and chainable command methods.
It also has Multi-Byte support and will default to non-mb functions if your system does not have mb enabled.

## Basic Usage
```php

        //
        // Create a new Stringly
        //
        $stringly = Stringly::Create("World");

        //
        // Prepend the string
        //
        $stringly->prepend("Hello");


```

## Usage
```php



        //
        // Create a new Stringly and define the encoding 
        //
        $stringly = Stringly::Create("This is a string",'UTF-8');


        //
        // Create a new Stringly and auto DETECT the encoding 
        //
        $stringly = Stringly::Create("プログラミングはクールです", "JIS", true);


        //
        // Create an Empty Stringly
        //
        $stringly = Stringly::Empty();



        //
        // Create From an array of strings
        //
        $stringly = Stringly::FromArray([' apple ','  pear ', 'strawberry']);





        //
        // Prepend the string
        //
        $stringly->prepend("text to add before the existing string");


        //
        // Append the string
        //
        $stringly->append("text to add after the existing string");


        //
        // Convert to UpperCase
        //
        $stringly->toUpperCase();


        //
        // Convert to LowerCase
        //
        $stringly->toLowerCase();


        //
        // Reverse the character string
        //
        $stringly->reverse();


        //
        // Is the text Numeric
        //
        $stringly->isNumeric() : bool;


        //
        // Length of String
        // Support built in for Multi-Byte Strings
        // 
        // 昨夜のコンサートは最高でし
        //
        $stringly->length() : int;


        //
        // Last index of String
        //
        $stringly->lastIndexOf('the fox');


        //
        // repeats the string
        //
        $stringly->repeat(3);


        //
        // and of course chaining 
        //
        $stringly->rinse()->repeat(3)->toLowerCase()->toTitleCase()->reverse()..... 


        //
        // Get the encoding of the string
        //
        $stringly->getEncoding();


```

Displaying/Output the Stringly object directly

```php
        
        //
        // toString()
        //
        echo $stringly->toString();

        //
        // or
        //
        echo $stringly;
```




<a name="installation"></a>
## Installation

Via Composer. Run the following command from your project's root.

```
composer require s-mcdonald/stringly
```

Then just add the namespace at the top of your php file.

```php

use SamMcDonald\Stringly\Stringly;

...

$stringly = Stringly::Create("This is the string",'UTF-8');

```


<a name="files"></a>
## Files

```
s-mcdonald/stringly/
            │    
            │    
            └ src/
              │
              │            
              └── Stringly.php

```

## License
<a name="license"></a>
Licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT)
