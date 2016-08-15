#!/usr/bin/perl
############################################
##                                        ##
##          WebAdverts (Display)          ##
##           by Darryl Burgdorf           ##
##       (e-mail burgdorf@awsd.com)       ##
##                                        ##
##             version:  3.10             ##
##        last modified:  02/23/01        ##
##           copyright (c) 2001           ##
##                                        ##
##    latest version is available from    ##
##        http://awsd.com/scripts/        ##
##                                        ##
############################################

# NOTHING BELOW THIS LINE NEEDS TO BE ALTERED!

use Fcntl;
BEGIN { @AnyDBM_File::ISA = qw (DB_File GDBM_File SDBM_File ODBM_File NDBM_File) }
use AnyDBM_File;
umask (0111);
$_ = $adverts_dir; /^(.+)$/; $adverts_dir = $1;

sub ADVsetup {
	$ADVtime = time;
	unless ($ADVUseLocking) {
		&ADVMasterLockOpen;
		if ($ADVlockerror) { return; }
	}
	if ($ADVRandomizeList && ((-M "$adverts_dir/adlist.txt") > .5)) {
		open (ADLIST, "$adverts_dir/adlist.txt");
		$adlist = @adlist = <ADLIST>;
		close (ADLIST);
		srand();
		&ADVlistreorder;
		&ADVLockOpen (ADLIST, "adlist.txt");
		unless ($ADVlockerror) {
			seek(ADLIST, 0, 0);
			foreach $ad (@newadlist) {
				print ADLIST "$ad";
			}
			truncate (ADLIST, tell(ADLIST));
			&ADVLockClose (ADLIST, "adlist.txt");
		}
		opendir (FILES,"$adverts_dir");
		@files = readdir(FILES);
		closedir (FILES);
		foreach $file (@files) {
			if (($file =~ /^dupcli/) || ($file =~ /^dupvie/)) {
				unlink "$adverts_dir/$file";
			}
		}
	}	
	if ($ADVLogIP) {
		if ($ADVResolveIPs) {
			if (($ENV{'REMOTE_ADDR'} =~ /\d+\.\d+\.\d+\.\d+/)
			  && (!($ENV{'REMOTE_HOST'})
			  || ($ENV{'REMOTE_HOST'} =~ /\d+\.\d+\.\d+\.\d+/))) {
				@domainbytes = split(/\./,$ENV{'REMOTE_ADDR'});
				$packaddr = pack("C4",@domainbytes);
				$resolvedip = (gethostbyaddr($packaddr, 2))[0];
				unless ($resolvedip =~
				  /^[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})$/) {
					$resolvedip = "";
				}
				if ($resolvedip) {
					$ENV{'REMOTE_HOST'} = $resolvedip;
				}
			}
		}
		unless ($ENV{'REMOTE_HOST'}) {
			$ENV{'REMOTE_HOST'} = $ENV{'REMOTE_ADDR'};
		}
	}
	$DoNotLog = 0;
	if ($IgnoredIPs) {
		@ignoredips = split(/ /,$IgnoredIPs);
		foreach $ignoredip (@ignoredips) {
			if (($ENV{'REMOTE_HOST'} =~ /$ignoredip/i)
			  || ($ENV{'REMOTE_ADDR'} =~ /$ignoredip/i)) {
				$DoNotLog = 1;
			}
		}
	}
	$harvester_list = 'bullseye|cherrypicker|crescent|emailcollector|emailsiphon|emailwolf|extractor|microsoft url|mozilla/3.mozilla/2.01|newt|nicerspro|webbandit';
	$download_list = 'da \d|dnload|download|fetch|flashget|ftp|getright|gozilla|jetcar|leach|leech';
	$linkchecker_list = 'analyze|check|link|netmechanic|netmind|powermarks|redalert|tooter|validat|verif|walk';
	$offline_list = 'avantgo|batch|copier|httrack|msiecrawler|msproxy|netattache|netscape-proxy|offline|teleport|webcapture|webzip';
	$spider_list = 'aport|archive|ask jeeves|behold|borg|bot|catch|crawl|digger|elitesys|enfish|esense|euroseek|ferret|grab|griffon|gulliver|harvest|hubat|hunt|infoseek|java|leia|mantraagent|mapper|mata hari|mercator|netants|perl|quest|reader|reaper|roamer|rover|scooter|search|slurp|snatch|spider|spinne|spyder|sweep|t-h-u-n-d-e-r-s-t-o-n-e|ultraseek|url|utopy|webcollage|webster pro|webwhacker|wget|whatuseek';
	if (($ENV{'HTTP_USER_AGENT'} =~ m#$harvester_list#oi)
	  || ($ENV{'HTTP_USER_AGENT'} =~ m#$download_list#oi)
	  || ($ENV{'HTTP_USER_AGENT'} =~ m#$linkchecker_list#oi)
	  || ($ENV{'HTTP_USER_AGENT'} =~ m#$offline_list#oi)
	  || ($ENV{'HTTP_USER_AGENT'} =~ m#$spider_list#oi)) {
		unless (($ENV{'HTTP_USER_AGENT'} =~ m#robotics#oi) || ($ENV{'HTTP_USER_AGENT'} =~ m#hotjava#oi)) {
			$DoNotLog = 1;
		}
	}
	$AdvertChosen = 0;
	($ADVmin,$ADVhour,$ADVmday,$ADVmon,$ADVyear) =
	  (localtime($ADVtime+($HourOffset*3600)))[1,2,3,4,5];
	if ($ADVmin < 10) { $ADVmin = "0$ADVmin"; }
	if ($ADVhour < 10) { $ADVhour = "0$ADVhour"; }
	if ($ADVmday < 10) { $ADVmday = "0$ADVmday"; }
	$ADVmon++;
	if ($ADVmon < 10) { $ADVmon = "0$ADVmon"; }
	$ADVyear = $ADVyear+1900;
	$TrimmedIP = $ENV{'REMOTE_ADDR'};
	$TrimmedIP =~ s/(\d*\.\d*\.\d*)\.\d*/$1/;
	$ADVshown = 0;
	$ADVWrapCounter = 0;
	if ($ADVQuery =~ /(.*)&url=(.*)/i) {
		$ADVQuery = $1;
		$rawmodedest = $2;
	}
	if ($ADVQuery =~ /page=([^\s&;\?]*)/i) {
		$displaypage = $1;
		$NonSSI = 1;
	}
	if ($ADVQuery =~ /zone=([^\s&;\?]*)/i) {
		$advertzone = $1;
		if ($advertzone =~ /\+/) {
			@advertzones = split(/\+/,$advertzone);
			$advertzonecount = @advertzones;
			srand();
			$advertzone = @advertzones[int(rand($advertzonecount))];
		}
	}
	unless ($advertzone) { $advertzone = "unzoned"; }
	if ($ENV{'HTTP_REFERER'} =~ /page=[^\s&;\?]*/i) {
		$ENV{'HTTP_REFERER'} = "dummy";
	}
	unless (defined $ENV{'HTTP_REFERER'}) {
		$ENV{'HTTP_REFERER'} = "dummy";
	}
	$ENV{'HTTP_REFERER'} =~ s/([^\?]*)\?.*/$1/;
	$ADVCookieName = "$ENV{'HTTP_REFERER'} $displaypage $advertzone";
	$ADVCookieName2 = "dummy $displaypage $advertzone";
	%Cookie_Encode_Chars = (
	  '\%','%25','\+','%2B','\;','%3B','\,','%2C',
	  '\=','%3D','\&','%26','\:\:','%3A%3A','\s','+'
	  );
	%Cookie_Decode_Chars = (
	  '\+',' ','\%3A\%3A','::','\%26','&','\%3D','=',
	  '\%2C',',','\%3B',';','\%2B','+','\%25','%'
	  );
	if ($ADVQuery =~ /setdest=([^\s&;\?]*)/i) {
		$SetDest = $1;
		$SetDest =~ s/[^\w\.\-\']//g;
		$SetDest =~ tr/A-Z/a-z/;
		unless ($SetDest =~ /^[a-z,0-9]/) { $SetDest = "a".$SetDest; }
		$SetDest = substr($SetDest,0,25);
	}
	if ($ADVQuery =~ /member=([^\s&;\?]*)/i) {
		$ADVID = $1;
		$ADVID =~ s/[^\w\.\-\']//g;
		$ADVID =~ tr/A-Z/a-z/;
		unless ($ADVID =~ /^[a-z,0-9]/) { $ADVID = "a".$ADVID; }
		$ADVID = substr($ADVID,0,25);
		$newsubdir = substr($ADVID,0,1);
		$newsubdir .= "/$ADVID";
	}
	if ($ADVQuery =~ /banner=([^\s&;\?]*)/i) {
		$displayad = $1;
		$displayad =~ s/[^\w\.\-\']//g;
		$displayad =~ tr/A-Z/a-z/;
		unless ($displayad =~ /^[a-z,0-9]/) { $displayad = "a".$displayad; }
		$displayad = substr($displayad,0,25);
		&gotoad;
		unless ($ADVUseLocking) { &ADVMasterLockClose; }
		return;
	}
	unless ($NonSSI || $ADVNoPrint) {
		if ($ENV{'PERLXS'} eq "PerlIS") { print "HTTP/1.0 200 OK\n"; }
		if ($ADVQuery =~ /jscript/) {
			print "Cache-Control: no-cache\n";
			print "Pragma: no-cache\n";
			print "Content-type: application/x-javascript\n\n";
		}
		else {
			print "Content-type: text/html\n\n";
		}
	}
	if ($RequireMember) {
		if ($NonSSI || ($ADVQuery =~ /iframe/) || ($ADVQuery =~ /jscript/)) {
			unless (($ADVID) && (-e "$adverts_dir/$newsubdir/$ADVID.txt")) {
				unless ($ADVUseLocking) { &ADVMasterLockClose; }
				return;
			}
		}
	}
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) {
		&ADVLockClose (DBMLIST, "dbmlist.txt");
		unless ($ADVUseLocking) { &ADVMasterLockClose; }
		return;
	}
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) {
			unless ($ADVUseLocking) { &ADVMasterLockClose; }
			return;
		}
	}
	($ADVcountlines[0],$ADVcountlines[1],$ADVcountlines[2]) =
	  split(/\n/,$DBMList{'adcount.txt'});
	if (($ADVtime - $ADVcountlines[2]) > 250000000) {
		$ADVcountlines[0] = 1;
		$ADVcountlines[1] = "0";
		$ADVcountlines[2] = time;
	}
	open (ADVLIST, "<$adverts_dir/adlist.txt");
	while (defined($ADVlist = <ADVLIST>)) {
		push (@ADVcountlines,$ADVlist);
	}
	close (ADVLIST);
	chomp (@ADVcountlines);
	$ADVcount = $ADVcountlines[0];
	@ADVcount = split(/\|/,$ADVcount);
	foreach $ADVcount (@ADVcount) {
		($ADVone,$ADVtwo) = split(/=/,$ADVcount);
		unless ($ADVtwo) { $ADVtwo = "unzoned"; }
		$zonecount{$ADVtwo} = $ADVone;
	}
	unless ($zonecount{$advertzone}) { $zonecount{$advertzone} = 1; }
	$ADVexposures = $ADVcountlines[1];
	($ADVexposures,@ADVcycles) = split(/\|/, $ADVexposures);
	foreach $ADVcycles (@ADVcycles) {
		($ADVone,$ADVtwo) = split(/=/,$ADVcycles);
		unless ($ADVtwo) { $ADVtwo = "unzoned"; }
		$cyclecount{$ADVtwo} = $ADVone;
	}
	unless ($cyclecount{$advertzone}) { $cyclecount{$advertzone} = 1; }
	if ($advertzone =~ /showall-(.*)/i) {
		$advertzone = $1; $ShowAllShown = 0; &ADVshowall;
	}
	elsif ($advertzone =~ /showall/i) {
		$ShowAllShown = 0; &ADVshowall;
	}
	else { &ADVdisplayad; }
	if (($AdvertChosen < 1) && $DefaultBanner) {
		$ShowDefaultBanner = 1;
		&ADVdisplayad;
	}
	$ADVcountlines[0] = "";
	foreach $key (keys %zonecount) {
		$ADVcountlines[0] =
		  $ADVcountlines[0]."$zonecount{$key}=$key|";
	}
	$ADVcountlines[1] = "$ADVexposures|";
	foreach $key (keys %cyclecount) {
		$ADVcountlines[1] =
		  $ADVcountlines[1]."$cyclecount{$key}=$key|";
	}
	$DBMList{'adcount.txt'} = "$ADVcountlines[0]\n$ADVcountlines[1]\n$ADVcountlines[2]";
	&ADVDBMClose;
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	unless ($ADVUseLocking) { &ADVMasterLockClose; }
}

sub ADVlistreorder {
	$randlocation = int(rand($adlist));
	if ($adlist[$randlocation] eq "-") { &ADVlistreorder; return; }
	push (@newadlist,$adlist[$randlocation]);
	$adlist[$randlocation] = "-";
	$adcounter ++;
	if ($adcounter < $adlist) { &ADVlistreorder; }
}

sub ADVdisplayad {
	if ($ShowDefaultBanner) {
		$ADVdisplayad = $DefaultBanner;
	}
	elsif ($SetDest) {
		$ADVdisplayad = $SetDest;
	}
	else {
		$ADVWrapCounter++;
		if ($ADVWrapCounter > @ADVcountlines-3) {
			return;
		}
		$ADVdisplayad = $ADVcountlines[$zonecount{$advertzone}+2];
		$ADVcycles = $cyclecount{$advertzone};
		$zonecount{$advertzone}++;
		if ($zonecount{$advertzone} > @ADVcountlines-3) {
			$zonecount{$advertzone} = 1;
			$cyclecount{$advertzone}++;
		}
		if ($ADVID eq $ADVdisplayad) {
			&ADVdisplayad;
			return;
		}
	}
	($ADVmax,$ADVshown,$ADVvisits,$ADVimage,$ADVstart,$ADVweight,
	  $ADVzone,$ADVraw,$ADVratio,$ADVclicksfrom) = split(/\t/,$DBMList{$ADVdisplayad});
	($ADVmax,$ADVmaxtype) = split(/\|/, $ADVmax);
	unless ($ADVmaxtype) { $ADVmaxtype = "E"; }
	($ADVdisplayratio,$ADVdisplaycount) = split(/\|/, $ADVratio);
	($ADVclicksfrom,$ADVclicksratio) = split(/\|/, $ADVclicksfrom);
	if ($ADVmaxtype eq "N") { $ADVmax = 0; }
	$ADVrealmax = $ADVmax;
	if (($ADVmaxtype eq "N") && ($ADVdisplayratio < 1) && ($ADVclicksratio < 1)) {
		$ADVrealmax = $ADVshown+1;
	}
	elsif (($ADVmaxtype eq "E") || ($ADVmaxtype eq "N")) {
		if ($ADVdisplayratio > 0) {
			$ADVrealmax += int($ADVdisplaycount/$ADVdisplayratio);
		}
		if ($ADVclicksratio > 0) {
			$ADVrealmax += ($ADVclicksfrom*$ADVclicksratio);
		}
	}
	unless ($ShowDefaultBanner || $SetDest) {
		if ((($advertzone ne "unzoned") && ($advertzone ne "ShowAll")
		  && (length($ADVzone) > 2) && ($ADVzone !~ /\s$advertzone\s/))
		  || !($ADVimage || $ADVraw)
		  || (($ADVraw eq "J") && !($ADVimage) 
		  && $JSConflict && ($ADVQuery =~ /jscript/))
		  || ($ADVweight < 1)
		  || (((($ADVmaxtype eq "E") || ($ADVmaxtype eq "N"))
		  && ($ADVrealmax <= $ADVshown))
		  && ((($ADVdisplayratio < 1) && ($ADVclicksratio < 1))
		  || ($advertzone ne "ShowAll")))
		  || (($ADVmaxtype eq "C") && ($ADVrealmax <= $ADVvisits))
		  || (($ADVmaxtype eq "D") && ($ADVrealmax <= $ADVtime))
		  || ($ADVstart > $ADVtime)
		  || ((($ADVcycles/$ADVweight) != int($ADVcycles/$ADVweight))
		  && ($advertzone ne "ShowAll"))
		  || (!($ADVimage) && $NonSSI)) {
			&ADVdisplayad;
			return;
		}
	}
	if ($SetDest) {
		if (!($ADVimage || $ADVraw)
		  || (($ADVraw eq "J") && !($ADVimage) 
		  && $JSConflict && ($ADVQuery =~ /jscript/))
		  || ($ADVweight < 1)
		  || (((($ADVmaxtype eq "E") || ($ADVmaxtype eq "N"))
		  && ($ADVrealmax <= $ADVshown))
		  && ((($ADVdisplayratio < 1) && ($ADVclicksratio < 1))
		  || ($advertzone ne "ShowAll")))
		  || (($ADVmaxtype eq "C") && ($ADVrealmax <= $ADVvisits))
		  || (($ADVmaxtype eq "D") && ($ADVrealmax <= $ADVtime))
		  || ($ADVstart > $ADVtime)) {
			$SetDest = "";
			&ADVdisplayad;
			return;
		}
	}
	$subdir = substr($ADVdisplayad,0,1);
	$subdir .= "/$ADVdisplayad";
	&ADVLockOpen (ADVDISPLAY, "$subdir/$ADVdisplayad.txt");
	if ($ADVlockerror) {
		&ADVLockClose (ADVDISPLAY, "$subdir/$ADVdisplayad.txt");
		$SetDest = "";
		unless ($ShowDefaultBanner) {
			&ADVdisplayad;
		}
		return;
	}
	@ADVdisplaylines = <ADVDISPLAY>;
	chomp (@ADVdisplaylines);
	($ADVmax,$ADVshown,$ADVvisits,
	  $ADVurl,$ADVimage,$ADVheight,$ADVwidth,
	  $ADValt,$ADVnada1,$ADVtext,$ADVstart,
	  $ADVweight,$ADVzone,$ADVborder,$ADVtarget,
	  $ADVraw,$ADVratio,$ADVnada2,$ADVnada3,
	  $ADVdisplayzone,$ADVclicksfrom) = @ADVdisplaylines;
	($ADVmax,$ADVmaxtype) = split(/\|/, $ADVmax);
	unless ($ADVmaxtype) { $ADVmaxtype = "E"; }
	($ADVdisplayratio,$ADVdisplaycount) = split(/\|/, $ADVratio);
	($ADVclicksfrom,$ADVclicksratio) = split(/\|/, $ADVclicksfrom);
	if ($ADVmaxtype eq "N") { $ADVmax = 0; }
	$ADVrealmax = $ADVmax;
	if (($ADVmaxtype eq "N") && ($ADVdisplayratio < 1) && ($ADVclicksratio < 1)) {
		$ADVrealmax = $ADVshown+1;
	}
	elsif (($ADVmaxtype eq "E") || ($ADVmaxtype eq "N")) {
		if ($ADVdisplayratio > 0) {
			$ADVrealmax += int($ADVdisplaycount/$ADVdisplayratio);
		}
		if ($ADVclicksratio > 0) {
			$ADVrealmax += ($ADVclicksfrom*$ADVclicksratio);
		}
	}
	($ADVtext,$ADVtexttype) = split(/\|/, $ADVtext);
	unless ($ADVtexttype) { $ADVtexttype = "B"; }
	if ($DupViewTime && !($DoNotLog)) {
		$DupView = "$TrimmedIP $ADVdisplayad";
		&ADVLockOpen (DUPVIEWS, "dupviews.txt");
		unless ($ADVlockerror) {
			if ($DBMType==1) {
				tie (%DupViews,'AnyDBM_File',"$adverts_dir/dupviews",O_RDWR|O_CREAT,0666,$DB_HASH)
				  || &ADVDupError;
			}
			elsif ($DBMType==2) {
				dbmopen(%DupViews,"$adverts_dir/dupviews",0666)
				  || &ADVDupError;
			}
			else {
				tie (%DupViews,'AnyDBM_File',"$adverts_dir/dupviews",O_RDWR|O_CREAT,0666)
				  || &ADVDupError;
			}
			unless ($ADVduperror) {
				if (($ADVtime-$DupViews{$DupView}) > ($DupViewTime * 60)) {
					$DupViews{$DupView} = $ADVtime;
				}
				else { $DoNotLog = 1; }
				if ($DBMType==2) { dbmclose (%DupViews); }
				else { untie %DupViews; }
			}
		}
		&ADVLockClose (DUPVIEWS, "dupviews.txt");
	}
	if ($ClickViewTime && !($DoNotLog)) {
		$ClickView = "$TrimmedIP $ADVdisplayad";
		&ADVLockOpen (CLICKVIEWS, "dupclicks.txt");
		unless ($ADVlockerror) {
			if ($DBMType==1) {
				tie (%ClickViews,'AnyDBM_File',"$adverts_dir/dupclicks",O_RDWR|O_CREAT,0666,$DB_HASH)
				  || &ADVDupError;
			}
			elsif ($DBMType==2) {
				dbmopen(%ClickViews,"$adverts_dir/dupclicks",0666)
				  || &ADVDupError;
			}
			else {
				tie (%ClickViews,'AnyDBM_File',"$adverts_dir/dupclicks",O_RDWR|O_CREAT,0666)
				  || &ADVDupError;
			}
			unless ($ADVduperror) {
				unless (($ADVtime-$ClickViews{$ClickView}) > ($ClickViewTime * 60)) {
					$DoNotLog = 1;
				}
				if ($DBMType==2) { dbmclose (%ClickViews); }
				else { untie %ClickViews; }
			}
		}
		&ADVLockClose (CLICKVIEWS, "dupclicks.txt");
	}
	unless ($DoNotLog) {
		if ($ADVLogIP) {
			&ADVLockOpen (IPLOG,"$subdir/$ADVdisplayad.$ADVmon$ADVmday.log","a");
			unless ($ADVlockerror) {
				print IPLOG "$ADVhour:$ADVmin E $ENV{'REMOTE_HOST'}\n";
			}
			&ADVLockClose (IPLOG,"$subdir/$ADVdisplayad.$ADVmon$ADVmday.log");
		}
		$ADVexposures++;
	}
	@ADVimage = split(/\|/,$ADVimage);
	$imagecount = @ADVimage;
	srand();
	$ADVdisplayimage = @ADVimage[int(rand($imagecount))];
	$ADVdisplayimage =~ s/<RAND>/$ADVtime/g;
	if ($NonSSI) {
		if ($ENV{'PERLXS'} eq "PerlIS") { print "HTTP/1.0 301 Found\n"; }
		else { print "Status: 301 Found\n"; }
		if ($ADVUseCookies) {
			$ADVCookieValue = "$ADVdisplayad $ADVtime";
			foreach $char ('\%','\+','\;','\,','\=','\&','\:\:','\s') {
				$ADVCookieName =~ s/$char/$Cookie_Encode_Chars{$char}/g;
				$ADVCookieValue =~ s/$char/$Cookie_Encode_Chars{$char}/g;
			}
			print "Set-Cookie: $ADVCookieName=$ADVCookieValue\n";
		}
		else {
			&ADVLockOpen (NONSSILOG,"nonssi.log","a");
			unless ($ADVlockerror) {
				print NONSSILOG "$ADVtime $TrimmedIP ";
				print NONSSILOG "$ADVCookieName | $ADVdisplayad\n";
			}
			&ADVLockClose (NONSSILOG,"nonssi.log");
		}
		print "Cache-Control: no-cache\n";
		print "Pragma: no-cache\n";
		print "Expires: Thu, 31 Dec 1998 11:59:59 GMT\n";
		print "Location: $ADVdisplayimage";
		if ($GraphicTimestamp) { print "?$ADVtime"; }
		print "\n\n";
	}
	elsif ($ADVraw
	  && !(($ADVraw =~ /<SCRIPT/) && $JSConflict && ($ADVQuery =~ /jscript/))) {
		$ADVrealraw = $ADVraw;
		$ADVrealraw =~ s/<NLB>/\n/g;
		$ADVrealraw =~ s/<RAND>/$ADVtime/g;
		$raw_display_cgi = $display_cgi."?";
		if ($ADVID) { $raw_display_cgi .= "member=$ADVID;"; }
		unless ($advertzone eq "unzoned") { $raw_display_cgi .= "zone=$advertzone;"; }
		if ($SetDest) { $raw_display_cgi .= "setdest=$SetDest;"; }
		$raw_display_cgi .= "banner=$ADVdisplayad&url=";
		$ADVrealraw =~ s/<URL>/$raw_display_cgi/g;
		if ($ADVQuery =~ /jscript/) {
			$ADVrealraw = &JSoutput($ADVrealraw);
		}
		if ($ADVQuery =~ /iframe/) {
			print "<HTML>\n";
			if ($IFRAMErefreshrate > 0) {
				$RefreshQuery = $ADVQuery;
				$RefreshQuery =~ s/;/&/g;
				print "<HEAD><META HTTP-EQUIV=\"REFRESH\" CONTENT=\"$IFRAMErefreshrate; url=$display_cgi?$RefreshQuery\"></HEAD>\n";
			}
			print "<BODY $IFRAMEbodyspec>\n";
		}
		print "$ADVrealraw";
		if ($ADVQuery =~ /iframe/) {
			print "\n</BODY></HTML>\n";
		}
	}
	else {
		unless ($ADVborder) { $ADVborder="0"; }
		if ($ADVurl) {
			$DestTag = "<A HREF=\"$display_cgi?banner=$ADVdisplayad;";
			$DestTag .= "time=$ADVtime";
			if ($ADVID) { $DestTag .= ";member=$ADVID"; }
			unless ($advertzone eq "unzoned") { $DestTag .= ";zone=$advertzone"; }
			if ($SetDest) { $DestTag .= ";setdest=$SetDest"; }
			$DestTag .= "\"";
			if ($ADVtarget eq "_top") { $DestTag .= " TARGET=\"$ADVtarget\""; }
			elsif ($ADVtarget) { $DestTag .= " $ADVtarget"; }
			$DestTag .= ">";
		}
		$JSad = "";
		if ($ADVtext && ($ADVtexttype eq "T")
		  && ($advertzone ne "ShowAll")) {
			$JSad .= "<SMALL>";
			if ($ADVurl) { $JSad .= "$DestTag"; }
			$JSad .= "$ADVtext";
			if ($ADVurl) { $JSad .= "</A>"; }
			$JSad .= "</SMALL><BR>";
		}
		if ($ADVurl) { $JSad .= "$DestTag"; }
		$JSad .= "<IMG SRC=\"$ADVdisplayimage";
		if ($GraphicTimestamp) { $JSad .= "?$ADVtime"; }
		$JSad .= "\"";
		if ($ADVheight && $ADVwidth) {
			$JSad .= " HEIGHT=$ADVheight WIDTH=$ADVwidth";
		}
		$JSad .= " ALT=\"$ADValt\"";
		$JSad .= " BORDER=$ADVborder>";
		if ($ADVurl) { $JSad .= "</A>"; }
		if ($ADVtext && ($ADVtexttype eq "B")
		  && ($advertzone ne "ShowAll")) {
			$JSad .= "<BR><SMALL>";
			if ($ADVurl) { $JSad .= "$DestTag"; }
			$JSad .= "$ADVtext";
			if ($ADVurl) { $JSad .= "</A>"; }
			$JSad .= "</SMALL>";
		}
		if ($ADVQuery =~ /jscript/) {
			$JSad = &JSoutput($JSad);
		}
		if ($ADVQuery =~ /iframe/) {
			print "<HTML>\n";
			if ($IFRAMErefreshrate > 0) {
				$RefreshQuery = $ADVQuery;
				$RefreshQuery =~ s/;/&/g;
				print "<HEAD><META HTTP-EQUIV=\"REFRESH\" CONTENT=\"$IFRAMErefreshrate; url=$display_cgi?$RefreshQuery\"></HEAD>\n";
			}
			print "<BODY $IFRAMEbodyspec>\n";
		}
		print "$JSad";
		if ($ADVQuery =~ /iframe/) {
			print "\n</BODY></HTML>\n";
		}
	}
	$ShowAllShown = 0;
	unless ($DoNotLog) { $ADVshown += 1; }
	if ($ADVmax || $ADVratio) {
		seek(ADVDISPLAY, 0, 0);
		print ADVDISPLAY "$ADVmax|$ADVmaxtype\n";
		print ADVDISPLAY "$ADVshown\n";
		print ADVDISPLAY "$ADVvisits\n";
		print ADVDISPLAY "$ADVurl\n";
		print ADVDISPLAY "$ADVimage\n";
		print ADVDISPLAY "$ADVheight\n";
		print ADVDISPLAY "$ADVwidth\n";
		print ADVDISPLAY "$ADValt\n";
		print ADVDISPLAY "$ADVnada1\n";
		print ADVDISPLAY "$ADVtext|$ADVtexttype\n";
		print ADVDISPLAY "$ADVstart\n";
		print ADVDISPLAY "$ADVweight\n";
		print ADVDISPLAY "$ADVzone\n";
		print ADVDISPLAY "$ADVborder\n";
		print ADVDISPLAY "$ADVtarget\n";
		print ADVDISPLAY "$ADVraw\n";
		print ADVDISPLAY "$ADVratio\n";
		print ADVDISPLAY "$ADVnada2\n";
		print ADVDISPLAY "$ADVnada3\n";
		print ADVDISPLAY "$ADVdisplayzone\n";
		print ADVDISPLAY "$ADVclicksfrom|$ADVclicksratio\n";
		if ($LogByZone) {
			$ADVlogzonecatch = 0;
			$ADVlogzone = "$advertzone $ADVID";
			unless ($ADVID) { $ADVlogzone .= "none"; }
			$ADVlogzone .= " E";
			if (@ADVdisplaylines > 21) {
				foreach $key (21..(@ADVdisplaylines-1)) {
					if (!($DoNotLog)
					  && ($ADVdisplaylines[$key] =~ /^$ADVlogzone (\d*)/)) {
						$count = $1;
						$count++;
						print ADVDISPLAY "$ADVlogzone $count\n";
						$ADVlogzonecatch = 1;
					}
					else {
						print ADVDISPLAY "$ADVdisplaylines[$key]\n";
					}
				}
			}
			unless ($ADVlogzonecatch || $DoNotLog) {
				print ADVDISPLAY "$ADVlogzone 1\n";
			}
		}
		truncate (ADVDISPLAY, tell(ADVDISPLAY));
		if ($ADVimage) { $ADVimage = "X"; }
		if ($ADVraw =~ /<SCRIPT/) { $ADVraw = "J"; }
		elsif ($ADVraw) { $ADVraw = "X"; }
		$DBMList{$ADVdisplayad} = "$ADVmax|$ADVmaxtype\t$ADVshown\t$ADVvisits\t$ADVimage\t$ADVstart\t$ADVweight\t";
		$DBMList{$ADVdisplayad} .= "$ADVzone\t$ADVraw\t$ADVratio\t$ADVclicksfrom|$ADVclicksratio";
	}
	&ADVLockClose (ADVDISPLAY, "$subdir/$ADVdisplayad.txt");
	$AdvertChosen++;
	unless ($DoNotLog) {
		if (($ADVID) && (-e "$adverts_dir/$newsubdir/$ADVID.txt")) {
			&ADVLockOpen (ADVDISPLAY, "$newsubdir/$ADVID.txt");
			unless ($ADVlockerror) {
				@ADVdisplaylines = <ADVDISPLAY>;
				chomp ($ADVdisplaylines[16]);
				($ADVratio,$ADVdisplaycount) = split(/\|/, $ADVdisplaylines[16]);
				$ADVdisplaycount++;
				unless (@ADVdisplaylines < 1) {
					seek(ADVDISPLAY, 0, 0);
					foreach $key (0..15) {
						print ADVDISPLAY "$ADVdisplaylines[$key]";
					}
					$newratio = "$ADVratio|$ADVdisplaycount";
					print ADVDISPLAY "$newratio\n";
					foreach $key (17..(@ADVdisplaylines-1)) {
						print ADVDISPLAY "$ADVdisplaylines[$key]";
					}
					truncate (ADVDISPLAY, tell(ADVDISPLAY));
					($ADVmax,$ADVshown,$ADVvisits,$ADVimage,$ADVstart,$ADVweight,
					  $ADVzone,$ADVraw,$ADVratio,$ADVclicksfrom) = split(/\t/,$DBMList{$ADVID});
					$DBMList{$ADVID} = "$ADVmax\t$ADVshown\t$ADVvisits\t$ADVimage\t$ADVstart\t$ADVweight\t";
					$DBMList{$ADVID} .= "$ADVzone\t$ADVraw\t$newratio\t$ADVclicksfrom";
				}
			}
			&ADVLockClose (ADVDISPLAY, "$newsubdir/$ADVID.txt");
			$ADVacc = 0;
			&ADVLockOpen (DAILYLOG,"$newsubdir/$ADVID.log");
			unless ($ADVlockerror) {
				$ADVaccess = "$ADVmday $ADVmon $ADVyear S";
				$ADVlocation = tell DAILYLOG;
				while (defined($ADVline = <DAILYLOG>)) {
					chomp ($ADVline);
					if (($ADVacc,$ADVlogstring) = ($ADVline =~
					  /^(\d\d\d\d\d\d\d\d\d\d) (\d\d \d\d \d\d\d\d S)$/)) {
						if ($ADVaccess eq $ADVlogstring) {
							last;
						}
					}
					last if ($ADVaccess eq $ADVlogstring);
					$ADVlocation = tell DAILYLOG;
					$ADVacc = 0;
				}
				$ADVacc++;
				seek (DAILYLOG, $ADVlocation, 0);
				$ADVlongacc = sprintf("%010.10d",$ADVacc);
				print DAILYLOG "$ADVlongacc $ADVaccess\n";
			}
			&ADVLockClose (DAILYLOG, "$newsubdir/$ADVID.log");
		}
		$ADVacc = 0;
		&ADVLockOpen (DAILYLOG,"$subdir/$ADVdisplayad.log");
		unless ($ADVlockerror) {
			$ADVaccess = "$ADVmday $ADVmon $ADVyear E";
			$ADVlocation = tell DAILYLOG;
			while (defined($ADVline = <DAILYLOG>)) {
				chomp ($ADVline);
				if (($ADVacc,$ADVlogstring) = ($ADVline =~
				  /^(\d\d\d\d\d\d\d\d\d\d) (\d\d \d\d \d\d\d\d E)$/)) {
					if ($ADVaccess eq $ADVlogstring) {
						last;
					}
				}
				last if ($ADVaccess eq $ADVlogstring);
				$ADVlocation = tell DAILYLOG;
				$ADVacc = 0;
			}
			$ADVacc++;
			seek (DAILYLOG, $ADVlocation, 0);
			$ADVlongacc = sprintf("%010.10d",$ADVacc);
			print DAILYLOG "$ADVlongacc $ADVaccess\n";
		}
		&ADVLockClose (DAILYLOG, "$subdir/$ADVdisplayad.log");
	}
}

sub JSoutput {
	$_[0] =~ s/\"/\\"/g;
	$_[0] =~ s/\r//g;
	$_[0] =~ s/\n/\")\;\ndocument.write(\" /g;
	$_[0] = "document.write(\" $_[0] \");document.close();";
	return $_[0];
}

sub ADVshowall {
	$ADVshown = 0;
	unless ($ShowAllShown) {
		$ShowAllShown = 1;
		print "\n<P>";
	}
	&ADVdisplayad;
	unless ((@ADVcountlines < 4)
	  || ($ADVWrapCounter > @ADVcountlines-3)) {
		&ADVshowall
	}
}

sub gotoad {
	$timestamp = 0;
	if ($NonSSI) {
		$displayad = "";
		if ($ENV{'HTTP_COOKIE'}) {
			foreach (split(/; /,$ENV{'HTTP_COOKIE'})) {
				($ADVcookie,$ADVvalue) = split(/=/);
				foreach $char ('\+','\%3A\%3A','\%26','\%3D','\%2C','\%3B','\%2B','\%25') {
					$ADVcookie =~ s/$char/$Cookie_Decode_Chars{$char}/g;
					$ADVvalue =~ s/$char/$Cookie_Decode_Chars{$char}/g;
				}
				if (($ADVCookieName eq $ADVcookie)
				  || ($ADVCookieName2 eq $ADVcookie)) {
					($displayad,$timestamp) = split(/ /,$ADVvalue);
				}
			}
		}
		unless ($displayad) {
			$logcheck = "$TrimmedIP $ADVCookieName";
			$logcheck2 = "$TrimmedIP $ADVCookieName2";
			&ADVLockOpen (NONSSILOG,"nonssi.log");
			if ($ADVlockerror) {
				if ($SetDest) {
					$displayad = $SetDest;
				}
				else {
					&ADVLockClose (NONSSILOG,"nonssi.log");
					&BadRef;
					return;
				}
			}
			else {
				undef (@nonssi);
				while (defined($nonssiline = <NONSSILOG>)) {
					chomp ($nonssiline);
					$timestamp = int($nonssiline);
					unless (($ADVtime-$timestamp) > 3600) {
						push (@nonssi,$nonssiline);
					}
					if (($nonssiline =~ /^\d* $logcheck \| (.*)$/)
					  || ($nonssiline =~ /^\d* $logcheck2 \| (.*)$/)) {
						$displayad = $1;
					}
				}
				seek(NONSSILOG, 0, 0);
				foreach $nonssiline (@nonssi) {
					print NONSSILOG "$nonssiline\n";
				}
				truncate (NONSSILOG, tell(NONSSILOG));
				&ADVLockClose (NONSSILOG, "nonssi.log");
			}
		}
		unless ($displayad) {
			if ($SetDest) {
				$displayad = $SetDest;
			}
			else {
				&BadRef;
				return;
			}
		}
	}
	elsif ($ADVQuery =~ /time=([^\s&;\?]*)/i) { $timestamp = $1; }
	$subdir = substr($displayad,0,1);
	$subdir .= "/$displayad";
	unless (-e "$adverts_dir/$subdir/$displayad.txt") {
		&BadRef;
		return;
	}
	if ((-M "$adverts_dir/$subdir/$displayad.log") > .5) {
		$DoNotLog = 1;
	}
	if (($DupClickTime || $ClickViewTime) && !($DoNotLog)) {
		$DupClick = "$TrimmedIP $displayad";
		&ADVLockOpen (DUPCLICKS, "dupclicks.txt");
		unless ($ADVlockerror) {
			if ($DBMType==1) {
				tie (%DupClicks,'AnyDBM_File',"$adverts_dir/dupclicks",O_RDWR|O_CREAT,0666,$DB_HASH)
				  || &ADVDupError;
			}
			elsif ($DBMType==2) {
				dbmopen(%DupClicks,"$adverts_dir/dupclicks",0666)
				  || &ADVDupError;
			}
			else {
				tie (%DupClicks,'AnyDBM_File',"$adverts_dir/dupclicks",O_RDWR|O_CREAT,0666)
				  || &ADVDupError;
			}
			unless ($ADVduperror) {
				if (($ADVtime-$DupClicks{$DupClick}) > ($DupClickTime * 60)) {
					$DupClicks{$DupClick} = $ADVtime;
				}
				else { $DoNotLog = 1; }
				if ($DBMType==2) { dbmclose (%DupClicks); }
				else { untie %DupClicks; }
			}
		}
		&ADVLockClose (DUPCLICKS, "dupclicks.txt");
	}
	&ADVLockOpen (ADVDISPLAY, "$subdir/$displayad.txt");
	if ($ADVlockerror) {
		&ADVLockClose (ADVDISPLAY,"$subdir/$displayad.txt");
		&BadRef;
		return;
	}
	@ADVdisplaylines = <ADVDISPLAY>;
	chomp (@ADVdisplaylines);
	unless ($DoNotLog) { $ADVdisplaylines[2] += 1; }
	unless (@ADVdisplaylines < 1) {
		seek(ADVDISPLAY, 0, 0);
		foreach $key (0..20) {
			print ADVDISPLAY "$ADVdisplaylines[$key]\n";
		}
		if ($LogByZone) {
			$ADVlogzone = "$advertzone $ADVID";
			unless ($ADVID) { $ADVlogzone .= "none"; }
			$ADVlogzone .= " C";
			if (@ADVdisplaylines > 21) {
				foreach $key (21..(@ADVdisplaylines-1)) {
					if (!($DoNotLog)
					  && ($ADVdisplaylines[$key] =~ /^$ADVlogzone (\d*)/)) {
						$count = $1;
						$count++;
						print ADVDISPLAY "$ADVlogzone $count\n";
						$ADVlogzonecatch = 1;
					}
					else {
						print ADVDISPLAY "$ADVdisplaylines[$key]\n";
					}
				}
			}
			unless ($ADVlogzonecatch || $DoNotLog) {
				print ADVDISPLAY "$ADVlogzone 1\n";
			}
		}
		truncate (ADVDISPLAY, tell(ADVDISPLAY));
	}
	&ADVLockClose (ADVDISPLAY, "$subdir/$displayad.txt");
	$ADVacc = 0;
	unless ($DoNotLog) {
		if ($ADVLogIP) {
			&ADVLockOpen (IPLOG,"$subdir/$displayad.$ADVmon$ADVmday.log","a");
			unless ($ADVlockerror) {
				print IPLOG "$ADVhour:$ADVmin C $ENV{'REMOTE_HOST'}\n";
			}
			&ADVLockClose (IPLOG, "$subdir/$displayad.$ADVmon$ADVmday.log");
		}
		&ADVLockOpen (DBMLIST, "dbmlist.txt");
		if ($ADVlockerror) {
			&ADVLockClose (DBMLIST, "dbmlist.txt");
			$ADVdbmerror = 1;
		}
		else { &ADVDBMOpen; }
		unless ($ADVdbmerror) {
			($ADVmax,$ADVshown,$ADVvisits,$ADVimage,$ADVstart,$ADVweight,
			  $ADVzone,$ADVraw,$ADVratio,$ADVclicksfrom) = split(/\t/,$DBMList{$displayad});
			$DBMList{$displayad} = "$ADVmax\t$ADVshown\t$ADVdisplaylines[2]\t$ADVimage\t$ADVstart\t$ADVweight\t";
			$DBMList{$displayad} .= "$ADVzone\t$ADVraw\t$ADVratio\t$ADVclicksfrom";
		}
		if (($ADVID) && (-e "$adverts_dir/$newsubdir/$ADVID.txt")) {
			&ADVLockOpen (ADVDISPLAY, "$newsubdir/$ADVID.txt");
			unless ($ADVlockerror) {
				@ADVIDdisplaylines = <ADVDISPLAY>;
				unless (@ADVIDdisplaylines < 1) {
					chomp ($ADVIDdisplaylines[20]);
					($ADVIDclicksfrom,$ADVIDclicksratio) = split(/\|/, $ADVIDdisplaylines[20]);
					$ADVIDclicksfrom++;
					seek(ADVDISPLAY, 0, 0);
					foreach $key (0..19) {
						print ADVDISPLAY "$ADVIDdisplaylines[$key]";
					}
					print ADVDISPLAY "$ADVIDclicksfrom|$ADVIDclicksratio\n";
					if (@ADVdisplaylines > 21) {
						foreach $key (21..(@ADVdisplaylines-1)) {
							print ADVDISPLAY "$ADVIDdisplaylines[$key]";
						}
					}
					truncate (ADVDISPLAY, tell(ADVDISPLAY));
					unless ($ADVdbmerror) {
						($ADVmax,$ADVshown,$ADVvisits,$ADVimage,$ADVstart,$ADVweight,
						  $ADVzone,$ADVraw,$ADVratio,$ADVclicksfrom) = split(/\t/,$DBMList{$ADVID});
						$DBMList{$ADVID} = "$ADVmax\t$ADVshown\t$ADVvisits\t$ADVimage\t$ADVstart\t$ADVweight\t";
						$DBMList{$ADVID} .= "$ADVzone\t$ADVraw\t$ADVratio\t$ADVIDclicksfrom|$ADVIDclicksratio";
					}
				}
			}
			&ADVLockClose (ADVDISPLAY, "$newsubdir/$ADVID.txt");
			$ADVacc = 0;
			&ADVLockOpen (DAILYLOG,"$newsubdir/$ADVID.log");
			unless ($ADVlockerror) {
				$ADVaccess = "$ADVmday $ADVmon $ADVyear X";
				$ADVlocation = tell DAILYLOG;
				while (defined($ADVline = <DAILYLOG>)) {
					chomp ($ADVline);
					if (($ADVacc,$ADVlogstring) = ($ADVline =~
					  /^(\d\d\d\d\d\d\d\d\d\d) (\d\d \d\d \d\d\d\d X)$/)) {
						if ($ADVaccess eq $ADVlogstring) {
							last;
						}
					}
					last if ($ADVaccess eq $ADVlogstring);
					$ADVlocation = tell DAILYLOG;
					$ADVacc = 0;
				}
				$ADVacc++;
				seek (DAILYLOG, $ADVlocation, 0);
				$ADVlongacc = sprintf("%010.10d",$ADVacc);
				print DAILYLOG "$ADVlongacc $ADVaccess\n";
			}
			&ADVLockClose (DAILYLOG, "$newsubdir/$ADVID.log");
		}
		&ADVDBMClose;
		&ADVLockClose (DBMLIST, "dbmlist.txt");
		&ADVLockOpen (DAILYLOG,"$subdir/$displayad.log");
		unless ($ADVlockerror) {
			$ADVaccess = "$ADVmday $ADVmon $ADVyear C";
			$ADVlocation = tell DAILYLOG;
			while (defined($ADVline = <DAILYLOG>)) {
				chomp ($ADVline);
				if (($ADVacc,$ADVlogstring) = ($ADVline =~
				  /^(\d\d\d\d\d\d\d\d\d\d) (\d\d \d\d \d\d\d\d C)$/)) {
					if ($ADVaccess eq $ADVlogstring) {
						last;
					}
				}
				last if ($ADVaccess eq $ADVlogstring);
				$ADVlocation = tell DAILYLOG;
				$ADVacc = 0;
			}
			$ADVacc++;
			seek (DAILYLOG, $ADVlocation, 0);
			$ADVlongacc = sprintf("%010.10d",$ADVacc);
			print DAILYLOG "$ADVlongacc $ADVaccess\n";
		}
		&ADVLockClose (DAILYLOG, "$subdir/$displayad.log");
	}
	if ($ENV{'PERLXS'} eq "PerlIS") { print "HTTP/1.0 301 Found\n"; }
	else { print "Status: 301 Found\n"; }
	if ($rawmodedest) {
		print "Location: $rawmodedest\n\n";
	}
	else {
		$ADVdisplaylines[3] =~ s/<RAND>/$timestamp/g;
		print "Location: $ADVdisplaylines[3]\n\n";
	}
}

sub BadRef {
	print "Content-type: text/html\n\n";
	print "<HTML>";
	print "<HEAD><TITLE>WebAdverts Error!</TITLE></HEAD>\n";
	print "<BODY BGCOLOR=\"#ffffff\" TEXT=\"#000000\">\n";
	print "<HR><H1 ALIGN=CENTER>Invalid Destination</H1><HR>\n";
	print "<P>Sorry, but the server encountered an error ";
	print "while trying to redirect you to the destination ";
	print "of the banner on which you clicked! ";
	print "The most likely cause of the problem ";
	print "is that you attempted to click on a banner ";
	print "before the graphic image loaded. ";
	print "Another possibility is that you attempted to click ";
	print "on an old banner which was reloaded ";
	print "from your browser's cache.</P>\n";
	print "<HR></BODY></HTML>\n";
}

sub ADVLockOpen {
	$ADVlockerror = 0;
	local(*FILE,$lockfilename,$append) = @_;
	$_ = $lockfilename; /^(.+)$/; $lockfilename = $1;
	if ($append eq "x") {
		unless (-d "$adverts_dir/$subdir") {
			mkdir ("$adverts_dir/$subdir",0777);
			chmod 0777,"$adverts_dir/$subdir";
		}
	}
	unless (-e "$adverts_dir/$lockfilename") {
		open (FILE,">$adverts_dir/$lockfilename");
		print FILE "\n";
		close (FILE);
	}
	if ($append eq "a") {
		open (FILE,">>$adverts_dir/$lockfilename") || &ADVError($lockfilename);
	}
	else {
		open (FILE,"+<$adverts_dir/$lockfilename") || &ADVError($lockfilename);
	}
	if ($ADVUseLocking) {
		local($TrysLeft) = 750;
		while ($TrysLeft--) {
			select(undef,undef,undef,0.01);
			(flock(FILE,6)) || next;
			last;
		}
		unless ($TrysLeft >= 0) {
			&ADVError($lockfilename);
		}
	}
}

sub ADVLockClose {
	local(*FILE,$lockfilename) = @_;
	close (FILE);
}

sub ADVMasterLockOpen {
	$ADVlockerror = 0;
	local($TrysLeft) = 750;
	if ((-e "$adverts_dir/masterlockfile.lok")
	  && ((stat("$adverts_dir/masterlockfile.lok"))[9]+15<$ADVtime)) {
		unlink ("$adverts_dir/masterlockfile.lok");
	}
	while ($TrysLeft--) {
		if (-e "$adverts_dir/masterlockfile.lok") {
			select(undef,undef,undef,0.01);
		}
		else {
			open (MASTERLOCKFILE,">$adverts_dir/masterlockfile.lok");
			print MASTERLOCKFILE "\n";
			close (MASTERLOCKFILE);
			last;
		}
	}
	unless ($TrysLeft >= 0) {
		$ADVUseLocking = 1;
		&ADVError("masterlockfile.lok");
	}
}

sub ADVMasterLockClose {
	unlink ("$adverts_dir/masterlockfile.lok");
}

sub ADVError {
	local($lockfilename) = @_;
	if ($AdminRun && ($lockfilename ne "dbmlist.txt")) { &Error_File($lockfilename); }
	else { $ADVlockerror = 1; }
}

sub ADVDBMOpen {
	if ($DBMType==1) {
		tie (%DBMList,'AnyDBM_File',"$adverts_dir/dbmlist",O_RDWR|O_CREAT,0666,$DB_HASH)
		  || &ADVDBMError;
	}
	elsif ($DBMType==2) {
		dbmopen(%DBMList,"$adverts_dir/dbmlist",0666)
		  || &ADVDBMError;
	}
	else {
		tie (%DBMList,'AnyDBM_File',"$adverts_dir/dbmlist",O_RDWR|O_CREAT,0666)
		  || &ADVDBMError;
	}
}

sub ADVDBMClose {
	if ($DBMType==2) { dbmclose (%DBMList); }
	else { untie %DBMList; }
}

sub ADVDupError {
	$ADVduperror = 1;
}

sub ADVDBMError {
	$ADVdbmerror = 1;
}

1;
