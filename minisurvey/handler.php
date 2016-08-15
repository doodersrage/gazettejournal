<?php
	require($_SERVER['DOCUMENT_ROOT'] . "/vcart/setup.php");

	$minisurveypk = $_REQUEST['minisurvey'];
	if ( ! $minisurveypk ) {
		$page->showtop();
		echo 'No minisurvey!';
		$page->showbottom();
		exit;
	}
	$sth = $db->prepare("select * from cms_minisurvey where minisurvey=? and isonline='Y'");
	$res = $db->execute($sth,array($minisurveypk));
	$minisurvey = $res->fetchrow(DB_FETCHMODE_OBJECT);
	if ( ! is_object($minisurvey) ) {
		$page->setPageTitle("Mini-Survey Voting | Invalid minisurvey");
		$page->setPage_header("Mini-Survey Voting: Invalid minisurvey");
		$page->showtop();
		echo '<p>Error: You tried to vote on an invalid or inactive mini survey</p>';
		$page->showbottom();
		exit;
	}

	$sth = $db->prepare("select * from cms_minisurveyres where ipaddr=? and minisurvey=?");
	$res = $db->execute($sth,array($_SERVER['REMOTE_ADDR'],$minisurveypk));
	$row = $res->fetchrow();
	if ( $row[0] ) {
		$page->setPageTitle("Survey Voting | One vote only, please");
		$page->showtop();
		echo '<h1>Survey Voting: One vote only, please</h1>' . LF;
		echo '<p>Error: You can only vote once!</p>';
		$page->showbottom();
		exit;
	}
	$sth = $db->prepare("insert into cms_minisurveyres ( ipaddr, minisurvey, minisurveya ) values (?,?,?)");
	$db->execute($sth,array($_SERVER['REMOTE_ADDR'],$minisurveypk,$_REQUEST['minisurveya']));
	$_SESSION['popup'] = 'Your vote has been registered';
	header("Location: /minisurvey/#" . $minisurveypk);
?>
