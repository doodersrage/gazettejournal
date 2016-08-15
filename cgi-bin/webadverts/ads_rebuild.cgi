############################################
##                                        ##
##          WebAdverts (Rebuild)          ##
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

sub RebuildDatabase {
	&ConfirmAdminPassword(1);
	unlink ("$adverts_dir/adcount.txt");
	foreach $key (a..z,0..9) {
		unless (-d "$adverts_dir/$key") {
			mkdir ("$adverts_dir/$key",0777);
			chmod 0777,"$adverts_dir/$key";
		}
	}
	opendir (FILES,$adverts_dir);
	@files = readdir(FILES);
	closedir (FILES);
	&ADVLockOpen (GRPLIST, "groups.txt");
	seek (GRPLIST,0,0);
	foreach $file (@files) {
		if ($file =~ /(.*)\.grp$/) {
		$AccountName = $1;
		&CheckName;
		rename ("$adverts_dir/$1\.grp","$adverts_dir/$AccountName\.grp");
		print GRPLIST "$AccountName\n"; }
	}
	truncate (GRPLIST, tell(GRPLIST));
	&ADVLockClose (GRPLIST, "groups.txt");
	&ADVLockOpen (ADVLIST, "adlist.txt");
	@adverts = <ADVLIST>;
	chomp (@adverts);
	seek (ADVLIST,0,0);
	foreach $advert (@adverts) {
		next unless ($advert);
		$AccountName = $advert;
		&CheckName;
		next if ($OnList{$AccountName});
		next unless ((-e "$adverts_dir/$advert.txt")
		  || (-e "$adverts_dir/$subdir/$AccountName.txt"));
		unless (-d "$adverts_dir/$subdir") {
			mkdir ("$adverts_dir/$subdir",0777);
			chmod 0777,"$adverts_dir/$subdir";
		}
		foreach $file (@files) {
			if (($file =~ /^$advert\.(txt)/)
			  || ($file =~ /^$advert\.(log)/)
			  || ($file =~ /^$advert\.(\d\d\d\d\.log)/)) {
				rename ("$adverts_dir/$file",
				  "$adverts_dir/$subdir/$AccountName.$1");
			}
		}
		print ADVLIST "$AccountName\n";
		$OnList{$AccountName} = 1;
	}
	truncate (ADVLIST, tell(ADVLIST));
	&ADVLockClose (ADVLIST, "adlist.txt");
	&ADVLockOpen (ADVLIST, "adnew.txt");
	@adverts = <ADVLIST>;
	chomp (@adverts);
	seek (ADVLIST,0,0);
	foreach $advert (@adverts) {
		next unless ($advert);
		$AccountName = $advert;
		&CheckName;
		next if ($OnList{$AccountName});
		next unless ((-e "$adverts_dir/$advert.txt")
		  || (-e "$adverts_dir/$subdir/$AccountName.txt"));
		unless (-d "$adverts_dir/$subdir") {
			mkdir ("$adverts_dir/$subdir",0777);
			chmod 0777,"$adverts_dir/$subdir";
		}
		foreach $file (@files) {
			if (($file =~ /^$advert\.(txt)/)
			  || ($file =~ /^$advert\.(log)/)
			  || ($file =~ /^$advert\.(\d\d\d\d\.log)/)) {
				rename ("$adverts_dir/$file",
				  "$adverts_dir/$subdir/$AccountName.$1");
			}
		}
		print ADVLIST "$AccountName\n";
		$OnList{$AccountName} = 1;
	}
	foreach $advertcheck (@files) {
		if ($advertcheck =~ /^dbmlist/) { unlink ("$adverts_dir/$advertcheck"); }
		next unless ($advertcheck =~ /(.*)\.txt$/);
		$advert = $1;
		next if (($advert eq "adcount") || ($advert eq "adlist") || ($advert eq "adminlog")
		  || ($advert eq "adnew") || ($advert eq "adpassword") || ($advert eq "dupclicks")
		  || ($advert eq "dupviews") || ($advert eq "groups") || ($advert eq "register")
		  || ($advert eq "reject") || ($advert eq "welcome") || ($advert eq "update"));
		$AccountName = $advert;
		&CheckName;
		next if ($OnList{$AccountName});
		open (DISPLAY, "$adverts_dir/$advert.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		($max,$shown,$visits,$url,$image,$height,$width,
		  $alt,$pass,$text,$start,$weight,$zone,
		  $border,$target,$raw,$displayratio,$username,$email,
		  $displayzone,$clicksfrom) = @lines;
		next unless ($max || $displayratio || $clicksfrom);
		($max,$maxtype) = split(/\|/, $max);
		($displayratio,$displaycount) = split(/\|/, $displayratio);
		($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
		next if (($max =~ /[^\d-]/)
		  || ($shown =~ /[^\d-]/) || ($visits =~ /[^\d-]/)
		  || ($start =~ /[^\d-]/) || ($weight =~ /[^\d-]/)
		  || ($displayratio =~ /[^\d-]/) || ($displaycount =~ /[^\d-]/)
		  || ($clicksfrom =~ /[^\d-]/) || ($clicksratio =~ /[^\d-]/));
		unless (-d "$adverts_dir/$subdir") {
			mkdir ("$adverts_dir/$subdir",0777);
			chmod 0777,"$adverts_dir/$subdir";
		}
		foreach $file (@files) {
			if (($file =~ /^$advert\.(txt)/)
			  || ($file =~ /^$advert\.(log)/)
			  || ($file =~ /^$advert\.(\d\d\d\d\.log)/)) {
				rename ("$adverts_dir/$file",
				  "$adverts_dir/$subdir/$AccountName.$1");
			}
		}
		print ADVLIST "$AccountName\n";
		$OnList{$AccountName} = 1;
	}
	foreach $key (a..z,0..9) {
		opendir (FILES,"$adverts_dir/$key");
		@files = readdir(FILES);
		closedir (FILES);
		foreach $file (@files) {
			unless (($file =~ /^\./) || $OnList{$file}) {
				print ADVLIST "$file\n";
				$OnList{$AccountName} = 1;
			}
		}
	}
	truncate (ADVLIST, tell(ADVLIST));
	&ADVLockClose (ADVLIST, "adnew.txt");
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			%DBMList = ();
			$DBMList{'adcount.txt'} = "1\n0\n$time";
			foreach $account (keys %OnList) {
				$subdir = substr($account,0,1);
				$subdir .= "/$account";
				open (DISPLAY, "$adverts_dir/$subdir/$account.txt");
				@lines = <DISPLAY>;
				close (DISPLAY);
				chomp (@lines);
				($max,$shown,$visits,$url,$image,$height,$width,
				  $alt,$pass,$text,$start,$weight,$zone,
				  $border,$target,$raw,$displayratio,$username,$email,
				  $displayzone,$clicksfrom) = @lines;
				if ($image) { $image = "X"; }
				if ($raw =~ /<SCRIPT/) { $raw = "J"; }
				elsif ($raw) { $raw = "X"; }
				$DBMList{$account} = "$max\t$shown\t$visits\t$image\t$start\t$weight\t";
				$DBMList{$account} .= "$zone\t$raw\t$displayratio\t$clicksfrom";
			}
			&ADVDBMClose;
		}
	}
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	if ($AdminDisplaySetup) { &defineview; }
	else {
		$INPUT{'whichtype'} = "pending established groups";
		$INPUT{'whichtime'} = "active expired disabled";
		$INPUT{'whichzone'} = "";
		&reviewall;
	}
}

sub CheckDatabase {
	&ConfirmAdminPassword(1);
	&Header("WebAdverts","WebAdverts Database Integrity Check");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
	print "WebAdverts Database Integrity Check";
	print "</STRONG></BIG></BIG>\n";
	open (ADVLIST, "$adverts_dir/adnew.txt");
	while (defined($ADVlist = <ADVLIST>)) {
		chomp $ADVlist;
		$Adverts_List{$ADVlist} = 1;
		$MasterList{$ADVlist} = 1;
	}
	close (ADVLIST);
	open (ADVLIST, "$adverts_dir/adlist.txt");
	while (defined($ADVlist = <ADVLIST>)) {
		chomp $ADVlist;
		$Adverts_List{$ADVlist} = 1;
		$MasterList{$ADVlist} = 1;
	}
	close (ADVLIST);
	opendir (FILES,$adverts_dir);
	@files = readdir(FILES);
	closedir (FILES);
	foreach $advertcheck (@files) {
		next unless ($advertcheck =~ /(.*)\.txt$/);
		$advert = $1;
		next if (($advert eq "adcount") || ($advert eq "adlist") || ($advert eq "adminlog")
		  || ($advert eq "adnew") || ($advert eq "adpassword") || ($advert eq "dbmlist")
		  || ($advert eq "dupclicks") || ($advert eq "dupviews") || ($advert eq "groups")
		  || ($advert eq "register") || ($advert eq "reject") || ($advert eq "welcome")
		  || ($advert eq "update"));
		open (DISPLAY, "$adverts_dir/$advert.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		($max,$shown,$visits,$url,$image,$height,$width,
		  $alt,$pass,$text,$start,$weight,$zone,
		  $border,$target,$raw,$displayratio,$username,$email,
		  $displayzone,$clicksfrom) = @lines;
		next unless ($max || $displayratio || $clicksfrom);
		($max,$maxtype) = split(/\|/, $max);
		($displayratio,$displaycount) = split(/\|/, $displayratio);
		($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
		next if (($max =~ /[^\d-]/)
		  || ($shown =~ /[^\d-]/) || ($visits =~ /[^\d-]/)
		  || ($start =~ /[^\d-]/) || ($weight =~ /[^\d-]/)
		  || ($displayratio =~ /[^\d-]/) || ($displaycount =~ /[^\d-]/)
		  || ($clicksfrom =~ /[^\d-]/) || ($clicksratio =~ /[^\d-]/));
		$Adverts_Physical{$advert} = 1;
		$MasterList{$advert} = 1;
	}
	foreach $key (a..z,0..9) {
		opendir (FILES,"$adverts_dir/$key");
		@files = readdir(FILES);
		closedir (FILES);
		foreach $file (@files) {
			unless ($file =~ /^\./) {
				$Adverts_Physical{$file} = 1;
				$MasterList{$file} = 1;
			}
		}
	}
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			foreach $account (keys %DBMList) {
				next if ($account eq "adcount.txt");
				$Adverts_DBM{$account} = 1;
				$MasterList{$account} = 1;
				($dbmmax,$dbmshown,$dbmvisits,$dbmimage,$dbmstart,$dbmweight,
				  $dbmzone,$dbmraw,$dbmdisplayratio,$dbmclicksfrom) = split(/\t/,$DBMList{$account});
				$subdir = substr($account,0,1);
				$subdir .= "/$account";
				next unless (-s "$adverts_dir/$subdir/$account.txt");
				open (DISPLAY, "<$adverts_dir/$subdir/$account.txt");
				@lines = <DISPLAY>;
				close (DISPLAY);
				chomp (@lines);
				($max,$shown,$visits,$url,$image,$height,$width,
				  $alt,$pass,$text,$start,$weight,$zone,
				  $border,$target,$raw,$displayratio,$username,$email,
				  $displayzone,$clicksfrom) = @lines;
				if (($max eq $dbmmax) && ($shown eq $dbmshown) && ($visits eq $dbmvisits)
				  && ($start eq $dbmstart) && ($weight eq $dbmweight) && ($zone eq $dbmzone)
				  && ($displayratio eq $dbmdisplayratio) && ($clicksfrom eq $dbmclicksfrom)) {
					$Adverts_Match{$account} = 1;
				}
			}
			&ADVDBMClose;
		}
	}
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	print "<P><CENTER><TABLE BORDER CELLPADDING=3>\n";
	print "<TR><TH>Account</TH><TH>List?</TH><TH>Physical?</TH><TH>DBM?</TH><TH>Match?</TH></TR>\n";
	foreach $key (sort (keys %MasterList)) {
		print "<TR><TD>$key</TD>";
		if ($Adverts_List{$key}) { print "<TD ALIGN=CENTER>X</TD>"; }
		else { print "<TD>&nbsp;</TD>"; $NotOK = 1; }
		if ($Adverts_Physical{$key}) { print "<TD ALIGN=CENTER>X</TD>"; }
		else { print "<TD>&nbsp;</TD>"; $NotOK = 1; }
		if ($Adverts_DBM{$key}) { print "<TD ALIGN=CENTER>X</TD>"; }
		else { print "<TD>&nbsp;</TD>"; $NotOK = 1; }
		if ($Adverts_Match{$key}) { print "<TD ALIGN=CENTER>X</TD>"; }
		else { print "<TD>&nbsp;</TD>"; $NotOK = 1; }
		print "</TR>\n";
	}
	print "</TABLE>\n<P><STRONG>";
	if ($NotOK) {
		print "There seem to be problems. Try rebuilding the database.\n";
		print "<P><FORM METHOD=POST ACTION=$admin_cgi>",
		  "<INPUT TYPE=HIDDEN NAME=password VALUE=$INPUT{'password'}>",
		  "<INPUT TYPE=SUBMIT NAME=rebuild VALUE=\"Rebuild Database\">",
		  "</FORM>\n";
	}
	else { print "Everything seems to be fine; all data is consistent.\n"; }
	print "</CENTER>\n";
	&LinkBack;
	&Footer;
}

1;
