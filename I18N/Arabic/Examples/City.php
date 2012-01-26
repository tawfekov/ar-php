<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Arabic Cities List</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" media="all" />
</head>

<body>
<div class="Paragraph">
<h2 dir="ltr">Cities Example Output</h2>
<?php
/**
 * Example of Cities Example Output
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
  
try {
    /*** connect to SQLite database ***/
    $dbh = new PDO('sqlite:../data/cities.db');

    $city_id  = $_GET['city'];
    if (!$city_id) { 
        $city_id = 1;
    }

    $sql = "select * from city where parent_id=$city_id and id>1 order by arabic";
        
    echo '<form action="City.php" method="get" name="frm"><p align="center">';
    echo "Selected Node ID: $city_id <br /><br />";

    $sth = $dbh->prepare("select * from city where id=$city_id");
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    echo $result['arabic'] . ' / ' . $result['english'] . '<br />';
    if (!empty($result['latitude'])) {
        echo 'Latitude: ' . $result['latitude'];
        echo ', Longitude: ' . $result['longitude'];
    }

    echo '<br /><br /><select name="city" dir="rtl" onChange="document.frm.submit()">';
    echo '<option>- إختر رجاء -</option>';

    /*
    * You will have noticed that we can iterate over the result set directly
    * with foreach. This is because internally the PDO statement implements 
    * the SPL traversble iterator, thus giving all the benifits of using SPL.
    *
    * The greatest benifit of this is that SPL iterators know only one element 
    * at a time and thus large result sets become manageable without hogging
    * memory.
    */             
    foreach ($dbh->query($sql) as $row) {
        echo '<option value="' . $row['id'] . '">';

        if ($row['arabic'] == '') {
            $title = $row['english'];
        } else {
            $title = $row['arabic'];
        }

        echo "$title</option>\n";
    }

    echo '</select> ';
    echo '<input type="button" onclick="window.location=\'City.php\';"
           value="قم باختيار جديد" /></form></p>';
    
    // Close the databse connection
    $dbh = null; 
} catch(PDOException $e) {
    echo $e->getMessage();
}
?>
</div><br />

<div class="Paragraph">
<h2>Cities Example Code:</h2>
<?php
$code = <<< END
<?php
  try {
      /*** connect to SQLite database ***/
      \$dbh = new PDO('sqlite:../data/cities.db');

      \$city_id  = \$_GET['city'];
      if (!\$city_id) { \$city_id = 1; } 

      \$sql = "select * from city where parent_id=\$city_id and id>1 order by arabic";
          
      echo '<form action="City.php" method="get" name="frm"><p align="center">';
      echo "Selected Node ID: \$city_id <br /><br />";

      \$sth = \$dbh->prepare("select * from city where id=\$city_id");
      \$sth->execute();
      \$result = \$sth->fetch(PDO::FETCH_ASSOC);

      echo \$result['arabic'] . ' / ' . \$result['english'] . '<br />';
      if (!empty(\$result['latitude'])) {
          echo 'Latitude: ' . \$result['latitude'];
          echo ', Longitude: ' . \$result['longitude'];
      }

      echo '<br /><br /><select name="city" dir="rtl" onChange="document.frm.submit()">';
      echo '<option>- إختر رجاء -</option>';

     /*
      * You will have noticed that we can iterate over the result set directly
      * with foreach. This is because internally the PDO statement implements 
      * the SPL traversble iterator, thus giving all the benifits of using SPL.
      *       
      * The greatest benifit of this is that SPL iterators know only one element 
      * at a time and thus large result sets become manageable without hogging 
      * memory.
      */             
      foreach (\$dbh->query(\$sql) as \$row) {
          echo '<option value="' . \$row['id'] . '">';

          if (\$row['arabic'] == '') {
              \$title = \$row['english'];
          } else {
              \$title = \$row['arabic'];
          }

          echo "\$title</option>\\n";
      }
  
      echo '</select> ';
      echo '<input type="button" onclick="window.location=\'City.php\';"
             value="قم باختيار جديد" /></form></p>';
      
      // Close the databse connection
      \$dbh = null; 
  } catch(PDOException \$e) {
      echo \$e->getMessage();
  }
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