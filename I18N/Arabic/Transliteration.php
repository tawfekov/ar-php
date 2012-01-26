<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (c) 2006-2012 Khaled Al-Sham'aa.
 *  
 * http://www.ar-php.org
 *  
 * PHP Version 5 
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: English-Arabic Transliteration
 *  
 * Filename:   Transliteration.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled@ar-php.org>
 *  
 * Purpose:    Transliterate English words into Arabic by render them
 *             in the orthography of the Arabic language and vise versa  
 *              
 * ----------------------------------------------------------------------
 *
 * English-Arabic Transliteration
 *    
 * PHP class transliterate English words into Arabic by render them in the 
 * orthography of the Arabic language and vise versa.
 *    
 * Out of vocabulary (OOV) words are a common source of errors in cross language 
 * information retrieval. Bilingual dictionaries are often limited in their coverage 
 * of named- entities, numbers, technical terms and acronyms. There is a need to 
 * generate translations for these "on-the-fly" or at query time.
 * 
 * A significant proportion of OOV words are named entities and technical terms. 
 * Typical analyses find around 50% of OOV words to be named entities. Yet these 
 * can be the most important words in the queries. Cross language retrieval 
 * performance (average precision) reduced more than 50% when named entities in the 
 * queries were not translated.
 * 
 * When the query language and the document language share the same alphabet it may 
 * be sufficient to use the OOV word as its own translation. However, when the two 
 * languages have different alphabets, the query term must somehow be rendered in 
 * the orthography of the other language. The process of converting a word from one 
 * orthography into another is called transliteration.
 * 
 * Foreign words often occur in Arabic text as transliteration. This is the case for 
 * many categories of foreign words, not just proper names but also technical terms 
 * such as caviar, telephone and internet.
 * 
 * Example:
 * <code>
 *   include('./I18N/Arabic.php');
 *   $obj = new I18N_Arabic('Transliteration');
 *     
 *   $ar_word_1 = $obj->en2ar($en_word_1);
 *   $en_word_2 = $obj->ar2en($ar_word_2);
 * </code>
 *             
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2012 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */

// New in PHP V5.3: Namespaces
// namespace I18N\Arabic;
// 
// $obj = new I18N\Arabic\Transliteration();
// 
// use I18N\Arabic;
// $obj = new Arabic\Transliteration();
//
// use I18N\Arabic\Transliteration as Transliteration;
// $obj = new Transliteration();

/**
 * This PHP class transliterate English words into Arabic
 *  
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2012 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
class I18N_Arabic_Transliteration
{
    private static $_arFinePatterns     = array("/'+/", "/([\- ])'/", '/(.)#/');
    private static $_arFineReplacements = array("'", '\\1', "\\1'\\1");
    
    private static $_en2arPregSearch  = array();
    private static $_en2arPregReplace = array();
    private static $_en2arStrSearch   = array();
    private static $_en2arStrReplace  = array();
    
    private static $_ar2enPregSearch  = array();
    private static $_ar2enPregReplace = array();
    private static $_ar2enStrSearch   = array();
    private static $_ar2enStrReplace  = array();

    /**
     * Loads initialize values
     *
     * @ignore
     */         
    public function __construct()
    {
        $xml = simplexml_load_file(dirname(__FILE__).'/data/Transliteration.xml');

        foreach ($xml->xpath("//preg_replace[@function='ar2en']/pair") as $pair) {
            array_push(self::$_ar2enPregSearch, (string)$pair->search);
            array_push(self::$_ar2enPregReplace, (string)$pair->replace);
        }

        foreach ($xml->xpath("//str_replace[@function='ar2en']/pair") as $pair) {
            array_push(self::$_ar2enStrSearch, (string)$pair->search);
            array_push(self::$_ar2enStrReplace, (string)$pair->replace);
        }

        foreach ($xml->xpath("//preg_replace[@function='en2ar']/pair") as $pair) {
            array_push(self::$_en2arPregSearch, (string)$pair->search);
            array_push(self::$_en2arPregReplace, (string)$pair->replace);
        }
    
        foreach ($xml->xpath("//str_replace[@function='en2ar']/pair") as $pair) {
            array_push(self::$_en2arStrSearch, (string)$pair->search);
            array_push(self::$_en2arStrReplace, (string)$pair->replace);
        }
    }
        
    /**
     * Transliterate English string into Arabic by render them in the 
     * orthography of the Arabic language
     *         
     * @param string $string English string you want to transliterate
     *                    
     * @return String Out of vocabulary English string in Arabic characters
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public static function en2ar($string)
    {
        $string = strtolower($string);
        $words  = explode(' ', $string);
        $string = '';
        
        foreach ($words as $word) {
            $word = preg_replace(self::$_en2arPregSearch, 
                                 self::$_en2arPregReplace, $word);
                                      
            $word = str_replace(self::$_en2arStrSearch, 
                                self::$_en2arStrReplace, $word);

            $string .= ' ' . $word;
        }
        
        return $string;
    }

    /**
     * Transliterate Arabic string into English by render them in the 
     * orthography of the English language
     *           
     * @param string $string Arabic string you want to transliterate
     *                    
     * @return String Out of vocabulary Arabic string in English characters
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public static function ar2en($string)
    {
        $string = str_replace('/ة ال/', 'tul', $string);

        $words  = explode(' ', $string);
        $string = '';

        foreach ($words as $word) {
            $temp = preg_replace(self::$_ar2enPregSearch, 
                                 self::$_ar2enPregReplace, $word);

            $temp = str_replace(self::$_ar2enStrSearch, 
                                self::$_ar2enStrReplace, $temp);

            $temp = preg_replace(self::$_arFinePatterns, 
                                 self::$_arFineReplacements, $temp);
            
            $temp = ucwords($temp);
            $pos  = strpos($temp, '-');

            if ($pos > 0) {
                $temp2  = substr($temp, 0, $pos);
                $temp2 .= '-'.strtoupper($temp[$pos+1]);
                $temp2 .= substr($temp, $pos+2);
            } else {
                $temp2 = $temp;
            }

            $string .= ' ' . $temp2;
        }
        
        return $string;
    }
    
    /**
     * Render numbers in given string using HTML entities that will show them as 
     * Arabic digits (i.e. 1, 2, 3, etc.) whatever browser language settings are 
     * (if browser supports UTF-8 character set).
     *         
     * @param string $string String includes some digits here or there
     *                    
     * @return String Original string after replace digits by HTML entities that 
     *                will show given number using Indian digits
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public static function enNum($string)
    {
        $html = '';

        $digits = str_split("$string");

        foreach ($digits as $digit) {
            $html .= preg_match('/\d/', $digit) ? "&#x3$digit;" : $digit;
        }
        
        return $html;
    }
    
    /**
     * Render numbers in given string using HTML entities that will show them as 
     * Indian digits (i.e. ١, ٢, ٣, etc.) whatever browser language settings are 
     * (if browser supports UTF-8 character set).
     *         
     * @param string $string String includes some digits here or there
     *                    
     * @return String Original string after replace digits by HTML entities that 
     *                will show given number using Arabic digits
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public static function arNum($string)
    {
        $html = '';

        $digits = str_split("$string");

        foreach ($digits as $digit) {
            $html .= preg_match('/\d/', $digit) ? "&#x066$digit;" : $digit;
        }
        
        return $html;
    }
}
