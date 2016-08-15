<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/vcart/setup.php');

	$page->showtop();

	echo '<h2>Current surveys</h2>' . LF;
	$sth = $db->prepare(
		"select * "
		.",date_format(datein,'%b %e, %Y') datein_form "
		.",date_format(dateoffline,'%b %e, %Y') dateoffline_form "
		."from cms_minisurvey where isonline='Y' and isactive='Y' order by datein"
	);
	$res = $db->execute($sth);
	while ( $minisurvey = $res->fetchrow(DB_FETCHMODE_OBJECT) ) {
		echo formatSurveyResults($db, $minisurvey);
	}

	echo '<h2>Old surveys</h2>' . LF;
	$sth = $db->prepare("select * from cms_minisurvey where isonline='Y' and isactive='N' order by datein");
	$res = $db->execute($sth);
	while ( $minisurvey = $res->fetchrow(DB_FETCHMODE_OBJECT) ) {
		echo formatSurveyResults($db, $minisurvey);
	}

	$page->showbottom();
	exit;

	function formatSurveyResults($db,$minisurvey) {
		$sth = $db->prepare("select count(1) from cms_minisurveyres where minisurvey=?");
		$res = $db->execute($sth,array($minisurvey->minisurvey));
		$row = $res->fetchrow();
		$answercount = $row[0];

		$o = '<div class="surveyresults">' . LF
			.'<a name="' . $minisurvey->minisurvey . '"></a>' . LF
			.'<table class="table1" width="100%">' . LF
			.'<tr><th colspan="4" class="question">' . $minisurvey->question . '</th></tr>' . LF;

		if ( $minisurvey->dateoffline_form ) {
			$o .= '<tr><td colspan="4" class="dates" style="font-size: 8pt;">Survey ran from  ' . $minisurvey->datein_form . ' through ' . $minisurvey->dateoffline_form . '</td></tr>' . LF;
		}
		else {
			$o .= '<tr><td colspan="4" class="dates">Started ' . $minisurvey->datein_form . '</td></tr>' . LF;
		}
		$o .= '<tr>'
			.'<th>Answer</th>'
			.'<th>Votes</th>'
			.'<th>Percentage</th>'
			.'<th>Graph</th>'
			.'</tr>' . LF;

		$sth_q = $db->prepare(
			"select count(1),  round(( count(1) / ?) * 100) from cms_minisurveyres "
			."where minisurveya = ? group by minisurveya"
		);

		$sth = $db->prepare("select minisurveya, answer from cms_minisurveya where minisurvey = ? order by orderby");
		$res = $db->execute($sth,array($minisurvey->minisurvey));
		while ($minisurveya = $res->fetchrow(DB_FETCHMODE_OBJECT)) {

			$res_q = $db->execute($sth_q,array($answercount,$minisurveya->minisurveya));
			$row_q = $res_q->fetchrow();

			$thisanswercount = $row_q[0] ? $row_q[0] : '0';
			$thisanswerratio = $row_q[1] ? $row_q[1] : '0';
			$ratio = $thisanswerratio / 100.0;
			$totalWidth = 150;
			$width = (int) ( $totalWidth * $ratio );
			$width++;

			$o .= '<tr>'
				.'<td>' . $minisurveya->answer . '</td>'
				.'<td align="right">' . $thisanswercount . '</td>'
				.'<td align="right">' . $thisanswerratio . '%</td>'
				.'<td class="graph"><img src="/minisurvey/dot_900.gif" height="12" width="' . $width . '"></td>'
				.'</tr>' . LF;
		}
		$o .= '</table>' . LF;
		$o .= '</div>' . LF;
		return $o;
	}
?>

