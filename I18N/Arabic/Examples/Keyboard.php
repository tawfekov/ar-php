<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Virtual Arabic Keyboard</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>

<div class="Paragraph">
<h2 dir="ltr">Example Output:</h2>
<?php
/**
 * Example of Virtual Arabic Keyboard
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
$time_start = microtime(true);
?>

  <script type="text/javascript" src="../js/vkb_implement.js"></script>
  <script type="text/javascript" src="../js/vkboard.js"></script>
  
  <script type="text/javascript">
      function getNewCaretPositions(ctrl) {
          // set this to false if you don't want keyboard DIV move just below
          // the active input field and stay where it is in your design
          var justBelow = true;
          if (justBelow) {
              var offset = ctrl.offsetTop + ctrl.offsetHeight;
              document.getElementById("keyboard").style.top = offset + "px";
              document.getElementById("keyboard").style.left = ctrl.offsetLeft + "px";
          }
          getCaretPositions(ctrl);
      }
  </script>
  
  <p>
  Changing target input field (anonymous, no ID) on the fly.<br />
  Text is streamed to the field last focused.
  </p>

  <p>Source: 
  <a href="http://www.codeproject.com/KB/scripting/jvk.aspx" target="_blank">JavaScript Virtual Keyboard</a> 
  By Dmitry Khudorozhkov<br />
  Arabic customization by <a href="http://www.ar-php.org" target="_blank">Khaled Al-Shamaa</a></p>

  <p><a href="javascript:keyb_change()" onclick="javascript:blur()">Keyboard [show|hide]</a></p>

  <input dir="rtl" size="40" onkeyup="getNewCaretPositions(this);" onclick="getNewCaretPositions(this);" /> - 
  <input dir="rtl" size="40" onkeyup="getNewCaretPositions(this);" onclick="getNewCaretPositions(this);" /><br /><br />
  <textarea dir="rtl" rows="5" cols="40" onkeyup="getNewCaretPositions(this);" onclick="getNewCaretPositions(this);"></textarea><br />

  <div id="keyboard"></div>

</div><br />
<div class="Paragraph">
<h2>Example Code:</h2>
<?php
$code = <<< END
  <script type="text/javascript" src="../js/vkb_implement.js"></script>
  <script type="text/javascript" src="../js/vkboard.js"></script>
  
  <script type="text/javascript">
      function getNewCaretPositions(ctrl) {
          // set this to false if you don't want keyboard DIV move just below
          // the active input field and stay where it is in your design
          var justBelow = true;
          if (justBelow) {
              var offset = ctrl.offsetTop + ctrl.offsetHeight;
              document.getElementById("keyboard").style.top = offset + "px";
              document.getElementById("keyboard").style.left = ctrl.offsetLeft + "px";
          }
          getCaretPositions(ctrl);
      }
  </script>
  
  <p><a href="javascript:keyb_change()" onclick="javascript:blur()">Keyboard [show|hide]</a></p>

  <input dir="rtl" size="40" onkeyup="getNewCaretPositions(this);" onclick="getNewCaretPositions(this);" /><br /><br />
  <textarea dir="rtl" rows="5" cols="40" onkeyup="getNewCaretPositions(this);" onclick="getNewCaretPositions(this);"></textarea><br />

  <div id="keyboard"></div>
END;

highlight_string($code);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "<hr />Total execution time is $time seconds<br />\n";
echo 'Amount of memory allocated to this script is ' . memory_get_usage() . ' bytes';

$included_files = get_included_files();
echo '<h4>Names of included or required files:</h4><ul>';

foreach ($included_files as $filename) {
    echo "<li>$filename</li>";
}

echo '</ul>';
?>
</div>

</body>
</html>
