<?php

namespace SamMcDonald\Stringly;

/**
 * 
 * Stringly
 * 
 *
 *
 * Stringly is a *String* wrapper for PHP which allows for fluent usage and chainable 
 * command methods. With built in Multi-Byte support and encoding conversions, the 
 * library is entirely contained within a single file and has no dependencies. 
 * Stringly will default to non-mb functions if your system does 
 * not support Multi-byte.
 *
 * @author Sam McDonald <s.mcdonald@outlook.com.au>
 * @link   https://github.com/s-mcdonald/stringly
 *
 * 
 * 
 * Licence 
 * @url https://opensource.org/licenses/MIT
 * 
 * Copyright 2019 Sam McDonald
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of 
 * this software and associated documentation files (the "Software"), to deal in 
 * the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or 
 * sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following 
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 * 
 * Fixes:
 * 1. Added default of 2 for repeat. So now ->repeat() will repeat the text
 * 2. setEncoding($encoding = 'UTF-8') now by defgault is to UTF-8 rather than null/required
 * 3. Added __debugInfo() for more info on the variable for development
 * 4. detectEncoding($use_strict = true) now has strict mode on, prev no option
 * 5. isEncodingValid()  now returns true if non-multi-byte
 * 6. No more constructor, just use helper static functions
 * 7. Removed the make command/facade
 * 8. added concat()
 * 
 */
final class Stringly 
{


#region Requires Review

    /**
     * Detect encoding when not provided.
     * 
     * Refer to http://php.net/manual/en/mbstring.supported-encodings.php for full list of encoding types.
     * 
     * - Added strict mode for detection
     * http://php.net/manual/en/function.mb-detect-encoding.php#102510
     * 
     */
    private function detectEncoding($use_strict = true)
    {
        if($this->has_mbstring) 
        {
            $this->encoding = \mb_detect_encoding($this->value,  \mb_detect_order(), $use_strict);
        }
    }



#endregion


#region class variables

    /*
     * Internal string container
     * 
     * @var string
     */
    private $value;

    /**
     * Flag to check if MB String is enabled on the system
     * 
     * @var bool
     */
    private $has_mbstring;


    /**
     * Encoding type for MB Strings
     * 
     * @var Encoding
     */
    private $encoding;



    private $_enableMBSupport;



    /**
     * Default enmcoding for Stringly
     * 
     */
    const DEFAULT_ENCODING = 'UTF-8';


#endregion


#region Constructor(s)

    /**
     * @constructor 
     * 
     */
    protected function __construct($string = "", $encoding = null, $auto_detect_encoding = true) 
    {  
        $this->_disabledMBSupport = false;

        $this->encoding = ( $encoding == null ) ? self::DEFAULT_ENCODING : $encoding ;

        // Check for MB support
        $this->has_mbstring = extension_loaded('mbstring');

        $this->value = $string;

        if($auto_detect_encoding)
            $this->detectEncoding();
    }

    /** 
     * Create(string $str, string $encoding = null)
     * 
     * @param String $string    - String or Styringly object required to create a new Stringly
     * @param String $encoding  - The prefferred encoding for the new Stringly Object.
     *                          - When null is passed the object will auto-detect 
     *                          - the encoding.,
     */
    public static function Create(string $string, string $encoding = null) : Stringly
    {
        return new static($string, $encoding,($encoding==null));
    }


    //
    // FromArray(array $str, string $glue = '', string $encoding = null)
    // future improvementr, add option to trim each str in the array before adding to stringly object
    //
    public static function FromArray(array $strings, string $glue = '', $encoding = null) : Stringly
    {
        $builder = [];

        foreach($strings as $str)
        {
            $builder[] = $str;
        }

        $string = implode($glue, $builder);

        return new static($string, $encoding,($encoding==null));
    }



    

    /**
     * Returns an empty string with the same encoding
     * 
     * @param String $encoding  - The prefferred encoding for the new Stringly Object.
     *                          - When null is passed the object will auto-detect 
     *                          - the encoding.,
     * 
     * 
     * @return  A new Stringly with no text
     */
    public static function Empty($encoding=null)
    {
        return new static("", $encoding,($encoding==null));
    }


#endregion


#region Methods in Order

    /**
     * append($string) : Stringly
     * 
     * Append a string to stringly. This string can either be any acceptable 
     * PHP String or a Stringly object
     * 
     * @since 1.0 - Initial
     * @since 2.0 - refactored code to work with new Creator facdes and type hints
     * @author Sam McDonald
     *
     * @param  String $string This string can either be any acceptable PHP String or a Stringly object
     * @return Stringly A new Stringly Object
     * 
     * @example:
     *      $stringly = Stringly::Create("...ABCD");
     *      $stringly2 = Stringly::Create("XYZ");
     *      echo $stringly->append("EFGHI..")->append($stringly2);
     * 
     *  Both Output:
     *      ...ABCDEFGHI..XYZ
     * 
     */
    public function append(String $string) : Stringly
    {
        return static::Create(implode('',[$this->value, $string]));
    }

    /**
     * charAt(int) : String
     * 
     * Returns the character at the position provided by the user
     * 
     * @since 2.0
     * @author Sam McDonald
     * @return String
     * 
     * @example:
     *      $stringly = Stringly::Create("Be Yourself. Everyone Else Is Already Taken");
     *      echo $stringly->charAt(7);
     * 
     *  Both Output:
     *      's'
     */
    public function charAt($index)
    {
        return $this->substring($index, 1);
    }


    /**
     * compare(string) : int
     * 
     * Case sensitive
     * 
     * @since 1.0
     * 
     * @author Sam McDonald
     * 
     * @param string $string The String/object to compare the current Stringly with.
     *
     * @return int  The greater the value from a perfect '0' the greater the
     *              difference between the two comparfisons. If the result
     *              is > 0 then thev partam is less than the current 
     *              object. If the resuylt is positive, then the 
     *              param is greater than the current object.
     * <code>
     *      $stringly = Stringly::Create("are we similar", "UTF-8");
     *      $stringly2 = Stringly::Create("are we similar", "ASCII");
     *
     *      echo $stringly->compare($stringly2);
     * </code>
     *      Output: 
     *      0
     */
    public function compare($string) : int
    {
        if($string instanceof Stringly)
        {
            return -strcmp($this->value, $string->toString());
        }

        if(is_string($string))
        {
            return -strcmp($this->value, $string);
        }

        return $this === (string) $string;        
        
    }



    /**
     * concat(...mixed)
     * 
     * Concat any number of strings or Stringlys in the desired order.
     * This will create a new Stringly and always imply the encoding of the new Stringly.
     * 
     * @since 2.0
     * @author Sam McDonald
     * @return Stringly
     * 
     * @example:
     *      $stringly->concat("..malcom..")->concat($stringly2);
     *      $stringly->concat("..malcom..", $stringly2);
     * 
     *  Both Output:
     *      ...ABCD..malcom..XYZ...
     */
    public function concat(...$str) 
    {
        $flat = [];

        foreach ($str as $s)
        {
            if (is_array($s))
            {
                $flat[] = implode('',$s);
            }
            else
            {
                $flat[] = $s;
            }
        }

        return Stringly::Create($this->value . implode('',$flat ));
    }


    
    /**
     * contains($string, $case_sensitive = true) : int
     * 
     * Does the current Stringly object contain a string passed in? If so the number 
     * returned will be the number of occurances of tyhat string.
     *
     * @param  string $string
     * @return int The number of occurances of a given string
     * 
     * @since 2.0
     * @author Sam McDonald
     * @example:
     *      $stringly = Stringly::Create("Everybody has a soul. So move your body and dance.");
     *      echo $stringly->contains("body");
     * 
     *  Output:
     *      2
     * 
     */
    public function contains($string, $case_sensitive = true) : int
    {
        if($this->has_mbstring)
        {
            return \mb_substr_count($this->value, $string, $this->encoding);
        }
        return substr_count($this->value, $string); 
    }


    // Which is better ?


    /**
     * containsMultibyte() : bool
     * 
     * Does the current Stringly object contain any multibyte characters.
     *
     * @return bool True if the string contains a MB character, false if no MB character found
     * 
     * @since 2.0
     * @author Sam McDonald
     * 
     */
    public function containsMultibyte()
    {
        // We need to check speed and accurancy of both
        // Solution:1
        //return !mb_check_encoding($this->value, 'ASCII') && mb_check_encoding($this->value, 'UTF-8');

        // Solution:2
        if (mb_strlen($this->value) != strlen($this->value)) {
            return true;
        } 
        return false;
    }

    /**
     * ctype_cntrl — Check for control character(s)
     */
    public function containsControlChar() : bool
    {
        return ctype_cntrl( $this->value );
    }


    /**
     * ctype_graph — Check for any printable character(s) except space
     */
    public function containsPrintable() : bool
    {
        return ctype_graph ( $this->value );
    }


    /**
     * Check for any printable character which is not 
     * whitespace or an alphanumeric character
     */
    public function containsPuctuation() : bool
    {
        return preg_replace("/[^a-z \d \' \. \, \" \: \; \? \- \! \s]/i", "", $this->value);
    }



    /**
     * count() : int
     * 
     * An alias of length()
     *
     * @since 1.0 - Initial
     * @since 2.0 - Added sujpport to disable MB check
     * 
     * @return int  The length of the String. <warning>With mb disabled 
     *              you may receive incorrect yeild. Do not disable 
     *              unbless you understand what this does.</warning>
     *
     * @author Sam McDonald
     * 
     *     $stringly = Stringly::Create("こんにちは、私の名前はStringlyです。");
     *
     * echo $stringly;
     * 
     * echo "With MB Support, the string is " . $stringly->length() . " characters long";
     * 
     * echo "Without MB Support, the string is " . strlen($stringly) . " characters long";
     * 
     * 
     * Output
     * こんにちは、私の名前はStringlyです。
     * With MB Support, the string is 22 characters long
     * Without MB Support, the string is 50 characters long
     *
     */
    public function count()
    {
        if (!$this->_enableMBSupport) 
            return strlen($this->value);

        return ($this->has_mbstring) ? mb_strlen($this->value) : strlen($this->value) ;
    }



    /**
     * debug() : Stringly
     * 
     * @since 2.0 - Initial
     * @deprecated - Please use ->export()
     * 
     * @return Stringly
     *
     * @author Sam McDonald
     * 
     */
    public function debug() 
    {
        /*
        ob_start();
        var_dump($this->__debugInfo());
        $result = ob_get_clean();
        */

        $debug = var_export($this->__debugInfo(), true);
        
        return Stringly::Create("<pre>".$debug."</pre>");
        
    }



    /**
     * encase() : Stringly
     * 
     * @since 2.0 - Initial
     *  
     * @return Stringly
     *
     * @author Sam McDonald
     * 
     */
    public function encase(String $start, String $end = null) : Stringly
    {
        $end = ($end == null) ? $start : $end;
        return static::FromArray([$start, $this->value, $end]);
    }



    /**
     * endsWith($string, bool) : bool
     * 
     * @since 2.0 - Initial
     *  
     * @return Stringly
     *
     * @author Sam McDonald
     * 
     */
    public function endsWith(string $string, bool $case_sensitive = true) : bool
    {
        $needle = static::Create($string);
        $length = $needle->length();
        if ($length == 0) {
            return true;
        }

        if($case_sensitive == false)
        {
            return ( $this->toLower()->substring(-$length)) == $needle->toLower()->toString();
        }

        return ( $this->substring(-$length)) == $needle->toString();

    }



    /**
     * Split string into array using delimiter
     * 
     * @param  string $delimiter [description]
     * @param  [type] $limit     [description]
     * @return [type]            [description]
     */
    public function explode($delimiter = ' ', $limit = null)
    {
        if($delimiter == '')
        {
            $delimiter = ' ';
        }

        if(($limit === null) || ($limit === false))
        {
            return explode($delimiter, $this->value);
        }

        return explode($delimiter, $this->value, $limit);
    }   



    /**
     * firstIndexOf($string) : int
     * 
     * @since 1.0 - Initial
     *  
     * @return int Position of the first index of a string. null given is the input does not exist.
     *
     * @author Sam McDonald
     * 
     * @deprecated - use indexOf()
     * 
     */
    public function firstIndexOf(string $needle = '', bool $case_sensitive = true) : int
    {
        return $this->indexOf ($needle, 0, $case_sensitive);
    }


    /*
    public function format(...$values)
    {
        return vprintf($this->value, $values);
    }
    */






    /**
     * getEncoding() : String
     * 
     * @since 1.0 - Initial
     *  
     * @return String The encoding type of the given String.
     * @author Sam McDonald
     */
    public function getEncoding()
    {
        return $this->encoding;
    }



    /**
     * firstIndexOf($string) : int
     * 
     * @since 2.0 - Initial
     *  
     * @return int Position of the first index of a string. null given is the input does not exist.
     *
     * @author Sam McDonald
     * 
     * 
     */
    public function indexOf($needle, $offset = 0, bool $case_sensitive = true) : int
    {
        $func   = ($this->has_mbstring) ? 'mb_strpos'  : 'strpos';
        $funci  = ($this->has_mbstring) ? 'mb_stripos' : 'stripos';
        
        return ($case_sensitive)? $func($this->value, $needle, $offset) : $funci($this->value, $needle, $offset) ; 
    }


    /**
     * isAlpha() : bool
     * 
     * @since 2.0 - Initial
     *  
     * @return bool If the given string is and only is Alpha characters 
     *              in either ASCII oir UTF-8 thenb true will be 
     *              returned. Otherwise false is returned.
     * 
     */
    public function isAlpha() : bool
    {
        return ctype_alpha( $this->value );
    }


    
    /**
     * isAlphaNumeric() : bool
     * 
     * @since 2.0 - Initial
     *  
     * @return bool If the given string contains both Numeric and  Alphabetical characters 
     *              in either ASCII oir UTF-8 thenb true will be 
     *              returned. Otherwise false is returned.
     * 
     */
    public function isAlphaNumeric() : bool
    {
        //ascii
        //return preg_match('/^[a-z0-9 .\-]+$/i', $this->value);

        //unicode
        return preg_match('/^[\p{L}\p{N} .-]+$/',  $this->value);
        
        //return ctype_alnum ( $this->value );
    }


    /**
     * isDigitsOnly() : bool
     * 
     * @since 2.0 - Initial
     *  
     * @return bool If the given string contains digits and only digits.
     * 
     */
    public function isDigitsOnly() : bool
    {
        return ctype_digit ( $this->value );
    }

    //---------------------------------------------------------
        
    /**
     * Is the encoding valid ?
     * 
     * @return bool If multi-Byte string then bool is returned if valid. 
     *              Will always return True for non-mb-strings. 
     */
    public function isEncodingValid() 
    {
        if($this->has_mbstring) 
        {      
            return \mb_check_encoding($this->value, $this->encoding);
        }

        return "unknown";
    }



    public function isEqualTo($string) : bool
    {
        return $this->equalTo($string);
    }

    // @deprecated
    public function equalTo($string) : bool
    {
        if($string instanceof Stringly)
        {
            return $this === $string;
        }

        if(is_string($string))
        {
            return $this->value === $string;
        }

        return $this === $string;
    }



        /**
     * Is this string a multibyte styring
     * By comparing the lengths of mb_stringlen with length
     * 
     * If the function not supported then null provided
     */
    public function isMultiByteString()
    {
        // check if the string doesn't contain invalid byte sequence
        if (mb_check_encoding($this->value, 'UTF-8') === false) 
            return false;

        $length = mb_strlen($this->value, 'UTF-8');
    
        for ($i = 0; $i < $length; $i += 1) {
    
            $char = mb_substr($this->value, $i, 1, 'UTF-8');
    
            // check if the string doesn't contain single character
            if (mb_check_encoding($char, 'ASCII')) {
                return false;
            }
        }

        return true;

    }


    
    /**
     * Check if string is considered numeric
     * 
     * @return boolean [description]
     */
    public function isNumeric(bool $as_text = false)
    {
        $val = is_numeric($this->value);

        if(!$as_text)
            return $val;

        if($val == true)
            return "true";
        
        return "false";

    }





    /**
     * Gets the last indexOf a string
     * 
     * @param  string       $needle         [description]
     * @param  int|integer  $offset         [description]
     * @param  bool|boolean $case_sensitive [description]
     * @return [type]                       [description]
     */
    public function lastIndexOf(string $needle = '', int $offset = 0, bool $case_sensitive = true)
    {

        $func   = ($this->has_mbstring) ? 'mb_strrpos'   : 'strrpos';
        $funci  = ($this->has_mbstring) ? 'mb_strripos'  : 'strripos';
        
        return ($case_sensitive)? $func($this->value, $needle, $offset) : $funci($this->value, $needle, $offset) ;        
    }


    /**
     * Gets the length of a string  and forces MB support
     * 
     * @return [type] [description]
     */
    public function length()
    {
        return $this->count();
    }




    /**
     * same as truncate ??
     * Truncate the string to given length of characters.
     *
     * @param string  $string The variable to truncate
     * @param integer $limit  The length to truncate the string to
     * @param string  $append Text to append to the string IF it gets
     *                        truncated, defaults to '...'
     * @return string
     */
    public static function limit_characters($string, $limit = 100, $append = '...')
    {
        if (mb_strlen($string) <= $limit) {
            return $string;
        }
        return rtrim(mb_substr($string, 0, $limit, 'UTF-8')) . $append;
    }


    
    /**
     * same as truncate ??
     *
     * @param $string
     * @param $limit
     * @param string $append
     * @return string
     */
    public static function limit_words($string, $limit = 100, $append = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);
        if (!isset($matches[0]) || strlen($string) === strlen($matches[0])) {
            return $string;
        }
        return rtrim($matches[0]).$append;
    }





    //http://php.net/manual/en/function.mb-strwidth.php
    //http://php.net/manual/en/function.mb-substr-count.php


    

    /**
     * Pads a given string with zeroes on the left.
     *
     * @param  int  $length The total length of the desired string
     * @return string
     */
    public static function padLeft(String $char = '0', $length = 2)
    {
        return Stringly::Crteate(str_pad($this->value, $length, $char, STR_PAD_LEFT));
    }


    
    /**
     * Pads a given string with zeroes on the left.
     *
     * @param  int  $length The total length of the desired string
     * @return string
     */
    public static function padRight(String $char = '0', $length = 2)
    {
        return Stringly::Create(str_pad($this->value, $length, $char, STR_PAD_RIGHT));
    }



    /**
     * Prepend a string to the stringly
     * 
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public function prepend($string) : Stringly
    {
        return static::FromArray([$string, $this->value]);
    }






    /**
     * Simple string `repea`ter
     * repeat(1) will only show 1 instance of the string, will not rtepeat
     * repeat() will repeat the text and is the same as repeat(2))
     * 
     * @param  int    $by   [description]
     * @param  string $top  [description]
     * @param  string $tail [description]
     * @return [type]       [description]
     */
    public function repeat(int $by = 2, string $top = '', string $tail = '')
    {
        $new_str = str_repeat($top.$this->value.$tail, $by);
        return static::Create($new_str, $this->encoding);
    }

    //
    // adapted from 
    // https://github.com/alecgorge/PHP-String-Class/blob/master/string.php
    //
    public function replace($needle, $replace='', $count = null, $case_sensitive = true) 
    {
        if($this->has_mbstring)
        {
            //case sensitive on always for mb for now
            // more work needed to fix this !
            $r = mb_str_replace($needle, $replace, $this->value, $count);
        }
        else 
        {
            $fn = ($case_sensitive)? "str_replace" : "str_ireplace";

            $r = $fn($needle, $replace, $this->value, $count); 
        }
        
        
		return static::Create($r);
	}



    /*
    public function replace($clean, $cleanWith = '', $times = null)
    {
        return $this->rinse($clean, $cleanWith, $times);
    }
*/

    /**
     * Reverse order of characters
     * 
     * @return [type] [description]
     */
    public function reverse()
    {
        $reversed = '';

        if($this->has_mbstring)
        {
            $strLength = $this->length();

            // Loop starting from last index of string to first
            for ($i = $strLength - 1; $i >= 0; $i--) 
            {
                $reversed .= \mb_substr($this->value, $i, 1, $this->encoding);
            }
        }
        else
        {
            $reversed = strrev($this->value);
        }
        
        return static::Create($reversed, $this->encoding);
    }



    /**
     * rinse($character) : Stringly
     * 
     * @version     1.0
     * @since       2.0
     * @see         Stringly->replace()
     * @author      Sam McDonald
     * @return      Stringly
     * 
     * Other
     * ========
     * Multi-byte Support since version 2.0
     * 
     * 
     * 
     * Originally in version 1.0, Stringly did not have a "replace()"  method. Now that 2.0 includes
     * replace(), rinse() has been changed to reflect a sinple rinse() procedure
     * rather than a full search/replace() utility.
     * 
     * Example:
     * To replace in 1.0, $stringly->rinse("*", " ");
     * In 2.0, we now use $stringly->replace("*", " ");
     * 
     * Fort 2.0, rinse() will be for simple removal of characters.
     * Example: $stringly->rinse("*"); will remove 
     * all "*" characters but doesa not allow a 
     * replacing of them. In future versions 
     * we plan of extending rinse to 
     * allow for.
     * 
     * $stringly->rinse("!@#%^&*");
     * 
     * What Stringly will do is treat this as a character array and 
     * remove each character from the subject Stringly.
     * 
     * 
     */
    public function rinse($dirty)
    {
        if($this->has_mbstring)
        {
            return static::Create(mb_str_replace($dirty, '', $this->value), $this->encoding);
        }

        return static::Create(str_replace($dirty, '', $this->value), $this->encoding);
    }
    /*
    public function rinse($dirty)
    {
        if(is_array($dirty))
        {
            foreach($dirty as $dirt)
            {
                // if this is a string, lets clean it
                return $this->rinse($dirt);
            }
        }

        if($this->has_mbstring)
        {
            return static::Create(mb_str_replace($dirty, '', $this->value), $this->encoding);
        }

        return static::Create(str_replace($dirty, '', $this->value), $this->encoding);
    }
    */


    //---------------------------------------------



 

    public function selectMBSupprt($enabled = true)
    {
        $this->_enableMBSupport = $enabled;
    
        if($enabled)
        {
            $this->has_mbstring = extension_loaded('mbstring');
        }
        else
        {
            $this->has_mbstring = false;
        }
    
        return $this->_enableMBSupport;
    }



    /**
     * Shuffle/Scramble letters
     * 
     * @return [type] [description]
     */
    public function shuffle()
    {
        $shuffled = '';
        if($this->has_mbstring)
        {
            $indexes = range(0, $this->length() - 1);

            shuffle($indexes);

            foreach ($indexes as $i) 
            {
                $shuffled .= \mb_substr($this->value, $i, 1, $this->encoding);
            }
        }
        else
        {
            $shuffled = str_shuffle($this->value);
        }

        return static::make($shuffled, $this->encoding);
    }

    
    
    
    
    /**
     * Split string into array using regex pattern.
     * 
     * @param  [type] $pattern [description]
     * @param  [type] $limit   [description]
     * @return [type]          [description]
     */
    public function split($pattern, $limit = null)
    {
        if(($limit === null) || ($limit === false))
        {
            return (($this->has_mbstring)? mb_split($pattern, $this->value) : split($pattern, $this->value));
        }    

        return (($this->has_mbstring)? mb_split($pattern, $this->value, $limit) : split($pattern, $this->value, $limit));
    }   



    public function startsWith(string $string, bool $case_sensitive = true) : bool
    {
        $queryString = static::Create($string);
        $len = $queryString->length(); 
    
        $searchString = $this->substring(0, $len);
    
        if (!$case_sensitive) 
        {
            $string = $queryString->toLowerCase();
            $searchString = $searchString->toLowerCase();
        }
    
        return $string === $searchString;
    }
    
    

    /**
     * No WhiteSpace
     *
     * @param  string 
     * @return string
     */
    public function stripSpace()
    {
        return preg_replace('/\s+/', '', $this->value);
    }


    
    /**
     * Get the substring of the stringly
     * 
     * @param  int|integer $start  [description]
     * @param  int|null    $length [description]
     * @return [type]              [description]
     */
    public function substring(int $start = 0, int $length = null)
    {
        if($this->has_mbstring)
        {
            return static::Create(\mb_substr($this->value, $start, $length, $this->encoding));   
        }
    
        return static::Create(substr($this->value, $start, $length));
    }
    
    
    
        
    
    public function toCrypt($salt = "") 
    {
        return static::Create(crypt ( $this->value, $salt ),$this->encoding);
    }


    
        /**
         * Sets the encoding
         * 
         * Now by default will set to UTF-8
         * 
         * @param [type] $to_encoding [description]
         * @param bool @translate   Should the class translate the char set based on new encoding ?
         * 
         * @return String The encoding set by user or default/system for the given string.
         */
        public function toEncoding($to_encoding = 'UTF-8') : Stringly
        {
            if($this->has_mbstring) 
            {
                foreach(\mb_list_encodings() as $ENC)
                {
                    if($to_encoding == $ENC)
                    {
                        $output = \mb_convert_encoding ( $this->value, $to_encoding, $this->encoding );
    
                        $stringly = new static($output, $to_encoding, false);
    
                        return $stringly;
    
                    }
                }
            }
    
            // log error
    
            return new static($this->value, 'UTF-8', false);
        }


    /**
     * Lowercase the string
     * 
     * @return [type] [description]
     */
    public function toLower()
    {
        return $this->toLowerCase();
    }

    public function toLowerCase()
    {
        return static::Create(($this->has_mbstring)? mb_strtolower($this->value) : strtolower($this->value));
    }

    public function toMd5()
    {
        return static::Create(md5($this->value),$this->encoding);
    }



    public function toSha1() 
    {
        return static::Create(sha1($this->value),$this->encoding);
    }

    public function toSha256(bool $raw_output = FALSE ) 
    {
        return static::Create(hash("sha256" , $this->value, $raw_output ) ,$this->encoding);
    }


    /**
     * echo $stringly->toString();
     * 
     * @return [type] [description]
     */
    public function toString()
    {
        return $this->value;
    }

    


    /**
     * Converts the first character of each word in the string to uppercase.
     *
     * @return static Object with all characters of $str being title-cased
     */
    public function toTitleCase()
    {
        if($this->has_mbstring)
        {
            $string = \mb_convert_case($this->value, \MB_CASE_TITLE, $this->encoding);
            return static::Create($string);
        }

        return static::Create(ucwords($this->toLowerCase()));
    }




    /**
     * To Uppercase the first letter
     * 
     * @return [type] [description]
     */
    public function toUcfirst()
    {
        if(($this->has_mbstring))
        {
            $ucfirst = mb_strtoupper(mb_substr($this->value, 0, 1), $this->encoding);
            $string =  $ucfirst.mb_substr($this->value, 1, ($this->length() - 1), $this->encoding);
            return static::Create($string);
        }

        return new static(ucfirst($string));
    }


    
    /**
     * UpperCase the string
     * 
     * @return [type] [description]
     */
    public function toUpperCase()
    {
        return static::Create(($this->has_mbstring)? mb_strtoupper($this->value) : strtoupper($this->value));
    }

    public function toUpper()
    {
        return $this->toUpperCase();
    }




    /**
     * Trim whitespaces and other special characters
     * 
     * @return [type] [description]
     */
    public function trim()
    {
        $newstr =   ($this->has_mbstring)? 
                    preg_replace("/(^\s+)|(\s+$)/us", "", $this->value) :
                    trim($this->value) ;

        return static::Create($newstr);
    }


    /**
     * Truncate string to length
     * 
     * @param  int    $max_length [description]
     * @return [type]             [description]
     */
    public function truncate(int $max_length)
    {
        if($this->length() > $max_length)
        {
            return static::Create($this->substring(0,$max_length));
        }

        //no change, just return self
        return $this;
    }


    /**
     * To count a particular word, use contains().
     * To count all words use wordCount()
     */
    public function wordCount()
    {
        return str_word_count($this->value);
    }



#endregion


    //


    /**
     * Does it?
     * was inh 1.0, changed to statiic
     * 
     * @return boolean [description]
     */
    public static function hasMultiByteSupport()
    {
        return $this->has_mbstring;
    }





    /**
     * hasMB Support sdhould be changed to static only, the variable 
     * does not need to have a checker but can still contain a 
     * variable to hold in mewmory
     * 
     * @return boolean [description]
     */
    /*
    public function HasMultiByteSupport() : bool
    {
        return extension_loaded('mbstring');
    }
    */











    /**
     * Decode html entities
     * 
     * http://php.net/manual/en/function.html-entity-decode.php
     *
     */
    public function htmlDecode($flags = ENT_COMPAT)
    {
        $string = html_entity_decode($this->value, $flags, $this->encoding);
        return static::make($string, $this->encoding);
    }

    /**
     * Encode html entities
     * 
     * Refer to http://php.net/manual/en/function.htmlentities.php
     *
     */
    public function htmlEncode($flags = ENT_COMPAT)
    {
        $string = htmlentities($this->value, $flags, $this->encoding);
        return static::make($string, $this->encoding);
    }

    /**
     * Strip html tags
     * 
     * @param  string $allowable_tags [description]
     * @return [type]                 [description]
     */
    public function htmlStripTags(string $allowable_tags = '')
    {
        $string = strip_tags($this->value, $allowable_tags);
        return static::make($string, $this->encoding);
    }

    /**
     * Deep cloning
     * @todo  Stil need to implement __clone
     * @return [type] [description]
     */
    public function deepClone() : Stringly
    {
        $new_object = unserialize(serialize($this));
        $stringly = static::Create($new_object);
        return $stringly;
    }






    /**
     * Convert tabs (\t) to spaces
     * 
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    /*
    public function toSpaces($length = 2)
    {
        $spaces = str_repeat(' ', $length);
        $string = str_replace("\t", $spaces, $this->value);
        return static::make($string, $this->encoding);
    }
    */








     /**
     * Generates a string of random characters.
     *
     * @throws  LengthException  If $length is bigger than the available
     *                           character pool and $no_duplicate_chars is
     *                           enabled
     *
     * @param   integer $length             The length of the string to
     *                                      generate
     * @param   boolean $human_friendly     Whether or not to make the
     *                                      string human friendly by
     *                                      removing characters that can be
     *                                      confused with other characters (
     *                                      O and 0, l and 1, etc)
     * @param   boolean $include_symbols    Whether or not to include
     *                                      symbols in the string. Can not
     *                                      be enabled if $human_friendly is
     *                                      true
     * @param   boolean $no_duplicate_chars Whether or not to only use
     *                                      characters once in the string.
     * @return  string
     */
    /*
    public static function random_string($length = 16, $human_friendly = true, $include_symbols = false, $no_duplicate_chars = false)
    {
        $nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
        $all_an     = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $symbols    = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
        $string     = '';
        // Determine the pool of available characters based on the given parameters
        if ($human_friendly) {
            $pool = $nice_chars;
        } else {
            $pool = $all_an;
            if ($include_symbols) {
                $pool .= $symbols;
            }
        }
        if (!$no_duplicate_chars) {
            return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
        }
        // Don't allow duplicate letters to be disabled if the length is
        // longer than the available characters
        if ($no_duplicate_chars && strlen($pool) < $length) {
            throw new \LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
        }
        // Convert the pool of characters into an array of characters and
        // shuffle the array
        $pool       = str_split($pool);
        $poolLength = count($pool);
        $rand       = mt_rand(0, $poolLength - 1);
        // Generate our string
        for ($i = 0; $i < $length; $i++) {
            $string .= $pool[$rand];
            // Remove the character from the array to avoid duplicates
            array_splice($pool, $rand, 1);
            // Generate a new number
            if (($poolLength - 2 - $i) > 0) {
                $rand = mt_rand(0, $poolLength - 2 - $i);
            } else {
                $rand = 0;
            }
        }
        return $string;
    }
*/





    /**
     * Wrapper to prevent errors if the user doesn't have the mbstring
     * extension installed.
     *
     * @param  string $encoding
     * @return string
     */
    protected static function mbInternalEncoding($encoding = null)
    {
        if (function_exists('mb_internal_encoding')) {
            return $encoding ? mb_internal_encoding($encoding) : mb_internal_encoding();
        }
        // @codeCoverageIgnoreStart
        return 'UTF-8';
        // @codeCoverageIgnoreEnd
    }




    /**
     * Good for a number version of stringly
     * Numberly
     * Returns the ordinal version of a number (appends th, st, nd, rd).
     *
     * @param  string $number The number to append an ordinal suffix to
     * @return string
     */
    /*
    public static function ordinal($number)
    {
        $test_c = abs($number) % 10;
        $ext = ((abs($number) % 100 < 21 && abs($number) % 100 > 4) ? 'th' : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
        return $number . $ext;
    }
*/





    public function createCharacter($code, $encoding = null)
    {
        //http://php.net/manual/en/function.mb-chr.php
        //chr(127)
        //mb_chr(774);
    }

    
    /*
     |
     |
     |
     | Special Members
     |
     |
     |
     |
     */




    /**
     * @since 1.1 - 2019
     */
    public function __debugInfo() 
    {
        return 
        [
            'value:'                 => $this->value,
            'isNumeric:'             => $this->isNumeric(),
            'isAlpha:'               => $this->isAlpha(),
            'isAlphaNumeric:'        => $this->isAlphaNumeric(),
            'isDigitsOnly:'          => $this->isDigitsOnly(),
            'hasMultiByteSupport:'   => $this->hasMultiByteSupport(),            
            'containsMultibyte:'     => $this->containsMultibyte(),
            'isMultiByteString:'     => $this->isMultiByteString(),
            'isEncodingValid:'       => $this->isEncodingValid(),
            'IsEncodingValid:'       => self::IsEncodingValid($this->value,$this->getEncoding()),                      
            'getEncoding:'           => $this->getEncoding(),
            'containsPuctuation:'    => $this->containsPuctuation(),
            'containsPrintable:'     => $this->containsPrintable(),  
            'containsControlChar:'   => $this->containsControlChar(),
            
        ];
    }


    /**
     * Convert the Stringly to its string representation.
     *
     * echo $stringly
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }




    
    /*
     |
     |
     |
     | Static Members
     |
     |
     |
     |
     */


    
    /**
     * Is Valid encoding
     * 
     * Now by default will set to UTF-8
     * 
     * @param [type] $to_encoding [description]
     * @param bool @translate   Should the class translate the char set based on new encoding ?
     * 
     * @return String The encoding set by user or default/system for the given string.
     */
    /*
    public static function IsEncodingValid(String $string, $encoding = 'ASCII') : bool
    {
        if(extension_loaded('mbstring')) 
        {
            return \mb_check_encoding($string, $encoding );
        }

        return true;
    } */   



 





    /*
     |
     |
     |
     | Still need to work on the functions below
     |
     |
     |
     |
     |
     */

    

    public static function Serialize(\SamMcDonald\Stringly $stringly)
    {
        return serialize($stringly);
    }



    // public function isBase64()
    // {
    //     return (string) (base64_encode(base64_decode($this->value, true)) === $this->value);
    // }
 
    // public function getIterator() 
    // {
    //     return new \ArrayIterator($this->explode(''));
    // }  

    // public function toJson($options = 0)
    // {
    //     return json_encode($this->value, $options);
    // }

    // public function toArray()
    // {
    //     return $this->split();
    // }


}


/**
 * http://iamseanmurphy.com/mb-str-replace-the-missing-php-function/
 * Replace all occurrences of the search string with the replacement string.
 *
 * @author Sean Murphy <sean@iamseanmurphy.com>
 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
 * @license http://creativecommons.org/publicdomain/zero/1.0/
 * @link http://php.net/manual/function.str-replace.php
 *
 * @param mixed $search
 * @param mixed $replace
 * @param mixed $subject
 * @param int $count
 * @return mixed
 */
if (!function_exists('mb_str_replace')) {
	function mb_str_replace($search, $replace, $subject, &$count = 0) {
		if (!is_array($subject)) {
			// Normalize $search and $replace so they are both arrays of the same length
			$searches = is_array($search) ? array_values($search) : array($search);
			$replacements = is_array($replace) ? array_values($replace) : array($replace);
			$replacements = array_pad($replacements, count($searches), '');
			foreach ($searches as $key => $search) {
				$parts = mb_split(preg_quote($search), $subject);
				$count += count($parts) - 1;
				$subject = implode($replacements[$key], $parts);
			}
		} else {
			// Call mb_str_replace for each subject in array, recursively
			foreach ($subject as $key => $value) {
				$subject[$key] = mb_str_replace($search, $replace, $value, $count);
			}
		}
		return $subject;
	}
}