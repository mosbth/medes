<?php
// ---------------------------------------------------------------------------------------------
//
// Do some initial checking, validating and defining/setting of variables that will be used
// all through the script. 
//
$output = "";
//$imageDir	= "upload";
$imageDir	= "upload";

$FILE_UPLOAD_DISABLED=false;
if(is_readable('config.php')) {	require_once('config.php'); }

if($FILE_UPLOAD_DISABLED == true) {
	die("<strong><em>File upload is disabled.</em></strong>");
}

if(!isset($imageDir)) {
	die("<strong><em>Fileupload is enabled but missing directory to store files. Define \$imageDir and ensure its a writable directory.</em></strong>");
}

$dir = $pp->installPath . "/$imageDir";
if(!(is_dir($dir) && is_writable($dir))) {
	die("<strong><em>The directory: '{$dir}' does not exists or is not writeable by the webserver.</em></strong>");
}


// ---------------------------------------------------------------------------------------------
//
// UPLOAD IMAGES
// Upload images to the server.
//
//	http://php.net/manual/en/features.file-upload.post-method.php
//	
if(!empty($_POST['doFileUpload'])) {
	$uploadfile = "$dir/" . basename($_FILES['userfile']['name']);
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    $output .= "Filen laddas upp. ";
	} else {
    $output .= "Något gick fel när filen laddades upp. ";
	}
}


// ---------------------------------------------------------------------------------------------
//
// DELETE IMAGES
// Delete images from the server.
//	
if(!empty($_GET['doRemoveImage'])) {
	$file = basename(strip_tags($_GET['doRemoveImage']));
	if(is_file("$dir/$file")) {
		unlink("$dir/$file");
    $output .= "Bilden $dir/$file raderas. ";
	}
}


// ---------------------------------------------------------------------------------------------
//
// DISPLAY LIST OF IMAGES
// Display the images that were uploaded.
//
$files = readDirectory($dir);
sort($files);

$images = "";
foreach($files as $val) {
	$parts = pathinfo("$dir/$val");
	if(is_file("$dir/$val") && isset($parts['extension']) && $parts['extension'] != 'php') {
		$del = "";
		if(is_writeable("$dir/$val")) {
			$del = " <a href='?p={$p}&amp;doRemoveImage=$val' title='Radera bilden'>x</a>";
		}
		$images	.= "<a href='{$pp->siteUrl}/$imageDir/$val' title='Visa bilden {$pp->siteUrl}/$imageDir/$val'>$val</a>{$del}<br>";
	}
}


// ------------------------------------------------------------------------------
//
// Set $page to contain html for the page
//
$page = <<<EOD
<h1>Upload files and images</h1>
<p><a href="{$pp->siteUrl}/{$imageDir}">Följande bilder finns sparade</a>
<p>{$images}</p>

<form enctype="multipart/form-data" action="?p={$p}" method=post>
 <fieldset>
	<legend>Upload files and images</legend>

		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000" />
		<!-- Name of input element determines name in $_FILES array -->
		<input name="userfile" type="file" />
		<input type="submit" name="doFileUpload" value="Ladda upp" />
	
	<output>{$output}</output>
	
 </fieldset>
</form>
EOD;
