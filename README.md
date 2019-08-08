# PHP Stringly
[![Source](https://img.shields.io/badge/source-S_McDonald-blue.svg)](https://github.com/s-mcdonald/stringly)

Stringly is a String wrapper for PHP which allows for fluent usage and chainable command methods.
It also has Multi-Byte support and will default to non-mb functions if your system does not have mb enabled.


## Basic Usage
```php
        //
        // Create a new String like so
        //
        $stringly = new Stringly("This is a string");

        //
        // If you want to define the encoding use ::Create()
        //
        $stringly = Stringly::Create("This is a string",'UTF-8');


        //
        // Prepend the string
        //
        $stringly->prepend("this goes first");


        //
        // Append the string
        //
        $stringly->append("this goes last");


        //
        // do other cool stuff
        //
        $stringly->toUpperCase();


        //
        // even cooler stuff
        //
        $stringly->reverse();


        //
        // Built-in fantastic stuff
        //
        $stringly->isNumeric() : bool;


        //
        // Built-in Multi-Byte support
        // 
        // 昨夜のコンサートは最高でし
        //
        $stringly->length() : int;


        //
        // Wow how amzing is this
        //
        $stringly->lastIndexOf('the fox');


        //
        // repeats the string
        //
        $stringly->repeat(3);


        //
        // and of course chaining
        //
        $stringly->rinse()->repeat(3)->toLowerCase()->toTitleCase()->reverse()..... //and so on 

        //
        // toString()
        //
        $stringly->toString();

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

$str = Stringly::Create(......);

```


<a name="files"></a>
## Files

```
s-mcdonald/stringly/
            │    
            └ src/
              │
              │            
              └── Stringly.php

```

## License
<a name="license"></a>
Licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT)
