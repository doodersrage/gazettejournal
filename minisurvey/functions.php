<?php
	function getMinisurveyForm($db,$minisurveypk = 0) {
		$minisurvey = null;
		if ( $minisurveypk ) {
			$sth = $db->prepare("select * from cms_minisurvey where minisurvey=?");
			$res = $db->execute($sth,array($minisurveypk));
			$minisurvey = $res->fetchrow(DB_FETCHMODE_OBJECT);
		}
		else {
			$sth = $db->prepare("select * from cms_minisurvey where isactive='Y' and isonline='Y' order by rand() limit 1");
			$res = $db->execute($sth);
			$minisurvey = $res->fetchrow(DB_FETCHMODE_OBJECT);
		}
		if ( ! is_object($minisurvey) ) {
			return '';
		}

		$o = '<div class="minisurveyform">' . LF
			.'<h1>Poll</h1>'
			.'<div style="padding: 5px;">' . LF
			.'<form action="/minisurvey/handler.php" method="post">' . LF
			.hidden('minisurvey',$minisurveypk) . LF
			.'<table>' . LF
			.'<tr><td colspan="2"><b>Q:</b> ' . $minisurvey->question . '</td></tr>' . LF;

		$sth = $db->prepare("select * from cms_minisurveya where minisurvey=? order by orderby");
		$res = $db->execute($sth,array($minisurveypk));
		while ($minisurveya = $res->fetchrow(DB_FETCHMODE_OBJECT)) {
			$o .= '<tr valign="top">'
				.'<td align="right"><input type="radio" name="minisurveya" value="' . $minisurveya->minisurveya . '"></td>'
				.'<td>' . $minisurveya->answer . '</td>'
				.'</tr>' . LF;
		}
		$o .= '<tr><td></td><td><input type="button" value="Vote" onClick="form.submit();"></td></tr>' . LF;
		$o .= '<tr><td></td><td><a href="/minisurvey/">See results</a></td></tr>' . LF;
		$o .= '</table>' . LF
			.'</form>' . LF
			.'</div>' . LF
			.'</div>' . LF;
		return $o;
	}

?>
