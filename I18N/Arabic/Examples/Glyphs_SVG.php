<?php
/**
 * Example of SVG implementation for Arabic glyphs Class
 *
 * @category  I18N
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2011 Khaled Al-Sham'aa
 *
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org
 */

error_reporting(E_STRICT);

// It is better to add code detect browser version and see if it supports 
// bidi algorithm for Arabic language correctly

header('Content-type: image/svg+xml');

require '../../Arabic.php';
$Arabic = new I18N_Arabic('Glyphs');

$text1 = 'الله أكبر';
$text2 = $Arabic->utf8Glyphs($text1);


$SVG = <<<END
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

<svg width="900" height="325" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" direction="rtl" xml:lang="ar">

    <rect x="0" y="0" width="400" height="100" fill="red"/>
    <text x="125" y="175" style="font-size: 44pt; font-weight:bold" fill="green">$text1</text>
    <rect x="0" y="200" width="400" height="100" fill="black"/>
    <text x="150" y="325">Before</text>

    <rect x="500" y="00" width="400" height="100" fill="red"/>
    <text x="625" y="175" style="font-size: 44pt; font-weight:bold" fill="green">$text2</text>
    <rect x="500" y="200" width="400" height="100" fill="black"/>
    <text x="675" y="325">After</text>

</svg>
END;

echo $SVG;
