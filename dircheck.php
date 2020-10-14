<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:         dircheck.php
* @Package:      GetSimple
* @Action:       Bottom Line theme for GetSimple CMS
*
*****************************************************/
?>
<?php
////////////////////////////////////////////////////////////////////////////////
  // Default Timezone Set //////////////////////////////////////////////////////
  date_default_timezone_set('America/Los_Angeles');

  // Variables /////////////////////////////////////////////////////////////////
  $slug = return_page_slug(); // e.g. dircheck/alex means slug is alex
  $previousFile = "dircheck_{$slug}.txt";
  $directory2scan = '/files'; // starting point for directory check
  $dateFormat = "m/d/Y H:i:s";

  /**
   * Given a starting directory this function will scan its contents recursively
   * and return a list of html table rows as an array (one for each file).
   *
   * @param string $dir A directory to scan for files.
   *
   * @return Array A list of html table rows where each row contains a
   *               file name and the date the file was last modified.
   */
  function directoryScan($dir) {
    if (isset($dir) && is_readable($dir)) {
      $table_data = Array();
      $dir = realpath($dir);
      $prefix_length = strlen($dir) + 1;
      $dateFormat = "m/d/Y H:i:s";
      $files = new RecursiveIteratorIterator(
                   new RecursiveDirectoryIterator(
                     $dir,
                     RecursiveDirectoryIterator::SKIP_DOTS // skip . & ..
                   ),
                   RecursiveIteratorIterator::LEAVES_ONLY,
                   RecursiveIteratorIterator::CATCH_GET_CHILD // ignore get err
      );
      foreach($files as $file){
        $file_part = substr($file, $prefix_length);
        $file_time = date ($dateFormat, filemtime($file));
        $table_data[] = "<td>{$file_part}</td><td>{$file_time}</td>";
      }
      return $table_data;
    }
  }

  /**
   * Given an array containing a list of html table data. Place the table data
   * cells into html table rows, and echo the rows out with new lines.
   *
   * @param array $files An array containing html formatted table data cells.
   */
  function displayFiles(array $files) {
    foreach ($files as $file) {
      echo "<tr>{$file}</tr>\n";
    }
  }

  /**
   * Given two arrays return all elements that are not shared between them.
   *
   * @param array $array_a one of two arrays used for comparison.
   * @param array $array_b one of two arrays used for comparison.
   *
   * @return Array A list of all elements that are not shared.
   */
  function array_xor($array_a, $array_b) {
    $union_array = array_merge($array_a, $array_b); // all elements
    $intersect_array = array_intersect($array_a, $array_b); // shared elements
    $xor_array = array_diff($union_array, $intersect_array); // non shared
    return $xor_array;
  }
  //////////////////////////////////////////////////////////////////////////////

  // Read previous file list into an array
  // If it doesn't exist, create an empty array
  if (file_exists($previousFile)) {
    $file_contents = file_get_contents($previousFile);
    $previousFiles = explode("\n",trim($file_contents));
  	$previousCheck = date($dateFormat, filemtime($previousFile));
  } else {
  	$previousFiles = array();
  	$previousCheck = "Never";
  }
  
  // Get list of files from the specified directory
  $currentFiles = directoryScan($directory2scan);

  // Compare file lists
  $differentFiles = array_xor($previousFiles, $currentFiles);
  $deletedFiles   = array_intersect($differentFiles, $previousFiles);
  $addedFiles     = array_intersect($differentFiles, $currentFiles);

  // Sort the file lists
  sort($currentFiles);
  sort($deletedFiles);
  sort($addedFiles);
  
  // Save current list of files for next time
  file_put_contents($previousFile, implode("\n",$currentFiles));
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php ucwords(get_page_clean_title()); ?>&apos;s Directory Check &mdash; <?php get_site_name(); ?></title>
<?php get_header(); ?>
<link href="<?php get_theme_url(); ?>/assets/css/default.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE]><link href="<?php get_theme_url(); ?>/assets/css/IE.css" rel="stylesheet" type="text/css" media="screen" /><![endif]-->
<!--[if lte IE 6]><link href="<?php get_theme_url(); ?>/assets/css/IE6.css" rel="stylesheet" type="text/css" media="screen" /><![endif]-->
<link rel="shortcut icon" href="<?php get_theme_url(); ?>/assets/images/favicon.ico" type="image/x-icon" />
</head>

<body id="dircheck">
  <div id="container">
    <div id="content">
	<h2><?php ucwords(get_page_clean_title()); ?>&apos;s Directory Check</h2>
	<table>
		<tr>
			<th colspan="2">Deleted Files:</th>
		</tr>
                <?php displayFiles($deletedFiles); ?>
		<tr>
			<th colspan="2">Added Files:</th>
		</tr>
		<?php displayFiles($addedFiles); ?>
		<tr>
			<th colspan="2">Last Check: <?php echo $previousCheck; ?></th>
		</tr>
		<?php displayFiles($previousFiles); ?>
		<tr>
			<th colspan="2">Current Check: <?php echo date("m/d/Y H:i:s"); ?></th>
		</tr>
		<?php displayFiles($currentFiles); ?>
	</table>
    </div>
    <a href="#header" id="gototop">Back to top</a>
  </div>
  <ul id="footer">
    <li><strong>Contact us now</strong> on <strong>(412) 441-1083</strong></li>
    <?php get_navigation(return_page_slug()); ?>
  </ul>
  <script type="text/javascript" src="<?php get_theme_url(); ?>/assets/js/mootools-1.2.3-core.js"></script>
  <script type="text/javascript" src="<?php get_theme_url(); ?>/assets/js/mootools-1.2.4.2-more.js"></script>
  <!--<script type="text/javascript" src="<?php get_theme_url(); ?>/assets/js/default.js"></script>-->
  <!--[if IE]>
    <script type="text/javascript" src="<?php get_theme_url(); ?>/assets/js/roundedCorners.js"></script>
  <![endif]-->
  <!--[if lte IE 6]>
    <script type="text/javascript" src="<?php get_theme_url(); ?>/assets/js/ie6-corners.js"></script>
  <![endif]-->
</body>
</html>
