<?php
	define('LF',"\n");

	$srcurl = 'http://vcartdemo.vectec.org/cms_admin';

	$unzip = findExe('unzip');
	echo '<h1>Initialize Utility for WMS</h1>' . LF;
	if ( is_dir($_SERVER["DOCUMENT_ROOT"] . '/cms_admin') ) {
		echo '<b>ERROR: /cms_admin/ already exists</b>.  I am stopping now.';
		exit;
	}


	$VERSION_SRC = implode('',file($srcurl . '/VERSION'));
	if ( $_REQUEST['go'] != 'go' || ! $_REQUEST['dbusername'] || ! $_REQUEST['dbpassword'] || ! $_REQUEST['dbhostname'] || ! $_REQUEST['dbdatabase'] ) {
		echo '<p>This utility will deploy the most up to date, bug-free, CMS from VECTEC</p>' . LF;
		echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . LF
			.'<input type="hidden" name="go" value="go">' . LF
			.'<input name="dbusername" value="' . $_REQUEST['dbusername'] . '"> Mysql username<br>' . LF
			.'<input name="dbpassword" value="' . $_REQUEST['dbpassword'] . '"> Mysql password<br>' . LF
			.'<input name="dbdatabase" value="' . $_REQUEST['dbdatabase'] . '"> Mysql database<br>' . LF
			.'<input name="dbhostname" value="' . $_REQUEST['dbhostname'] . '"> Mysql hostname<br>' . LF
			.'<input type="submit" value="Install now">' . LF
			.'</form>' . LF;
		echo '<hr>';
		exit;
	}
	else {
		if ( $_SERVER['SERVER_NAME'] == 'vcartdemo.vectec.org' ) {
			echo '<h3>Error:</h3><p>Will not update the source!</p>'; exit;
		}
		chdir($_SERVER['DOCUMENT_ROOT']);
		echo '<ul>' . LF;
		
		echo '<li>Copying files: ';
		{
			$filetoget = $srcurl . '/vectec_wms.' . trim($VERSION_SRC) . '.zip';
			$rem = fopen($filetoget,'r');
			$tmpfname = tempnam("/tmp", "cmszip");
			$loc = fopen($tmpfname, "w");
			while (!feof($rem)) {
				fwrite($loc, fread($rem, 8192));
			}
			fclose($loc);
			if ( ! is_file($tmpfname) || filesize($tmpfname) < 10 ) {
				echo '<h3>Error: Getting remote WMS Package</h3>';
				exit;
			}
			chdir($_SERVER['DOCUMENT_ROOT']);
			echo '<pre>';
			system("$unzip -o $tmpfname");
			echo '</pre>';
			unlink($tmpfname);
		}
		echo ' Files/dirs updated';

		echo '<li>Setting database login info: ';
		$dbphptemplate = $_SERVER['DOCUMENT_ROOT'] . '/cms_admin/db.php_template';
		$dbphp = $_SERVER['DOCUMENT_ROOT'] . '/cms_admin/db.php';
		$dbinfo = implode("",file($dbphptemplate));
		$dbinfo = str_replace("%USERNAME%",$_REQUEST['dbusername'],$dbinfo);
		$dbinfo = str_replace("%PASSWORD%",$_REQUEST['dbpassword'],$dbinfo);
		$dbinfo = str_replace("%HOSTNAME%",$_REQUEST['dbhostname'],$dbinfo);
		$dbinfo = str_replace("%DATABASE%",$_REQUEST['dbdatabase'],$dbinfo);
		$fh = fopen($dbphp,'w');
		fwrite($fh,$dbinfo);
		fclose($fh);
		# php5: 
		# file_put_contents($dbphp,$dbinfo);
		echo 'ok';
		echo '<li>Now visit <a href="/cms_admin/">Your new CMS</a>' . LF;
		echo '</ul>' . LF;
	}

	function findExe($b) {
		$paths = array(
			'/bin/'
			,'/usr/bin/'
			,'/usr/local/bin/'
			,'/usr/local/sbin/'
			,'/usr/local/mysql/bin/'
			,'/usr/local/mysql4/bin/'
			,'/usr/local/mysql5/bin/'
			,'/sbin/'
		);
		foreach ($paths as $path) {
			if ( is_executable($path . $b) ) {
				return $path . $b;
			}
		}
		echo '<h1>Oops!</h1>';
		die('Cannot find usable "' . $b . '"');
	}
?>

