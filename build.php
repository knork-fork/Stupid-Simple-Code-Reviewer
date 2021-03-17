<?php

/**
 * gitwrapper was designed to be single-file install, but with more complex features such code became unmaintainable.
 * As such, parts of code were split into custom .screen files (which corespond to different steps) to make the code more readable.
 * .screen files are not classes and are unrunnable by php interpreter!
 * 
 * Builder works by taking index.screen as starting point and then replacing #TAGS# with values from other screen files,
 * effectively bundling the code together into a single file.
 * CSS (styles.css) and JS (functions.js) assets are automatically included into <style> and <script> tags.
 * 
 * Run builder with `php build.php -o gitwrapper.php` and copy output from gitwrapper.php to desired install location.
 */

echo "Bundling screens to gitwrapper.php...\n";

// Open output file to write to
$output_file = fopen("gitwrapper.php", "w");

// Start from index screen
$output = file_get_contents("src/index.screen");

// Add styles and functions
$styles = file_get_contents("src/assets/styles.css");
$styles = "<style>\n$styles\n</style>";
$output = str_replace("<style></style>", $styles, $output);
$functions = file_get_contents("src/assets/functions.js");
$functions = "<script>\n$functions\n</script>";
$output = str_replace("<script></script>", $functions, $output);

// Add main screen
$output = fill_tag("#TAG_MAIN#", "src/main.screen", $output);

// Add branch pick screen
$output = fill_tag("#TAG_BRANCH_PICK#", "src/branch_pick.screen", $output);

// Add commit list screen
$output = fill_tag("#TAG_COMMIT_LIST#", "src/commit_list.screen", $output);
$output = fill_tag("#TAG_COMMIT_LIST_PHP#", "src/commit_list.screen", $output);

// Add commit diff screen
$output = fill_tag("#TAG_COMMIT_DIFF#", "src/commit_diff.screen", $output);
$output = fill_tag("#TAG_COMMIT_DIFF_PHP#", "src/commit_diff.screen", $output);

// Save code to output and close file
fwrite($output_file, $output);
fclose($output_file);

echo "\e[1;32mDone!\e[0m\nDon't forget to commit changes in output file after build!\n";

/***********************/

function fill_tag($tag, $filepath, $output)
{
    $code = file_get_contents($filepath);

    // Get content inside tag from screen file
    $code = get_string_between($code, $tag, $tag);

    // Replace tag in index screen with content from screen file
    $output = str_replace($tag, $code, $output);

    return $output;
}

function get_string_between($string, $start, $end)
{
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}