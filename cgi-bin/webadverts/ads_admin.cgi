#!/usr/local/bin/perl

############################################
##                                        ##
##           WebAdverts (Admin)           ##
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

# Define the locations of your "ads_settings," "ads_text"
# and "ads_rebuild" files.

$settings_path = "/Library/WebServer/Documents/gazette/cgi-bin/webadverts/ads_settings.cgi";
$text_path = "/Library/WebServer/Documents/gazette/cgi-bin/webadverts/ads_text.cgi";
$rebuild_path = "/Library/WebServer/Documents/gazette/cgi-bin/webadverts/ads_rebuild.cgi";

# NOTHING BELOW THIS LINE NEEDS TO BE ALTERED!

require $settings_path;
require $text_path;
require $display_path;

$version = "3.10";
$AdminRun = 1;
$cryptword = 0;
$time = time;

$_ = $UserUploadDir; /^(.+)$/; $UserUploadDir = $1;

print "Content-type: text/html\n\n";

$ADVtime = time;
unless ($ADVUseLocking) { &ADVMasterLockOpen; }

($SSIvirtual,$SSIfile) = (&FindSpecifics)[1,9];

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
unless ($ENV{'REMOTE_HOST'}) {
	$ENV{'REMOTE_HOST'} = $ENV{'REMOTE_ADDR'};
}

if ($BannedIPs) {
	@ignoredips = split(/ /,$BannedIPs);
	foreach $ignoredip (@ignoredips) {
		if (($ENV{'REMOTE_HOST'} =~ /$ignoredip/i)
		  || ($ENV{'REMOTE_ADDR'} =~ /$ignoredip/i)) {
			&Header("$text{'9000'}","$text{'9010'}");
			print "<P ALIGN=CENTER>$text{'9011'}\n";
			&Footer("$text{'9010'}");
		}
	}
}

if ($ENV{'CONTENT_TYPE'} =~ /^multipart\/form-data/) {
	if ($ENV{'CONTENT_TYPE'} =~ /boundary=(\"?([^\";,]+)\"?)*/) { $boundary = $1; }
	binmode STDIN;
	read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});
	@buffer = split(/\r\n/,$buffer);
	foreach $line (@buffer) {
		if ($line =~ /$boundary/) { $Current = ""; next; }
		if ($line =~ /Content-Disposition/) {
			if ($line =~ /^.+name\s*=\s*"*([^\s"]+).+$/) { $Current = $1; }
			$INPUT{$Current} = ""; next;
		}
		if ($line =~ /Content-Type/) {
			if ($line =~ /gif/) { $BannerType = "GIF"; }
			elsif (($line =~ /jpeg/) || ($line =~ /jpg/)) { $BannerType = "JPG"; }
			$Current = "BannerFile"; $INPUT{'BannerFile'} = ""; next;
		}
		if (($line eq "") && ($Current ne "BannerFile")) { next; }
		if ($INPUT{$Current} && ($Current eq "BannerFile")) { $INPUT{$Current} .= "\r\n"; }
		$INPUT{$Current} .= $line;
	}
}
else {
	read(STDIN, $buffer, $ENV{'CONTENT_LENGTH'});
	@pairs = split(/&/, $buffer);
	foreach $pair (@pairs) {
		($name, $value) = split(/=/, $pair);
		$value =~ tr/+/ /;
		$value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		if ($INPUT{$name}) { $INPUT{$name} = $INPUT{$name}." ".$value; }
		else { $INPUT{$name} = $value; }
	}
}

if ($INPUT{'rebuild'}) {
	require $rebuild_path;
	&RebuildDatabase;
}

if ($INPUT{'check_database'}) {
	require $rebuild_path;
	&CheckDatabase;
}

if ($ENV{'QUERY_STRING'} =~ /reginfo/i) { &reginfo; }
elsif ($ENV{'QUERY_STRING'} =~ /admin/i) { &adminintro; }
elsif ($INPUT{'getpass'}) { &GetPassword; }
elsif ($INPUT{'register'}) { &register; }
elsif ($INPUT{'expireemailupdate'}) { &ExpireEmailUpdate; }
elsif ($INPUT{'emails'}) { &emailmembers; }
elsif ($INPUT{'edit'}) { &edit; }
elsif ($INPUT{'newuserpassword'}) { &NewUserPassword; }
elsif ($INPUT{'UserEdit'}) { &UserEdit; }
elsif ($INPUT{'uploadbanner'}) { &UploadBanner; }
elsif ($INPUT{'renameaccount'}) { &RenameAccount; }
elsif ($INPUT{'del'}) { &del; }
elsif ($INPUT{'delgroup'}) { &delgroup; }
elsif ($INPUT{'newpass'}) { &newpass; }
elsif ($INPUT{'resetcount'}) { &resetcount; }
elsif ($INPUT{'editfinal'}) { &editfinal; }
elsif ($INPUT{'delfinal'}) { &delfinal; }
elsif ($INPUT{'delgroupfinal'}) { &delgroupfinal; }
elsif ($INPUT{'reviewone'} eq "Define View") {
	if ($AdminDisplaySetup) { &defineview; }
	else {
		$INPUT{'whichtype'} = "pending established groups";
		$INPUT{'whichtime'} = "active expired disabled";
		$INPUT{'whichzone'} = "";
		&reviewall;
	}
}
elsif ($INPUT{'reviewone'} eq "Review All Accounts") { &reviewall; }
elsif ($INPUT{'reviewone'}) { &reviewone; }
elsif ($INPUT{'dailystats'}) { &dailystats; }
elsif ($INPUT{'monthlystats'}) { &monthlystats; }
elsif ($INPUT{'masteriplog'}) { &masteriplog; }
elsif ($INPUT{'iplog'}) { &iplog; }
elsif ($INPUT{'logbyzone'}) { &logbyzone; }
elsif ($INPUT{'cheatercheck'}) { &cheatercheck; }
elsif ($INPUT{'resetadminlog'}) { &resetadminlog; }
elsif ($INPUT{'adminlog'}) { &adminlog; }
elsif ($INPUT{'listemail'}) { &ListEmail; }
elsif ($INPUT{'expireemail'}) { &ExpireEmail; }
elsif ($INPUT{'editgroupfinal'}) { &editgroupfinal; }
elsif ($INPUT{'editgroup'}) { &editgroup; }
else { &userintro; }

sub userintro {
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER>$text{'1010'}\n",
	  "<CENTER><FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<P><STRONG>$text{'1011'}</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=reviewone SIZE=15></FONT>\n",
	  "<BR><STRONG>$text{'1012'}</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=PASSWORD NAME=password SIZE=15></FONT>\n",
	  "<P><INPUT TYPE=SUBMIT ",
	  "VALUE=\"$text{'1013'}\">\n",
	  "</FORM></CENTER>\n";
	if ($AllowUserEdit) {
		print "<P><HR><P ALIGN=CENTER>$text{'1020'}\n",
		  "<CENTER><FORM METHOD=POST ACTION=$admin_cgi>\n",
		  "<P><STRONG>$text{'1021'}</STRONG> ",
		  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=reviewone SIZE=15></FONT>\n",
		  "<BR><STRONG>$text{'1022'}</STRONG> ",
		  "<FONT FACE=\"Courier\"><INPUT TYPE=PASSWORD NAME=password SIZE=15></FONT>\n",
		  "<INPUT TYPE=HIDDEN NAME=newuser VALUE=yes>\n",
		  "<P><INPUT TYPE=SUBMIT VALUE=\"$text{'1023'}\">\n",
		  "</FORM></CENTER>\n";
	}
	if ($mailprog) {
		print "<P><HR><P ALIGN=CENTER>$text{'1030'}\n",
		  "<CENTER><FORM METHOD=POST ACTION=$admin_cgi>\n",
		  "<P><STRONG>$text{'1031'}</STRONG> ",
		  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=getpass SIZE=15></FONT>\n",
		  "<P><INPUT TYPE=SUBMIT VALUE=\"$text{'1032'}\">\n",
		  "</FORM></CENTER>\n";
	}
	&Footer;
}

sub adminintro {
	open (PASSWORD, "<$adverts_dir/adpassword.txt");
	$password = <PASSWORD>;
	close (PASSWORD);
	chomp ($password);
	if (!$password) { &InitializePassword; }
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER>$text{'1040'}\n",
	  "<CENTER><FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<P><STRONG>$text{'1041'}</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=PASSWORD NAME=password SIZE=15></FONT>\n",
	  "<INPUT TYPE=HIDDEN NAME=reviewone ",
	  "VALUE=\"Define View\">\n",
	  "<P><INPUT TYPE=SUBMIT VALUE=\"$text{'1042'}\"> ",
	  "</FORM></CENTER>\n";
	&Footer;
}

sub InitializePassword {
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P>Before you can do anything else, ",
	  "you'll need to set your administrative password. ",
	  "This will allow you to access the admin functions, ",
	  "create and edit accounts, review statistics, etc. ",
	  "Please enter your desired password below. ",
	  "(Enter it twice.)\n",
	  "<FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<INPUT TYPE=HIDDEN NAME=newpass VALUE=yes> ",
	  "<P><CENTER><INPUT TYPE=SUBMIT ",
	  "VALUE=\"Set Admin Password:\"> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=PASSWORD NAME=passad SIZE=10> ",
	  "<INPUT TYPE=PASSWORD NAME=passad2 SIZE=10></FONT>\n",
	  "</CENTER></FORM>\n";
	&Footer;
}

sub ConfirmAdminPassword {
	local($which_admin) = @_;
	if ($INPUT{'password'}) {
		$newpassword = crypt($INPUT{'password'}, "aa");
	}
	else {
		&Header("$text{'9000'}","$text{'9020'}");
		print "<P ALIGN=CENTER>$text{'9021'}\n";
		&Footer;
	}
	open (PASSWORD, "<$adverts_dir/adpassword.txt");
	$password = <PASSWORD>;
	close (PASSWORD);
	chomp ($password);
	unless ($password && ($newpassword eq $password)) {
		if ($AllowUserEdit && $INPUT{'newuser'} && ($which_admin == 2)) {
			&Header("$text{'9000'}","$text{'9030'}");
			print "<P ALIGN=CENTER>$text{'9031'}\n";
			&Footer;
		}
		else {
			&Header("$text{'9000'}","$text{'9022'}");
			print "<P ALIGN=CENTER>$text{'9023'}\n";
			&Footer;
		}
	}
	$cryptword = 1;
}

sub ConfirmUserPassword {
	unless ($INPUT{'password'}) {
		&Header("$text{'9000'}","$text{'9020'}");
		print "<P ALIGN=CENTER>$text{'9021'}\n";
		&Footer;
	}
	if ($INPUT{'admincheck'}) {
		&ConfirmAdminPassword(2);
	}
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	$pass = $lines[0];
	unless ($INPUT{'password'} eq $pass) {
		if ($INPUT{'groupstatus'}) {
			open (DISPLAY, "<$adverts_dir/$INPUT{'groupstatus'}.grp");
			@grpck = <DISPLAY>;
			close (DISPLAY);
			chomp (@grpck);
		}
		unless ($grpck[0] && ($INPUT{'password'} eq $grpck[0])) {
			&ConfirmAdminPassword(2);
		}
	}
}

sub CheckName {
	$AccountName =~ s/[^\w\.\-\']//g;
	$AccountName =~ tr/A-Z/a-z/;
	unless ($AccountName =~ /^[a-z,0-9]/) { $AccountName = "a".$AccountName; }
	$AccountName = substr($AccountName,0,25);
	$subdir = substr($AccountName,0,1);
	$subdir .= "/$AccountName";
	$_ = $AccountName; /^(.+)$/; $AccountName = $1;
	$_ = $subdir; /^(.+)$/; $subdir = $1;
	if (-e "$adverts_dir/$subdir/$AccountName.txt") {
		unless (-e "$adverts_dir/$subdir/$AccountName.dat") {
			open (OLDDATA, "$adverts_dir/$subdir/$AccountName.txt");
			@lines = <OLDDATA>;
			close (OLDDATA);
			chomp (@lines);
			($pass,$username,$email) = @lines[8,17,18];
			open (NEWDATA,">$adverts_dir/$subdir/$AccountName.dat");
			print NEWDATA "$pass\n$username\n$email\n";
			close (NEWDATA);
		}
	}
}

sub GetPassword {
	$AccountName = $INPUT{'getpass'};
	&CheckName;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	($pass,$email) = @lines[0,2];
	unless ($email) {
		&Header("$text{'9000'}","$text{'9040'}");
		print "<P ALIGN=CENTER>$text{'9041'} ";
		print "<STRONG>$AccountName</STRONG> $text{'9042'}\n";
		&Footer;
	}
	&SendMail($email,"getpass");
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER>$text{'1050'} <STRONG>$AccountName</STRONG> ";
	print "$text{'1051'}\n";
	&Footer;
}

sub defineview {
	&ConfirmAdminPassword(1);
	&Header("$text{'1000'}","$text{'1001'}");
	unless ((-e "$adverts_dir/register.txt") && (-w "$adverts_dir/register.txt")) {
		print "<P ALIGN=CENTER><STRONG>Unregistered copy.</STRONG> ";
		print "<A HREF=\"$admin_cgi?reginfo\">Click here</A> ";
		print "for registration info.<P><HR>\n";
	}
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>Select Which Accounts ",
	  "You Wish to View:</STRONG></BIG></BIG>\n",
	  "<CENTER><FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<INPUT TYPE=HIDDEN NAME=password ",
	  "VALUE=\"$INPUT{'password'}\">\n";
	if ((-s "$adverts_dir/adnew.txt") || (-s "$adverts_dir/groups.txt")) {
		print "<P><STRONG>Pending Accounts (those awaiting ";
		print "administrative approval), Established Accounts,\n";
		print "<BR>and/or Defined Groups (&quot;sets&quot; ";
		print "of accounts):</STRONG>\n<BR>";
		if (-s "$adverts_dir/adnew.txt") {
			print "<INPUT TYPE=CHECKBOX NAME=whichtype ";
			print "VALUE=pending>Pending Accounts ";
		}
		print "<INPUT TYPE=CHECKBOX NAME=whichtype ";
		print "VALUE=established CHECKED>Established Accounts";
		if (-s "$adverts_dir/groups.txt") {
			print " <INPUT TYPE=CHECKBOX NAME=whichtype ";
			print "VALUE=groups>Defined Groups";
		}
		print "\n";
		print "<BR><SMALL><EM>(If no selection is made, ";
		print "only Established Accounts will be displayed.)</EM></SMALL>\n";
		print "<P><STRONG><EM>(If including Established Accounts, ";
		print "select which ones below.)</EM></STRONG>\n";
	}
	else {
		print "<INPUT TYPE=HIDDEN NAME=whichtype ";
		print "VALUE=established>\n";
	}
	print "<P><STRONG>Active Accounts, Expired Accounts,\n",
	  "<BR>and/or Disabled Accounts (those with weights ",
	  "temporarily set to 0):</STRONG>\n",
	  "<BR><INPUT TYPE=CHECKBOX NAME=whichtime ",
	  "VALUE=active CHECKED>Active Accounts ",
	  "<INPUT TYPE=CHECKBOX NAME=whichtime ",
	  "VALUE=expired>Expired Accounts ",
	  "<INPUT TYPE=CHECKBOX NAME=whichtime ",
	  "VALUE=disabled>Disabled Accounts\n",
	  "<BR><SMALL><EM>(If no selection is made, ",
	  "only Active Accounts will be displayed.)</EM></SMALL>\n";
	if (@zones) {
		print "<P><STRONG>Accounts Displaying in Zone(s):</STRONG>\n<BR>";
		foreach $setzone (sort (@zones)) {
			print "<INPUT TYPE=CHECKBOX NAME=whichzone ";
			print "VALUE=\"$setzone\" CHECKED>$setzone ";
		}
		print "\n";
		print "<BR><SMALL><EM>(If no selection is made, ";
		print "accounts from all zones will be displayed.)</EM></SMALL>\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=reviewone ";
	print "VALUE=\"Review All Accounts\">\n";
	print "<P><INPUT TYPE=SUBMIT VALUE=\"Review Accounts\"> ";
	print "</FORM></CENTER>\n";
	&Footer;
}

sub reviewall {
	&ConfirmAdminPassword(1);
	if (!(-d "$adverts_dir/a") || !(-d "$adverts_dir/0")) {
		require $rebuild_path;
		&RebuildDatabase;
	}
	unless ($MasterIPLogDays) { $MasterIPLogDays = 2; }
	if (!(-e "$adverts_dir/update.txt") || ((-M "$adverts_dir/update.txt") > 1)) {
		foreach $key (a..z,0..9) {
			opendir (FILES,"$adverts_dir/$key");
			@files = readdir(FILES);
			closedir (FILES);
			foreach $file (@files) {
				next unless (-d "$adverts_dir/$key/$file");
				opendir (SUBFILES,"$adverts_dir/$key/$file");
				@subfiles = readdir(SUBFILES);
				closedir (SUBFILES);
				foreach $subfile (@subfiles) {
					if (($subfile=~/\d\d\d\d\.log/)
					  && ((-M "$adverts_dir/$key/$file/$subfile") > $MasterIPLogDays)) {
						unlink "$adverts_dir/$key/$file/$subfile";
					}
				}
			}
		}
		if (-e "$adverts_dir/register.txt") {
			open (REGISTER, "$adverts_dir/register.txt");
			$register = <REGISTER>;
			close (REGISTER);
			chomp $register;
			$count = (length($register)-1);
			foreach $key (0..$count) {
				$fig = substr($register,$key,1); $fig = ord($fig); $checksum += $fig;
			}
			unless (($count==5) && ($checksum==688)) {
				unlink ("$adverts_dir/register.txt");
			}
		}
		open (UPDATE,">$adverts_dir/update.txt");
		print UPDATE " ";
		close (UPDATE);
	}
	&Header("$text{'1000'}","$text{'1001'}");
	unless ((-e "$adverts_dir/register.txt") && (-w "$adverts_dir/register.txt")) {
		print "<P ALIGN=CENTER><STRONG>";
		print "Unregistered copy.</STRONG> ";
		print "<A HREF=\"$admin_cgi?reginfo\">Click here</A> ";
		print "for registration info.<P><HR>\n";
	}
	unless ($INPUT{'whichtype'}) { $INPUT{'whichtype'} = "established"; }
	unless ($INPUT{'whichtime'}) { $INPUT{'whichtime'} = "active"; }
	if ($INPUT{'whichtype'} =~ /established/) {
		print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
		print "The Following Accounts ";
		print "Have Been Established:</STRONG></BIG></BIG>\n";
		if ($AdminDisplaySetup) {
			print "<P ALIGN=CENTER>";
			print "(Accounts Included: ";
			print "$INPUT{'whichtime'})\n";
			if ($INPUT{'whichzone'}) {
				@whichzones = split(/\s+/,$INPUT{'whichzone'});
				print "<BR>(Zones Included: ";
				print "$INPUT{'whichzone'})\n";
			}
		}
		open (LIST, "<$adverts_dir/adlist.txt");
		@advertisements = <LIST>;
		close (LIST);
		chomp (@advertisements);
		@sortedadverts = sort (@advertisements);
		&ADVLockOpen (DBMLIST, "dbmlist.txt");
		if ($ADVlockerror) { &Error_DBM; }
		else {
			&ADVDBMOpen;
			if ($ADVdbmerror) { &Error_DBM; }
			else {
				foreach $advertiser (@sortedadverts) {
					$name = $advertiser;
					next if (length($advertiser) < 1);
					($max,$shown,$visits,$image,$start,$weight,
					  $zone,$raw,$displayratio,$clicksfrom) = split(/\t/,$DBMList{$name});
					if (length($zone) > 2) { $ZoneColumn = 1; last; }
				}
				print "<FORM METHOD=POST ACTION=$admin_cgi>",
				  "<INPUT TYPE=HIDDEN NAME=password ",
				  "VALUE=\"$INPUT{'password'}\">",
				  "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
				print "<P><CENTER><TABLE CELLPADDING=3>\n",
				  "<TR ALIGN=CENTER VALIGN=BOTTOM>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2010'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2011'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2012'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
				if ($ZoneColumn) {
					print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2013'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
				}
				print "<TD><SMALL><FONT $fontspec><EM>$text{'2014'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2015'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2016'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2017'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
				  "<TD><SMALL><FONT $fontspec><EM>$text{'2019'}</EM><BR><HR NOSHADE></FONT></SMALL></TD></TR>\n";
				($other,$exposures,$starttime) = split(/\n/,$DBMList{'adcount.txt'});
				($exposures,$other) = split(/\|/,$exposures);
				foreach $advertiser (@sortedadverts) {
					$expired = 0;
					$name = $advertiser;
					next if (length($advertiser) < 1);
					($max,$shown,$visits,$image,$start,$weight,
					  $zone,$raw,$displayratio,$clicksfrom) = split(/\t/,$DBMList{$name});
					unless ($max || $displayratio || $clicksfrom) {
						print "<TR ALIGN=CENTER>";
						print "<TD COLSPAN=10><SMALL><FONT $fontspec>";
						print "[ Database Error: ";
						print "$advertiser.txt ]</FONT></SMALL></TD></TR>\n";
						next;
					}
					($max,$maxtype) = split(/\|/, $max);
					unless ($maxtype) { $maxtype = "E"; }
					($text,$texttype) = split(/\|/, $text);
					unless ($texttype) { $texttype = "B"; }
					($displayratio,$displaycount) = split(/\|/, $displayratio);
					($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
					if (($maxtype eq "N") && ($displayratio == 0) && ($clicksratio == 0)
					  && ($start < $time) && ($weight == 1) && (length($zone) < 3)) {
						$AlwaysAvailable = 1;
					}
					if ($DefaultBanner eq $name) {
						$DefaultAvailable = 1;
					}
					if ($INPUT{'whichzone'} && (length($zone)>2)) {
						$zoneok = 0;
						foreach $whichzones (@whichzones) {
							if ($zone =~ /$whichzones/) {
								$zoneok = 1;
							}
						}
						next unless ($zoneok);
					}
					if (($displayratio > 0) || ($displaycount > 0)
					  || ($clicksfrom > 0) || ($clicksratio > 0)) {
						$SecondaryDisplay = 1;
					}
					if ($maxtype eq "N") { $max = 0; }
					if ((($maxtype eq "E") || ($maxtype eq "N"))
					  && ($displayratio > 0)) {
						$max = $max+int($displaycount/$displayratio);
					}
					if ((($maxtype eq "E") || ($maxtype eq "N"))
					  && ($clicksratio > 0)) {
						$max = $max+($clicksfrom*$clicksratio);
					}
					if ($max == 0) { $max = "0"; }
					$runtime = 0;
					if ($start) {
						$runtime = $time - $start + 1;
					}
					$average = 0;
					if (($weight > 0) && ($runtime > 86400)) {
						&GetAverage;
					}
					$expirationstatus = "";
					if ($maxtype eq "D") {
						($sec,$min,$hour,$mday,$mon,$year,
						  $wday,$yday,$isdst) = localtime($max+($HourOffset*3600));
						$year += 1900;
						$expirationstatus .= "<TD NOWRAP><SMALL><FONT $fontspec>$mday $months[$mon] $year";
						unless ($max > $time) {
							$expired = 1 ;
							$expirationstatus .=
							  "<BR><EM>$text{'2100'}</EM>";
						}
						$expirationstatus .= "</FONT></SMALL></TD>";
					}
					elsif (($maxtype eq "N") && ($displayratio == 0) && ($clicksratio == 0)) {
						$expirationstatus .= "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2101'}</EM></FONT></SMALL></TD>";
					}
					else {
						$expirationstatus .= "<TD NOWRAP><SMALL><FONT $fontspec>".&commas($max);
						if ($maxtype eq "C") {
							$expirationstatus .= " $text{'2110'}";
							if (($average == 0) || ($shown == 0) || ($visits == 0)) {
								$expirationstatus .= "<BR><EM>$text{'2102'}</EM>";
							}
							elsif ($max > $visits) {
								$daystogo = (($max-$visits)/($average*($visits/$shown)));
								$calculatedend = $time+($daystogo*86400);
								($sec,$min,$hour,$mday,$mon,$year,
								  $wday,$yday,$isdst) = localtime($calculatedend+($HourOffset*3600));
								$year += 1900;
								$expirationstatus .= "<BR><EM>(~ $mday $months[$mon] $year)</EM>";
							}
							else {
								$expired = 1;
								$expirationstatus .=
								  "<BR><EM>$text{'2100'}</EM>";
							}
						}
						else {
							$expirationstatus .= " $text{'2111'}";
							if (($displayratio > 0) || ($clicksratio > 0)) {
								$expirationstatus .= "<BR><EM>$text{'2101'}</EM>";
							}
							elsif ($average == 0) {
								$expirationstatus .= "<BR><EM>$text{'2102'}</EM>";
							}
							elsif ($max > $shown) {
								$daystogo = (($max-$shown)/$average);
								$calculatedend = $time+($daystogo*86400);
								($sec,$min,$hour,$mday,$mon,$year,
								  $wday,$yday,$isdst) = localtime($calculatedend+($HourOffset*3600));
								$year += 1900;
								$expirationstatus .= "<BR><EM>(~ $mday $months[$mon] $year)</EM>";
							}
							else {
								$expired = 1;
								$expirationstatus .=
								  "<BR><EM>$text{'2100'}</EM>";
							}
						}
						$expirationstatus .= "</FONT></SMALL></TD>";
					}
					next if (($expired == 1) && ($INPUT{'whichtime'} !~ /expired/));
					next if (($expired == 0) && ($weight == 0)
					  && ($INPUT{'whichtime'} !~ /disabled/));
					next if (($expired == 0) && ($weight > 0)
					  && ($INPUT{'whichtime'} !~ /active/));
					if (($shown == 0) || ($visits == 0)) {
						$perc = "$text{'2020'}";
						$ratio = "$text{'2020'}";
					}
					else {
						$perc = ((100*($visits/$shown))+.05001);
						$ratio = (($shown/$visits)+.5001);
					}
					unless ($perc eq "$text{'2020'}") {
						$perc =~ s/(\d+\.\d).*/$1/;
						$perc = $perc."%";
					}
					unless ($ratio eq "$text{'2020'}") {
						$ratio =~ s/(\d+)\.\d.*/$1/;
						$ratio = $ratio.":1";
					}
					print "<TR ALIGN=CENTER>\n",
					  "<TD><SMALL><FONT $fontspec>",
					  "<INPUT TYPE=SUBMIT NAME=reviewone ",
					  "VALUE=\"$advertiser\">",
					  "</FONT></SMALL></TD>\n";
					$runtime = 0;
					if ($start) {
						($sec,$min,$hour,$mday,$mon,$year,
						  $wday,$yday,$isdst) = localtime($start+($HourOffset*3600));
						$year += 1900;
						print "<TD NOWRAP><SMALL><FONT $fontspec>$mday $months[$mon] $year</FONT></SMALL></TD>";
					}
					else { print "<TD></TD>"; }
					print "$expirationstatus";
					if ($ZoneColumn) {
						print "<TD NOWRAP><SMALL><FONT $fontspec>$zone</FONT></SMALL></TD>";
					}
					print "<TD><SMALL><FONT $fontspec>$weight</FONT></SMALL></TD>";
					print "<TD><SMALL><FONT $fontspec>",&commas($shown),"</FONT></SMALL></TD>";
					if ($expired || ($weight < 1)) {
						print "<TD><SMALL><FONT $fontspec>--</FONT></SMALL></TD>";
					}
					elsif ($average > 0) {
						print "<TD><SMALL><FONT $fontspec>",&commas($average),"</FONT></SMALL></TD>";
					}
					else {
						print "<TD><SMALL><FONT $fontspec>$text{'2020'}</FONT></SMALL></TD>";
					}
					print "<TD><SMALL><FONT $fontspec>",&commas($visits),"</FONT></SMALL></TD>";
					print "<TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD><TD><SMALL><FONT $fontspec>$ratio</FONT></SMALL></TD></TR>\n";
				}
				&ADVDBMClose;
				print "</TABLE></CENTER>\n";
				print "</FORM>\n";
				print "<P ALIGN=CENTER>";
				($sec,$min,$hour,$mday,$mon,$year,
				  $wday,$yday,$isdst) = localtime($time+($HourOffset*3600));
				if ($hour < 10) { $hour = "0".$hour; }
				if ($min < 10) { $min = "0".$min; }
				$year += 1900;
				print "(These figures are accurate as of ";
				print "$hour:$min on $mday $months[$mon] $year";
				if ($starttime) {
					($sec,$min,$hour,$mday,$mon,$year,
					  $wday,$yday,$isdst) = localtime($starttime+($HourOffset*3600));
					if ($hour < 10) { $hour = "0".$hour; }
					if ($min < 10) { $min = "0".$min; }
					$year += 1900;
					print ".<BR>Since $hour:$min on $mday $months[$mon] $year, ";
					print "there have been a total of <STRONG>",&commas($exposures);
					print "</STRONG> advert exposures";
					$time = $time - $starttime + 1;
					if ($time > 86400) {
						$average = int(($exposures/($time/86400))+.5);
						print ",<BR>for an average of <STRONG>",&commas($average);
						print "</STRONG> exposures per day";
					}
				}
				print ".)\n";
				unless ($AlwaysAvailable) {
					print "<BLOCKQUOTE><BIG><P><STRONG>CAUTION:</STRONG> You may see &quot;gaps&quot; ",
					  "in your rotation, where no banners are shown, since you currently have no ",
					  "banners &quot;guaranteed&quot; to always be available for display. You should ",
					  "always have at least one account set to never expire, with no display ratio ",
					  "or click-thru ratio, with a weight of 1, and with no assigned zones.";
					if ($DefaultBanner) {
						if ($DefaultAvailable) {
							print " You've assigned a &quot;default&quot; banner ",
							  "(&quot;$DefaultBanner&quot;), so the problem will be &quot;hidden&quot;; ",
							  "however, it's still best to make sure that you actually have banners ",
							  "properly available for display.";
						}
						else {
							print " In addition, your &quot;default&quot; banner assignment ",
							  "(&quot;$DefaultBanner&quot;) is invalid, as the assignment doesn't ",
							  "match the name of any account in your rotation."
						}
					}
					print "</BIG></BLOCKQUOTE>\n";
				}
			}
		}
		print "<P><HR>";
		&ADVLockClose (DBMLIST, "dbmlist.txt");
	}
	if ((-s "$adverts_dir/adnew.txt") && ($INPUT{'whichtype'} =~ /pending/)) {
		undef @newlines;
		open (COUNT, "<$adverts_dir/adnew.txt");
		@newlines = <COUNT>;
		close (COUNT);
		chomp (@newlines);
	}
	if (@newlines > 0) {
		print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
		print "The Following Accounts Await ";
		print "Administrative Approval:";
		print "</STRONG></BIG></BIG><CENTER>\n";
		@sortednewlines = sort (@newlines);
		print "<FORM METHOD=POST ACTION=$admin_cgi>",
		  "<INPUT TYPE=HIDDEN NAME=password ",
		  "VALUE=\"$INPUT{'password'}\">",
		  "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		foreach $newad (@sortednewlines) {
			next if (length($newad) < 1);
			print "<P><INPUT TYPE=SUBMIT NAME=reviewone ",
			  "VALUE=\"$newad\">\n";
		}
		print "</FORM></CENTER>\n<P><HR>";
	}
	if ((-s "$adverts_dir/groups.txt") && ($INPUT{'whichtype'} =~ /groups/)) {
		undef @grouplines;
		open (COUNT, "<$adverts_dir/groups.txt");
		@grouplines = <COUNT>;
		close (COUNT);
		chomp (@grouplines);
	}
	if (@grouplines > 0) {
		print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
		print "The Following Groups Have Been Defined:";
		print "</STRONG></BIG></BIG>\n";
		print "<FORM METHOD=POST ",
		  "ACTION=$admin_cgi>",
		  "<INPUT TYPE=HIDDEN NAME=password ",
		  "VALUE=$INPUT{'password'}>\n";
		print "<P><CENTER><TABLE CELLPADDING=3>\n";
		@sortedgroups = sort (@grouplines);
		foreach $group (@sortedgroups) {
			next if (length($group) < 1);
			open (DISPLAY, "<$adverts_dir/$group.grp");
			@members = <DISPLAY>;
			close (DISPLAY);
			chomp (@members);
			$grppassword = $members[0];
			print "<TR ALIGN=CENTER>",
			  "<TD><SMALL><FONT $fontspec>",
			  "<INPUT TYPE=SUBMIT NAME=editgroup ",
			  "VALUE=\"$group\">",
			  "</FONT></SMALL></TD>\n",
			  "<TD><SMALL><FONT $fontspec>";
			foreach $member (@members) {
				unless ($member eq $grppassword) {
					print " $member";
				}
			}
			print "</FONT></SMALL></TD></TR>\n";
		}
		print "</FORM></TABLE></CENTER>\n<P><HR>";
	}
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<CENTER><P><BIG><BIG><STRONG>The Following Options ",
	  "Are Available:</STRONG></BIG></BIG>\n",
	  "<INPUT TYPE=HIDDEN NAME=password ",
	  "VALUE=$INPUT{'password'}>\n",
	  "<P><STRONG>Add/Edit/Delete Account:</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=editad SIZE=25 ",
	  "VALUE=\"(enter account name)\"></FONT> ",
	  "<INPUT TYPE=SUBMIT NAME=edit ",
	  "VALUE=\"Edit Account\">\n",
	  "<P><STRONG>Add/Edit/Delete Group:</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=editgroup SIZE=25 ",
	  "VALUE=\"(enter group name)\"></FONT> ",
	  "<INPUT TYPE=SUBMIT VALUE=\"Edit Group\">\n",
	  "<P><STRONG>Rename Account:</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=oldname SIZE=25 ",
	  "VALUE=\"(current name)\"></FONT> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=newname SIZE=25 ",
	  "VALUE=\"(new name)\"></FONT> ",
	  "<INPUT TYPE=SUBMIT NAME=renameaccount ",
	  "VALUE=\"Rename Account\">\n",
	  "<P><STRONG>Change Admin Password:</STRONG> ",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=PASSWORD NAME=passad SIZE=10> ",
	  "<INPUT TYPE=PASSWORD NAME=passad2 SIZE=10></FONT> ",
	  "<INPUT TYPE=SUBMIT NAME=newpass ",
	  "VALUE=\"Change Password\">\n";
	if ($SecondaryDisplay) {
		print "<P><INPUT TYPE=SUBMIT NAME=cheatercheck ",
		  "VALUE=\"View &quot;Cheater Check&quot; Log\">\n";
	}
	if ($ADVLogIP) {
		print "<P><INPUT TYPE=SUBMIT NAME=masteriplog ",
		  "VALUE=\"View Master IP Access Log\">\n";
	}
	if ($LogAdminAccesses) {
		print "<P><INPUT TYPE=SUBMIT NAME=adminlog ",
		  "VALUE=\"View Admin Access Log\"> ",
		  "<INPUT TYPE=SUBMIT NAME=resetadminlog ",
		  "VALUE=\"Reset Admin Access Log\">\n";
	}
	print "<P><INPUT TYPE=SUBMIT NAME=resetcount ",
	  "VALUE=\"Reset Overall Total Exposures Count\">\n";
	if ($mailprog) {
		print "<P><INPUT TYPE=SUBMIT NAME=listemail ",
		  "VALUE=\"Send E-Mail to Account Holders\">\n";
	}
	else {
		print "<P><INPUT TYPE=SUBMIT NAME=listemail ",
		  "VALUE=\"List All Account Holder E-Mails\">\n";
	}
	print "<P><INPUT TYPE=SUBMIT NAME=expireemail ",
	  "VALUE=\"Select Automatic E-Mail Recipients\">\n";
	print "<P><INPUT TYPE=SUBMIT NAME=check_database ",
	  "VALUE=\"Check Database Integrity\"> ",
	  "<INPUT TYPE=SUBMIT NAME=rebuild ",
	  "VALUE=\"Rebuild Database\">\n",
	  "</CENTER></FORM>\n";
	&LinkBack;
	&Footer;
}

sub ListEmail {
	&ConfirmAdminPassword(1);
	$listtype = "active";
	open (LIST, "<$adverts_dir/adlist.txt");
	@advertlist = <LIST>;
	close (LIST);
	chomp (@advertlist);
	foreach $advertiser (@advertlist) { &constructlist; }
	$listtype = "pending";
	open (LIST, "<$adverts_dir/adnew.txt");
	@advertlist = <LIST>;
	close (LIST);
	chomp (@advertlist);
	foreach $advertiser (@advertlist) { &constructlist; }
	@sortedemails1 = sort (@emails1);
	@sortedemails2 = sort (@emails2);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
	print "Account Holder E-Mail Addresses:";
	print "</STRONG></BIG></BIG>\n";
	if ($mailprog) {
		print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		print "<P>Select the account holders to whom the e-mail should be sent:\n<P>";
		$lastemail = "";
		foreach $email (@sortedemails1) {
			if ($lastemail) { print "<BR>"; }
			print "$email\n";
			$lastemail = $email;
		}
		print "<P>Message Subject: <FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=messagesubject LENGTH=50></FONT>\n";
		print "<P>Message Text:\n";
		print "<BR><FONT FACE=\"Courier\"><TEXTAREA COLS=70 ROWS=10 NAME=messagetext WRAP=VIRTUAL></TEXTAREA></FONT>\n";
		print "<P><INPUT TYPE=SUBMIT VALUE=\"Send E-Mail\">\n";
		print "</FORM>";
	}
	else {
		print "<P>";
		$lastemail = "";
		foreach $email (@sortedemails1) {
			if ($lastemail) { print "<BR>"; }
			print "$email\n";
			$lastemail = $email;
		}
		print "<P><EM>You may, if you like, copy the following listing directly ";
		print "into the &quot;To:&quot; field in your e-mail program, to e-mail ";
		print "all account holders.</EM>\n<P>";
		$lastemail = "";
		foreach $email (@sortedemails2) {
			if ($email eq $lastemail) { next; }
			if ($lastemail) { print ", "; }
			print "$email";
			$lastemail = $email;
		}
		print "\n";
	}
	&LinkBack;
	&Footer;
}

sub ExpireEmail {
	&ConfirmAdminPassword(1);
	open (LIST, "<$adverts_dir/adlist.txt");
	@advertlist = <LIST>;
	close (LIST);
	chomp (@advertlist);
	open (LIST, "<$adverts_dir/adexpirelist.txt");
	@recipientlist = <LIST>;
	close (LIST);
	chomp (@recipientlist);
	$recipientlist = join(' ',@recipientlist);
	$recipientlist = " $recipientlist ";
	foreach $advertiser (@advertlist) { &constructlist; }
	@sortedemails1 = sort (@emails1);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>";
	print "Automatic E-Mail Recipients:";
	print "</STRONG></BIG></BIG>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	print "<P>If you're using the separate &quot;ads_expire.pl&quot; script, you can send automatic ",
	  "e-mail notices to your account holders, informing them of the progress of their accounts. ",
	  "Select in the list below, which of your account holders should receive such notices. ",
	  "Note that this page <EM>sends</EM> nothing; it simply determines who will receive the notices ",
	  "sent by the &quot;ads_expire.pl&quot; script, when it is run.\n<P>";
	$lastemail = "";
	foreach $email (@sortedemails1) {
		if ($lastemail) { print "<BR>"; }
		print "$email\n";
		$lastemail = $email;
	}
	print "<P><INPUT TYPE=SUBMIT NAME=\"expireemailupdate\" VALUE=\"Update Recipient List\">\n";
	print "</FORM>";
	&LinkBack;
	&Footer;
}

sub constructlist {
	$subdir = substr($advertiser,0,1);
	$subdir .= "/$advertiser";
	open (DISPLAY, "<$adverts_dir/$subdir/$advertiser.dat");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	unless ($lines[1]) { $lines[1] = "Account Holder"; }
	if ($lines[2]) { $email = "$lines[2]"; }
	else { $email = ""; }
	$listing = "<!--$listtype $advertiser-->";
	if ($mailprog || $recipientlist) {
		$listing .= "<INPUT TYPE=CHECKBOX NAME=\"emails\" VALUE=\"";
		if ($recipientlist) {
			$listing .= "$advertiser\"";
			if ($recipientlist =~ / $advertiser /) { $listing .= " CHECKED"; }
		}
		else {
			$listing .= "$email\"";
			if ($email) { $listing .= " CHECKED"; }
		}
		$listing .= "> ";
	}
	unless ($recipientlist) { $listing .= "($listtype) "; }
	$listing .= "<STRONG>$advertiser</STRONG> - $lines[1] (";
	if ($email) { $listing .= "<A HREF=\"mailto:$lines[2]\">$lines[2]</A>)"; }
	else { $listing .= "E-Mail Unknown)"; }
	push (@emails1,"$listing");
	unless ($lines[2]) { next; }
	push (@emails2,"&quot;$lines[1]&quot; &lt;$lines[2]&gt;");
}

sub emailmembers {
	&ConfirmAdminPassword(1);
	@bcclist = split(/\s/,$INPUT{'emails'});
	foreach $bcc (sort @bcclist) {
		if ($bcc eq $lastbcc) { next; }
		if (length($bcc)<3) { next; }
		push (@bcc,$bcc);
		$lastbcc = $bcc;
	}
	&SendMail($email_address,"groupmail");
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>E-Mail Sent</STRONG></BIG></BIG>\n",
	  "<P ALIGN=CENTER>The message has been sent.\n";
	&LinkBack;
	&Footer;
}

sub ExpireEmailUpdate {
	&ConfirmAdminPassword(1);
	@recipientlist = split(/\s/,$INPUT{'emails'});
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>Recipient List Updated</STRONG></BIG></BIG>\n",
	  "<P>The following accounts will receive update notices ",
	  "when the &quot;ads_expire.pl&quot; script is run:\n<P>";
	open (LIST, ">$adverts_dir/adexpirelist.txt");
	$lastrecipient = "";
	foreach $recipient (sort @recipientlist) {
		if ($lastrecipient) { print "<BR>"; }
		print "$recipient\n";
		print LIST "$recipient\n";
		$lastrecipient = $recipient;
	}
	close (LIST);
	&LinkBack;
	&Footer;
}

sub reviewgroup {
	$groupstatus = "$AccountName";
	unless (-s "$adverts_dir/$AccountName.grp") {
		if ($AllowUserEdit && $INPUT{'newuser'}) {
			&UserEdit;
		}
		&Header("$text{'9000'}","$text{'9050'}");
		print "<P ALIGN=CENTER>$text{'9051'} ";
		print "<STRONG>&quot;$AccountName&quot;</STRONG> $text{'9052'}\n";
		&Footer;
	}
	open (DISPLAY, "<$adverts_dir/$AccountName.grp");
	@adverts = <DISPLAY>;
	close (DISPLAY);
	chomp (@adverts);
	unless ($cryptword) {
		unless ($INPUT{'password'} eq $adverts[0]) {
			&ConfirmAdminPassword(2);
		}
	}
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><STRONG>$text{'2000'} ";
	print "<EM>$AccountName</EM> $text{'2001'}</STRONG></BIG>\n";
	foreach $advert (@adverts) {
		$name = $advert;
		$subdir = substr($advert,0,1);
		$subdir .= "/$advert";
		next unless (-s "$adverts_dir/$subdir/$advert.txt");
		open (DISPLAY, "<$adverts_dir/$subdir/$advert.dat");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		($pass,$username,$email,$comments) = @lines;
		open (DISPLAY, "<$adverts_dir/$subdir/$advert.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		($max,$shown,$visits,$url,$image,$height,$width,
		  $alt,$nada,$text,$start,$weight,$zone,
		  $border,$target,$raw,$displayratio,$nada,$nada,
		  $displayzone,$clicksfrom) = @lines;
		($max,$maxtype) = split(/\|/, $max);
		unless ($maxtype) { $maxtype = "E"; }
		($text,$texttype) = split(/\|/, $text);
		unless ($texttype) { $texttype = "B"; }
		($displayratio,$displaycount) = split(/\|/, $displayratio);
		($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
		if ($maxtype eq "N") { $max = 0; }
		if ((($maxtype eq "E") || ($maxtype eq "N")) && ($displayratio > 0)) {
			$max = $max+int($displaycount/$displayratio);
		}
		if ((($maxtype eq "E") || ($maxtype eq "N")) && ($clicksratio > 0)) {
			$max = $max+($clicksfrom*$clicksratio);
		}
		if ($max == 0) { $max = "0"; }
		$TotalShown += $shown;
		$TotalVisits += $visits;
		print "<P><HR>\n";
		&reviewadvert;
	}
	print "<P><HR><P ALIGN=CENTER><BIG><BIG><STRONG>$text{'2002'}",
	  "</STRONG></BIG></BIG>\n",
	  "<P><CENTER><TABLE CELLPADDING=3>\n",
	  "<TR ALIGN=CENTER VALIGN=BOTTOM>",
	  "<TD><SMALL><FONT $fontspec><EM>$text{'2015'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
	  "<TD><SMALL><FONT $fontspec><EM>$text{'2017'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
	  "<TD><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
	  "<TD><SMALL><FONT $fontspec><EM>$text{'2019'}</EM><BR><HR NOSHADE></FONT></SMALL></TD></TR>\n";
	if (($TotalShown == 0) || ($TotalVisits == 0)) {
		$perc = "$text{'2020'}";
		$ratio = "$text{'2020'}";
	}
	else {
		$perc = ((100*($TotalVisits/$TotalShown))+.05001);
		$ratio = (($TotalShown/$TotalVisits)+.5001);
	}
	unless ($perc eq "$text{'2020'}") {
		$perc =~ s/(\d+\.\d).*/$1/;
		$perc = $perc."%";
	}
	unless ($ratio eq "$text{'2020'}") {
		$ratio =~ s/(\d+)\.\d.*/$1/;
		$ratio = $ratio.":1";
	}
	print "<TR ALIGN=CENTER>";
	print "<TD><SMALL><FONT $fontspec>",&commas($TotalShown),"</FONT></SMALL></TD>";
	print "<TD><SMALL><FONT $fontspec>",&commas($TotalVisits),"</FONT></SMALL></TD>";
	print "<TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD><TD><SMALL><FONT $fontspec>$ratio</FONT></SMALL></TD></TR>\n";
	print "</TABLE></CENTER>\n";
	&Footer("Group: $AccountName");
}

sub reviewone {
	unless ($INPUT{'password'}) {
		&Header("$text{'9000'}","$text{'9020'}");
		print "<P ALIGN=CENTER>$text{'9021'}\n";
		&Footer;
	}
	$AccountName = $INPUT{'reviewone'};
	&CheckName;
	if ($INPUT{'admincheck'}) {
		&ConfirmAdminPassword(2);
	}
	unless (-s "$adverts_dir/$subdir/$AccountName.txt") {
		&reviewgroup;
	}
	$name = $AccountName;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	($pass,$username,$email,$comments) = @lines;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	($max,$shown,$visits,$url,$image,$height,$width,
	  $alt,$nada,$text,$start,$weight,$zone,
	  $border,$target,$raw,$displayratio,$nada,$nada,
	  $displayzone,$clicksfrom) = @lines;
	($max,$maxtype) = split(/\|/, $max);
	unless ($maxtype) { $maxtype = "E"; }
	($text,$texttype) = split(/\|/, $text);
	unless ($texttype) { $texttype = "B"; }
	($displayratio,$displaycount) = split(/\|/, $displayratio);
	($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
	if ($maxtype eq "N") { $max = 0; }
	if ((($maxtype eq "E") || ($maxtype eq "N")) && ($displayratio > 0)) {
		$max = $max+int($displaycount/$displayratio);
	}
	if ((($maxtype eq "E") || ($maxtype eq "N")) && ($clicksratio > 0)) {
		$max = $max+($clicksfrom*$clicksratio);
	}
	if ($max == 0) { $max = "0"; }
	unless ($cryptword) {
		unless ($INPUT{'password'} eq $pass) {
			&ConfirmAdminPassword(2);
		}
	}
	&Header("$text{'1000'}","$text{'1001'}");
	&reviewadvert;
	if ($cryptword) {
		if ($comments) { print "<P ALIGN=CENTER>Comments: $comments\n"; }
		&LinkBack;
	}
	else {
		print "<P><CENTER>\n";
		print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
		print "<INPUT TYPE=SUBMIT VALUE=\"$text{'2050'}\">\n";
		print "</FORM></CENTER>\n";
	}
	&Footer("Account: $AccountName");
}

sub reviewadvert {
	$expired = 0;
	print "<CENTER><P><BIG><BIG><STRONG>$text{'2005'} ";
	print "<EM>$name</EM> $text{'2006'}</STRONG></BIG></BIG>\n";
	open (COUNT, "$adverts_dir/adnew.txt");
	@lines = <COUNT>;
	close (COUNT);
	chomp (@lines);
	foreach $line (@lines) {
		if ($line eq $name) {
			print "<P>$text{'2007'}";
			last;
		}
	}
	print "</CENTER>\n";
	print "<P><CENTER><TABLE CELLPADDING=3>\n";
	print "<TR ALIGN=CENTER VALIGN=BOTTOM>";
	print "<TD><SMALL><FONT $fontspec><EM>$text{'2011'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	if ($displayratio || $displaycount || $clicksratio || $clicksfrom) {
		print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2040'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2041'}";
			print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec><EM>$text{'2019'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		}
		print "</TR>\n";
		print "<TR ALIGN=CENTER>";
		if ($start) {
			($sec,$min,$hour,$mday,$mon,$year,
			  $wday,$yday,$isdst) = localtime($start+($HourOffset*3600));
			$year += 1900;
			print "<TD NOWRAP><SMALL><FONT $fontspec>$mday $months[$mon] $year</FONT></SMALL></TD>";
			$runtime = $time - $start + 1;
		}
		else { print "<TD></TD>"; }
		if (($displaycount == 0) || ($clicksfrom == 0)) {
			$foreignperc = "$text{'2020'}";
			$foreignratio = "$text{'2020'}";
		}
		else {
			$foreignperc = ((100*($clicksfrom/$displaycount))+.05001);
			$foreignratio = (($displaycount/$clicksfrom)+.5001);
		}
		unless ($foreignperc eq "$text{'2020'}") {
			$foreignperc =~ s/(\d+\.\d).*/$1/;
			$foreignperc = $foreignperc."%";
		}
		unless ($foreignratio eq "$text{'2020'}") {
			$foreignratio =~ s/(\d+)\.\d.*/$1/;
			$foreignratio = $foreignratio.":1";
		}
		print "<TD><SMALL><FONT $fontspec>",&commas($displaycount),"</FONT></SMALL></TD>";
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<TD><SMALL><FONT $fontspec>",&commas($clicksfrom),"</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>$foreignperc</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>$foreignratio</FONT></SMALL></TD>";
		}
		print "</TR>\n";
		print "</TABLE>\n";
		print "<P><TABLE CELLPADDING=3>\n";
		print "<TR ALIGN=CENTER VALIGN=BOTTOM>";
	}
	else {
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2012'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	}
	if ($image || $raw || $shown || $visits) {
		print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2030'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2016'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2031'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2019'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	}
	print "</TR>\n";
	if (($shown == 0) || ($visits == 0)) {
		$perc = "$text{'2020'}";
		$ratio = "$text{'2020'}";
	}
	else {
		$perc = ((100*($visits/$shown))+.05001);
		$ratio = (($shown/$visits)+.5001);
	}
	unless ($perc eq "$text{'2020'}") {
		$perc =~ s/(\d+\.\d).*/$1/;
		$perc = $perc."%";
	}
	unless ($ratio eq "$text{'2020'}") {
		$ratio =~ s/(\d+)\.\d.*/$1/;
		$ratio = $ratio.":1";
	}
	print "<TR ALIGN=CENTER>";
	$runtime = 0;
	if ($start) {
		($sec,$min,$hour,$mday,$mon,$year,
		  $wday,$yday,$isdst) = localtime($start+($HourOffset*3600));
		$year += 1900;
		unless ($displayratio || $displaycount || $clicksratio || $clicksfrom) {
			print "<TD NOWRAP><SMALL><FONT $fontspec>$mday $months[$mon] $year</FONT></SMALL></TD>";
		}
		$runtime = $time - $start + 1;
	}
	else {
		unless ($displayratio || $displaycount || $clicksratio || $clicksfrom) {
			print "<TD></TD>";
		}
	}
	$average = 0;
	if (($weight > 0) && ($runtime > 86400)) {
		&GetAverage;
	}
	unless ($displayratio || $clicksratio) {
		if ($maxtype eq "D") {
			($sec,$min,$hour,$mday,$mon,$year,
			  $wday,$yday,$isdst) = localtime($max+($HourOffset*3600));
			$year += 1900;
			print "<TD NOWRAP><SMALL><FONT $fontspec>$mday $months[$mon] $year";
			unless ($max > $time) {
				$expired = 1;
				print "<BR><EM>$text{'2100'}</EM>";
			}
			print "</FONT></SMALL></TD>";
		}
		elsif ($maxtype eq "N") {
			print "<TD NOWRAP><SMALL><FONT $fontspec><EM>$text{'2101'}</EM></FONT></SMALL></TD>";
		}
		else {
			print "<TD NOWRAP><SMALL><FONT $fontspec>",&commas($max);
			if ($maxtype eq "C") {
				print " $text{'2110'}";
				if (($average == 0) || ($shown == 0) || ($visits == 0)) {
					print "<BR><EM>$text{'2102'}</EM>";
				}
				elsif ($max > $visits) {
					$daystogo = (($max-$visits)/($average*($visits/$shown)));
					$calculatedend = $time+($daystogo*86400);
					($sec,$min,$hour,$mday,$mon,$year,
					  $wday,$yday,$isdst) = localtime($calculatedend+($HourOffset*3600));
					$year += 1900;
					print "<BR><EM>(~ $mday $months[$mon] $year)</EM>";
				}
				else {
					$expired = 1;
					print "<BR><EM>$text{'2100'}</EM>";
				}
			}
			else {
				print " $text{'2111'}";
				if ($average == 0) {
					print "<BR><EM>$text{'2102'}</EM>";
				}
				elsif ($max > $shown) {
					$daystogo = (($max-$shown)/$average);
					$calculatedend = $time+($daystogo*86400);
					($sec,$min,$hour,$mday,$mon,$year,
					  $wday,$yday,$isdst) = localtime($calculatedend+($HourOffset*3600));
					$year += 1900;
					print "<BR><EM>(~ $mday $months[$mon] $year)</EM>";
				}
				else {
					$expired = 1;
					print "<BR><EM>$text{'2100'}</EM>";
				}
			}
			print "</FONT></SMALL></TD>";
		}
	}
	if ($image || $raw || $shown || $visits) {
		print "<TD><SMALL><FONT $fontspec>",&commas($shown),"</FONT></SMALL></TD>";
		if ($expired || ($weight < 1)) {
			print "<TD><SMALL><FONT $fontspec>--</FONT></SMALL></TD>";
		}
		elsif ($average > 0) {
			print "<TD><SMALL><FONT $fontspec>",&commas($average),"</FONT></SMALL></TD>";
		}
		else {
			print "<TD><SMALL><FONT $fontspec>$text{'2020'}</FONT></SMALL></TD>";
		}
		print "<TD><SMALL><FONT $fontspec>",&commas($visits),"</FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD><TD><SMALL><FONT $fontspec>$ratio</FONT></SMALL></TD>";
	}
	print "</TR></TABLE></CENTER>\n";
	unless ($image || $raw) {
		print "<P>$text{'2120'}\n";
	}
	if ($displayratio || $clicksratio) {
		unless ($NoBanners) {
			if ($displaycount<1) { $displaycount = "0"; }
			print "<P>$text{'2121'} <STRONG>";
			print &commas($displaycount),"</STRONG> $text{'2122'}";
			if ($clicksratio || $cryptword || $ShowClicksFrom) {
				print "$text{'2123'} <STRONG>";
				print &commas($clicksfrom),"</STRONG> $text{'2124'}";
			}
			print "$text{'2125'} ";
			$earnings = 0;
			if ($displayratio > 0) { $earnings = int($displaycount/$displayratio); }
			if ($clicksratio > 0) { $earnings += ($clicksfrom*$clicksratio); }
			print "$text{'2126'} <STRONG>";
			print &commas($earnings),"</STRONG> $text{'2127'}";
			if ($displayratio > 0) {
				print " $text{'2128'} ";
				print "<STRONG>$displayratio</STRONG> $text{'2129'}";
			}
			if (($displayratio > 0) && ($clicksratio > 0)) {
				print "$text{'2130'}";
			}
			if ($clicksratio > 0) {
				print " <STRONG>$clicksratio</STRONG> $text{'2131'}";
			}
			print "$text{'2132'}";
			unless ($max == $earnings) {
				print " $text{'2133'} <STRONG>";
				print &commas($max-$earnings);
				print "</STRONG> $text{'2134'}";
			}
			print "\n";
		}
		print "<P>$text{'2135'}\n";
		$HTMLCode = "";
		if ($ExchangeName) {
			$HTMLCode = "&lt;!-- Begin $ExchangeName Code --&gt;\n<BR>";
		}
		unless ($ExchangeBorder) { $ExchangeBorder = "0"; }
		$HTMLCode .= "&lt;P&gt;&lt;CENTER&gt;";
		if ($ExchangeLogo
		  && (($ExchangeLogoPosition =~ /^t/i)
		  || ($ExchangeLogoPosition =~ /^l/i)
		  || ($ExchangeLogoPosition =~ /^1/i)
		  || !($ExchangeLogoPosition))) {
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;A HREF=&quot;$ExchangeURL&quot;";
				$HTMLCode .= " $DefaultLinkAttribute&gt;";
			}
			$HTMLCode .= "&lt;IMG SRC=&quot;$ExchangeLogo&quot;";
			if ($ExchangeLogoHeight && $ExchangeLogoWidth) {
				$HTMLCode .= " WIDTH=$ExchangeLogoWidth";
				$HTMLCode .= " HEIGHT=$ExchangeLogoHeight";
			}
			if ($ExchangeName) {
				$HTMLCode .= " ALT=&quot;$ExchangeName&quot;";
			}
			$HTMLCode .= " BORDER=$ExchangeBorder&gt;";
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;/A&gt;";
			}
			if ($ExchangeLogoPosition =~ /^t/i) {
				$HTMLCode .= "&lt;BR&gt;";
			}
		}
		if ($IFRAMEexchange) {
			$HTMLCode .= "&lt;IFRAME SRC=&quot;$nonssi_cgi?";
			$HTMLCode .= "iframe;member=$name";
			if ($displayzone) {
				$HTMLCode .= ";zone=$displayzone";
			}
			$HTMLCode .= "&quot;";
			$HTMLCode .= " MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0";
			$HTMLCode .= " FRAMEBORDER=0 SCROLLING=NO";
			if ($ExchangeBannerHeight && $ExchangeBannerWidth) {
				$IFRAMEWidth = $ExchangeBannerWidth+($DefaultBorder*2);
				$IFRAMEHeight = $ExchangeBannerHeight+($DefaultBorder*2);
				$HTMLCode .= " WIDTH=$IFRAMEWidth";
				$HTMLCode .= " HEIGHT=$IFRAMEHeight";
			}
			$HTMLCode .= "&gt;";
		}
		if ($JavaScriptExchange) {
			$HTMLCode .= "&lt;SCRIPT LANGUAGE=&quot;JavaScript&quot; ";
			$HTMLCode .= "SRC=&quot;$nonssi_cgi?";
			$HTMLCode .= "jscript;member=$name";
			if ($displayzone) {
				$HTMLCode .= ";zone=$displayzone";
			}
			$HTMLCode .= "&quot;&gt;";
			$HTMLCode .= "&lt;/SCRIPT&gt;";
			$HTMLCode .= "&lt;NOSCRIPT&gt;";
		}
		$HTMLCode .= "&lt;A HREF=&quot;$nonssi_cgi?";
		$HTMLCode .= "member=$name;banner=NonSSI;page=01";
		if ($displayzone) {
			$HTMLCode .= ";zone=$displayzone";
		}
		$HTMLCode .= "&quot;";
		$HTMLCode .= " $DefaultLinkAttribute&gt;";
		$HTMLCode .= "&lt;IMG SRC=&quot;$nonssi_cgi?";
		$HTMLCode .= "member=$name;page=01";
		if ($displayzone) {
			$HTMLCode .= ";zone=$displayzone";
		}
		$HTMLCode .= "&quot;";
		if ($ExchangeBannerHeight && $ExchangeBannerWidth) {
			$HTMLCode .= " WIDTH=$ExchangeBannerWidth";
			$HTMLCode .= " HEIGHT=$ExchangeBannerHeight";
		}
		if ($ExchangeName) {
			$HTMLCode .= " ALT=&quot;$ExchangeName&quot;";
		}
		$HTMLCode .= " BORDER=$DefaultBorder&gt;&lt;/A&gt;";
		if ($JavaScriptExchange) {
			$HTMLCode .= "&lt;/NOSCRIPT&gt;";
		}
		if ($IFRAMEexchange) {
			$HTMLCode .= "&lt;/IFRAME&gt;";
		}
		if ($ExchangeLogo
		  && (($ExchangeLogoPosition =~ /^b/i)
		  || ($ExchangeLogoPosition =~ /^r/i))) {
			if ($ExchangeLogoPosition =~ /^b/i) {
				$HTMLCode .= "&lt;BR&gt;";
			}
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;A HREF=&quot;$ExchangeURL&quot;";
				$HTMLCode .= " $DefaultLinkAttribute&gt;";
			}
			$HTMLCode .= "&lt;IMG SRC=&quot;$ExchangeLogo&quot;";
			if ($ExchangeLogoHeight && $ExchangeLogoWidth) {
				$HTMLCode .= " WIDTH=$ExchangeLogoWidth";
				$HTMLCode .= " HEIGHT=$ExchangeLogoHeight";
			}
			if ($ExchangeName) {
				$HTMLCode .= " ALT=&quot;$ExchangeName&quot;";
			}
			$HTMLCode .= " BORDER=$ExchangeBorder&gt;";
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;/A&gt;";
			}
		}
		if ($ExchangeName && !($ExchangeLogo)) {
			$HTMLCode .= "&lt;BR&gt;&lt;SMALL&gt;";
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;A HREF=&quot;$ExchangeURL&quot;";
				$HTMLCode .= " $DefaultLinkAttribute&gt;";
			}
			$HTMLCode .= "$ExchangeName";
			if ($ExchangeURL) {
				$HTMLCode .= "&lt;/A&gt;";
			}
			$HTMLCode .= "&lt;/SMALL&gt;";
		}
		$HTMLCode .= "&lt;/CENTER&gt;";
		if ($ExchangeName) {
			$HTMLCode .= "\n<BR>&lt;!-- End $ExchangeName Code --&gt;\n";
		}
		print "<P><CENTER><FORM><FONT FACE=\"Courier\">";
		print "<TEXTAREA COLS=70 ROWS=10 WRAP=VIRTUAL>$HTMLCode</TEXTAREA>";
		print "</FONT></FORM></CENTER>\n";
		print "<P>$text{'2150'}\n";
	}
	print "<CENTER>";
	if (($AllowUserEdit || $cryptword) && !($groupstatus)) {
		print "<P><TABLE><TR ALIGN=CENTER>\n";
		print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		print "<INPUT TYPE=HIDDEN NAME=reviewone ";
		print "VALUE=$name>\n<TD>";
		if ($cryptword) {
			print "<INPUT TYPE=SUBMIT NAME=edit ";
		}
		else {
			print "<INPUT TYPE=SUBMIT NAME=UserEdit ";
		}
		print "VALUE=\"$text{'2200'}\">";
		print "</TD></FORM>\n";
		print "<FORM METHOD=POST ACTION=$admin_cgi>\n",
		  "<INPUT TYPE=HIDDEN NAME=password ",
		  "VALUE=$INPUT{'password'}>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		print "<TD><INPUT TYPE=SUBMIT NAME=del ";
		print "VALUE=\"$text{'2201'}\"></TD>";
		print "<INPUT TYPE=HIDDEN NAME=delad ";
		print "VALUE=\"$AccountName\">\n";
		print "</FORM>\n";
		print "</TR></TABLE>\n";
	}
	print "<P><TABLE><TR ALIGN=CENTER>\n";
	if ($LogByZone) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($groupstatus) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$groupstatus\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$name>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=logbyzone ";
		print "VALUE=\"$text{'2202'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($groupstatus) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$groupstatus\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$name>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($clicksratio || $cryptword || $ShowClicksFrom) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=dailystats ";
	print "VALUE=\"$text{'2203'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($groupstatus) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$groupstatus\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$name>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($clicksratio || $cryptword || $ShowClicksFrom) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=monthlystats ";
	print "VALUE=\"$text{'2204'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($ADVLogIP) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($groupstatus) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$groupstatus\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$name>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=iplog ";
		print "VALUE=\"$text{'2205'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "</TR></TABLE></CENTER>\n";
	if ($email && $INPUT{'welcomeletter'}) {
		open (WELCOME, "<$adverts_dir/welcome.txt");
		$body = "";
		while (defined($line = <WELCOME>)) {
			$body .= $line;
		}
		close (WELCOME);
		$HTMLCode =~ s/<BR>//g;
		$HTMLCode =~ s/&lt;/</g;
		$HTMLCode =~ s/&gt;/>/g;
		$HTMLCode =~ s/&quot;/"/g;
		$body =~ s/<--UserID-->/$name/g;
		$body =~ s/<--Password-->/$pass/g;
		$body =~ s/<--HTMLCode-->/$HTMLCode/g;
		&SendMail($email,"welcome");
	}
	&ShowAdvert;
}

sub GetAverage {
	$subdir = substr($name,0,1);
	$subdir .= "/$name";
	open (DISPLAY, "<$adverts_dir/$subdir/$name.log");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	@reverselines = reverse (@lines);
	$avexposures = 0;
	$linecount = 0;
	foreach $line (@reverselines) {
		next if (length($line)<10);
		($acc,$type) = ($line =~
		  /^(\d\d\d\d\d\d\d\d\d\d) \d\d \d\d \d\d\d\d (\w)$/);
		next unless ($type eq "E");
		$linecount++;
		next if ($linecount < 2);
		last if ($linecount > 8);
		$avexposures += int($acc);
	}
	unless ($linecount > 8) {
		$avexposures -= int($acc);
	}
	if (($avexposures < 1) || ($linecount < 3)) {
		return;
	}
	$average = int(($avexposures/($linecount-2))+.5);
}

sub logbyzone {
	$AccountName = $INPUT{'advert'};
	&CheckName;
	&ConfirmUserPassword;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>$text{'2210'} ";
	print "<EM>$AccountName</EM> $text{'2211'}:</STRONG></BIG></BIG>\n";
	if (@lines > 21) {
		foreach $key (21..@lines-1) {
			if ($lines[$key] =~ /(\S+ \S+) (\S) (\d+)/) {
				$zone = $1;
				$type = $2;
				$count = $3;
				if ($type eq "E") { $exposures{$zone} += $count; }
				if ($type eq "C") {
					$clicks{$zone} += $count;
					unless ($exposures{$zone}) { $exposures{$zone} = "0"; }
				}
			}
		}
		print "<P><CENTER><TABLE CELLPADDING=3>\n";
		print "<TR ALIGN=CENTER VALIGN=BOTTOM>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2212'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2213'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2214'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2215'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD><SMALL><FONT $fontspec><EM>$text{'2019'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "</TR>\n";
		foreach $key (sort (keys %exposures)) {
			($zone,$member) = split(/\s/,$key);
			unless ($clicks{$key}) { $clicks{$key} = "0"; }
			print "<TR ALIGN=CENTER>";
			if (($exposures{$key} == 0) || ($clicks{$key} == 0)) {
				$perc = "$text{'2020'}";
				$ratio = "$text{'2020'}";
			}
			else {
				$perc = ((100*($clicks{$key}/$exposures{$key}))+.05001);
				$ratio = (($exposures{$key}/$clicks{$key})+.5001);
			}
			unless ($perc eq "$text{'2020'}") {
				$perc =~ s/(\d+\.\d).*/$1/;
				$perc = $perc."%";
			}
			unless ($ratio eq "$text{'2020'}") {
				$ratio =~ s/(\d+)\.\d.*/$1/;
				$ratio = $ratio.":1";
			}
			print "<TD ALIGN=LEFT><SMALL><FONT $fontspec>$zone</FONT></SMALL></TD>";
			print "<TD ALIGN=LEFT><SMALL><FONT $fontspec>$member</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>",&commas($exposures{$key}),"</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>",&commas($clicks{$key}),"</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD>";
			print "<TD><SMALL><FONT $fontspec>$ratio</FONT></SMALL></TD>";
			print "</TR>\n";
		}
		print "</TABLE>\n";
	}
	print "<P><CENTER><TABLE><TR ALIGN=CENTER>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=reviewone VALUE=";
	if ($INPUT{'groupstatus'}) {
		print "\"$INPUT{'groupstatus'}\"";
	}
	else {
		print "$AccountName";
	}
	print ">\n";
	print "<INPUT TYPE=SUBMIT ";
	print "VALUE=\"$text{'2206'}\"> ";
	print "</FONT></SMALL></TD></FORM>\n";
	print "<FORM METHOD=POST ";
	print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert ";
	print "VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=dailystats ";
	print "VALUE=\"$text{'2203'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=monthlystats ";
	print "VALUE=\"$text{'2204'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($ADVLogIP) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert ";
		print "VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($INPUT{'showclicksfrom'}) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=iplog ";
		print "VALUE=\"$text{'2205'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "</TR></TABLE></CENTER>\n";
	&Footer;
}

sub dailystats {
	$AccountName = $INPUT{'advert'};
	&CheckName;
	&ConfirmUserPassword;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.log") || &Error_NoStats;
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	foreach $line (@lines) {
		next if (length($line) < 10);
		($acc,$logstring) = ($line =~
		  /^(\d\d\d\d\d\d\d\d\d\d) (\d\d \d\d \d\d\d\d \w)$/);
		$accesses{$logstring} = int($acc);
		($mday,$mon,$year,$type) = ($logstring =~
			  /^(\d+) (\d+) (\d+) (\w)/);
		if ($type eq "E") {
			$beingshown = 1;
			$TotalE += int($acc);
			if (int($acc) > $MaxE) { $MaxE = int($acc); }
		}
		elsif ($type eq "C") {
			$beingshown = 1;
			$TotalC += int($acc);
			if (int($acc) > $MaxC) { $MaxC = int($acc); }
		}
		elsif ($type eq "S") {
			$bannex = 1;
			$TotalS += int($acc);
			if (int($acc) > $MaxS) { $MaxS = int($acc); }
		}
		elsif ($type eq "X") {
			$bannex = 1;
			$TotalX += int($acc);
			if (int($acc) > $MaxX) { $MaxX = int($acc); }
		}
		else { next; }
		unless ($startday) {
			&date_to_count(int($mon),int($mday),($year-1900));
			$startday = $perp_days;
		}
	}
	&date_to_count(int($mon),int($mday),($year-1900));
	$endday = $perp_days;
	if ((($endday-$startday) > 34) && !($INPUT{'FullDailyList'})) {
		$startday = $endday-34;
		$ShortenedList = 1;
		$TotalE = $MaxE = 0;
		$TotalC = $MaxC = 0;
		$TotalS = $MaxS = 0;
		$TotalX = $MaxX = 0;
		foreach $daycount ($startday..$endday) {
			&count_to_date($daycount);
			if ($perp_mon < 10) { $perp_mon = "0$perp_mon"; }
			if ($perp_day < 10) { $perp_day = "0$perp_day"; }
			$perp_year = $perp_year + 1900;
			$exposures = "$perp_day $perp_mon $perp_year E";
			$clicks = "$perp_day $perp_mon $perp_year C";
			$banners = "$perp_day $perp_mon $perp_year S";
			$clicksfrom = "$perp_day $perp_mon $perp_year X";
			$TotalE += $accesses{$exposures};
			$TotalC += $accesses{$clicks};
			$TotalS += $accesses{$banners};
			$TotalX += $accesses{$clicksfrom};
			if ($accesses{$exposures} > $MaxE) { $MaxE = $accesses{$exposures}; }
			if ($accesses{$clicks} > $MaxC) { $MaxC = $accesses{$clicks}; }
			if ($accesses{$banners} > $MaxS) { $MaxS = $accesses{$banners}; }
			if ($accesses{$clicksfrom} > $MaxX) { $MaxX = $accesses{$clicksfrom}; }
		}
	}
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>$text{'2220'} ";
	print "<EM>$AccountName</EM> $text{'2221'}";
	if ($ShortenedList) {
		print "<BR>$text{'2222'}";
	}
	elsif ($INPUT{'FullDailyList'}) {
		print "<BR>$text{'2223'}";
	}
	print ":</STRONG></BIG></BIG>\n";
	print "<P><CENTER><TABLE CELLPADDING=3>\n";
	print "<TR ALIGN=CENTER VALIGN=BOTTOM>";
	print "<TD><SMALL><FONT $fontspec><EM>$text{'2224'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	if ($bannex) {
		print "<TD NOWRAP>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2040'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		$ColCountA = 2;
		if ($INPUT{'showclicksfrom'}) {
			print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2041'}";
			print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
			print "<TD COLSPAN=2><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
			$ColCountA += 6;
		}
	}
	if ($beingshown) {
		print "<TD NOWRAP>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2030'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2031'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD COLSPAN=2><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		$ColCountB = 8;
	}
	$daycounter = (($endday-$startday)+1);
	if ($TotalE) { $MultE = ((49.5/($MaxE/$TotalE))/$daycounter); }
	if ($TotalC) { $MultC = ((49.5/($MaxC/$TotalC))/$daycounter); }
	if ($TotalS) { $MultS = ((49.5/($MaxS/$TotalS))/$daycounter); }
	if ($TotalX) { $MultX = ((49.5/($MaxX/$TotalX))/$daycounter); }
	foreach $daycount ($startday..$endday) {
		print "<TR ALIGN=CENTER>";
		if (($daycount > $startday)
		  && ($daycount-(int($daycount/7)*7)==3)) {
			print "<TD><HR NOSHADE></TD>";
			if ($ColCountA) {
				print "<TD>&nbsp;</TD>";
				print "<TD COLSPAN=$ColCountA><HR NOSHADE></TD>";
			}
			if ($ColCountB) {
				print "<TD>&nbsp;</TD>";
				print "<TD COLSPAN=$ColCountB><HR NOSHADE></TD>";
			}
			print "</TR>\n";
			print "<TR ALIGN=CENTER>";
		}
		&count_to_date($daycount);
		if ($perp_mon < 10) { $perp_mon = "0$perp_mon"; }
		if ($perp_day < 10) { $perp_day = "0$perp_day"; }
		$perp_year = $perp_year + 1900;
		print "<TD NOWRAP><SMALL><FONT $fontspec>$perp_day $months[$perp_mon-1] $perp_year</FONT></SMALL></TD>";
		$banners = "$perp_day $perp_mon $perp_year S";
		$clicksfrom = "$perp_day $perp_mon $perp_year X";
		$exposures = "$perp_day $perp_mon $perp_year E";
		$clicks = "$perp_day $perp_mon $perp_year C";
		$banners = $accesses{$banners};
		if (($TotalS == 0) || ($banners==$TotalS)) { $bannerspercent = 0; }
		else { $bannerspercent=int((($banners/$TotalS)*($daycounter*$MultS))+.5); }
		$clicksfrom = $accesses{$clicksfrom};
		if (($TotalX == 0) || ($clicksfrom==$TotalX)) { $clicksfrompercent = 0; }
		else { $clicksfrompercent=int((($clicksfrom/$TotalX)*($daycounter*$MultX))+.5); }
		$exposures = $accesses{$exposures};
		if (($TotalE == 0) || ($exposures==$TotalE)) { $exposurespercent = 0; }
		else { $exposurespercent=int((($exposures/$TotalE)*($daycounter*$MultE))+.5); }
		$clicks = $accesses{$clicks};
		if (($TotalC == 0) || ($clicks==$TotalC)) { $clickspercent = 0; }
		else { $clickspercent=int((($clicks/$TotalC)*($daycounter*$MultC))+.5); }
		if ($banners < 1) { $banners = "0"; }
		if ($clicksfrom < 1) { $clicksfrom = "0"; }
		if ($exposures < 1) { $exposures = "0"; }
		if ($clicks < 1) { $clicks = "0"; }
		if ($bannex) {
			print "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
			print "<TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($banners),"</FONT></SMALL></TD>";
			if ($bannerspercent==0) { print "<TD>&nbsp;</TD>"; }
			else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$bannerspercent NOSHADE COLOR=\"#666666\"></TD>"; }
			if ($INPUT{'showclicksfrom'}) {
				print "<TD>&nbsp;</TD><TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($clicksfrom),"</FONT></SMALL></TD>";
				if ($clicksfrompercent==0) { print "<TD>&nbsp;</TD>"; }
				else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$clicksfrompercent NOSHADE COLOR=\"#666666\"></TD>"; }
				if ($banners == 0) { $perc = "-"; }
				elsif ($clicksfrom == 0) { $perc = "-"; }
				else { $perc = ((100*($clicksfrom/$banners))+.05001); }
				unless ($perc eq "-") {
					$perc =~ s/(\d+\.\d).*/$1/;
					$perc = $perc."%";
				}
				print "<TD>&nbsp;</TD><TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD>";
			}
		}
		if ($beingshown) {
			print "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
			print "<TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($exposures),"</FONT></SMALL></TD>";
			if ($exposurespercent==0) { print "<TD>&nbsp;</TD>"; }
			else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$exposurespercent NOSHADE COLOR=\"#666666\"></TD>"; }
			print "<TD>&nbsp;</TD><TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($clicks),"</FONT></SMALL></TD>";
			if ($clickspercent==0) { print "<TD>&nbsp;</TD>"; }
			else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$clickspercent NOSHADE COLOR=\"#666666\"></TD>"; }
			if ($exposures == 0) { $perc = "-"; }
			elsif ($clicks == 0) { $perc = "-"; }
			else { $perc = ((100*($clicks/$exposures))+.05001); }
			unless ($perc eq "-") {
				$perc =~ s/(\d+\.\d).*/$1/;
				$perc = $perc."%";
			}
			print "<TD>&nbsp;</TD><TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD>";
			print "</TR>\n";
		}
	}
	print "</TABLE></CENTER>\n";
	if ($ShortenedList) {
		print "<P><CENTER><FORM METHOD=POST ";
		print "ACTION=$admin_cgi>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert ";
		print "VALUE=$AccountName>\n";
		print "<INPUT TYPE=HIDDEN NAME=FullDailyList ";
		print "VALUE=Yes>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($INPUT{'showclicksfrom'}) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=dailystats ";
		print "VALUE=\"$text{'2226'}\">";
		print "</FORM></CENTER>\n";
	}
	elsif ($INPUT{'FullDailyList'}) {
		print "<P><CENTER><FORM METHOD=POST ";
		print "ACTION=$admin_cgi>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert ";
		print "VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($INPUT{'showclicksfrom'}) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=dailystats ";
		print "VALUE=\"$text{'2225'}\">";
		print "</FORM></CENTER>\n";
	}
	print "<P><CENTER><TABLE><TR ALIGN=CENTER>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=reviewone VALUE=";
	if ($INPUT{'groupstatus'}) {
		print "\"$INPUT{'groupstatus'}\"";
	}
	else {
		print "$AccountName";
	}
	print ">\n";
	print "<INPUT TYPE=SUBMIT ";
	print "VALUE=\"$text{'2206'}\"> ";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($LogByZone) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=logbyzone ";
		print "VALUE=\"$text{'2202'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=monthlystats ";
	print "VALUE=\"$text{'2204'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($ADVLogIP) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert ";
		print "VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($INPUT{'showclicksfrom'}) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=iplog ";
		print "VALUE=\"$text{'2205'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "</TR></TABLE></CENTER>\n";
	&Footer;
}

sub monthlystats {
	$AccountName = $INPUT{'advert'};
	&CheckName;
	&ConfirmUserPassword;
	open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.log") || &Error_NoStats;
	@lines = <DISPLAY>;
	close (DISPLAY);
	chomp (@lines);
	foreach $line (@lines) {
		next if (length($line) < 10);
		($acc,$logstring) = ($line =~
		  /^(\d\d\d\d\d\d\d\d\d\d) \d\d (\d\d \d\d\d\d \w)$/);
		$accesses{$logstring} += int($acc);
		($mon,$year,$type) = ($logstring =~
			  /^(\d+) (\d+) (\w)/);
		if ($type eq "E") {
			$beingshown = 1;
			$TotalE += int($acc);
		}
		elsif ($type eq "C") {
			$beingshown = 1;
			$TotalC += int($acc);
		}
		elsif ($type eq "S") {
			$bannex = 1;
			$TotalS += int($acc);
		}
		elsif ($type eq "X") {
			$bannex = 1;
			$TotalX += int($acc);
		}
		else { next; }
		unless ($startyear) {
			$startyear = $year;
			$startmon = $mon;
		}
	}
	$endyear = $year;
	$endmon = $mon;
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>$text{'2230'} ";
	print "<EM>$AccountName</EM> $text{'2231'}";
	print ":</STRONG></BIG></BIG>\n";
	print "<P><CENTER><TABLE CELLPADDING=3>\n";
	print "<TR ALIGN=CENTER VALIGN=BOTTOM>";
	print "<TD><SMALL><FONT $fontspec><EM>$text{'2232'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	if ($bannex) {
		print "<TD NOWRAP>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2040'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		if ($INPUT{'showclicksfrom'}) {
			print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2041'}";
			print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
			print "<TD COLSPAN=2><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		}
	}
	if ($beingshown) {
		print "<TD NOWRAP>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2030'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD NOWRAP COLSPAN=3><SMALL><FONT $fontspec><EM>$text{'2031'}";
		print "</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
		print "<TD COLSPAN=2><SMALL><FONT $fontspec><EM>$text{'2018'}</EM><BR><HR NOSHADE></FONT></SMALL></TD>";
	}
	foreach $year ($startyear..$endyear) {
		if ($year == $startyear) { $firstmon = $startmon; }
		else { $firstmon = "01"; }
		if ($year == $endyear) { $lastmon = $endmon; }
		else { $lastmon = 12; }
		foreach $month ($firstmon..$lastmon) {
			$TestS = "$month $year S";
			$TestX = "$month $year X";
			$TestE = "$month $year E";
			$TestC = "$month $year C";
			if ($accesses{$TestS} > $MaxS) { $MaxS = $accesses{$TestS}; }
			if ($accesses{$TestX} > $MaxX) { $MaxX = $accesses{$TestX}; }
			if ($accesses{$TestE} > $MaxE) { $MaxE = $accesses{$TestE}; }
			if ($accesses{$TestC} > $MaxC) { $MaxC = $accesses{$TestC}; }
			$monthcounter++;
		}
	}
	if ($TotalE) { $MultE = ((49.5/($MaxE/$TotalE))/$monthcounter); }
	if ($TotalC) { $MultC = ((49.5/($MaxC/$TotalC))/$monthcounter); }
	if ($TotalS) { $MultS = ((49.5/($MaxS/$TotalS))/$monthcounter); }
	if ($TotalX) { $MultX = ((49.5/($MaxX/$TotalX))/$monthcounter); }
	foreach $year ($startyear..$endyear) {
		if ($year == $startyear) { $firstmon = $startmon; }
		else { $firstmon = "01"; }
		if ($year == $endyear) { $lastmon = $endmon; }
		else { $lastmon = 12; }
		foreach $month ($firstmon..$lastmon) {
			print "<TR ALIGN=CENTER>";
			print "<TD NOWRAP><SMALL><FONT $fontspec>$months[$month-1] $year</FONT></SMALL></TD>";
			$banners = "$month $year S";
			$clicksfrom = "$month $year X";
			$exposures = "$month $year E";
			$clicks = "$month $year C";
			$banners = $accesses{$banners};
			if (($TotalS == 0) || ($banners==$TotalS)) { $bannerspercent = 0; }
			else { $bannerspercent=int((($banners/$TotalS)*($monthcounter*$MultS))+.5); }
			$clicksfrom = $accesses{$clicksfrom};
			if (($TotalX == 0) || ($clicksfrom==$TotalX)) { $clicksfrompercent = 0; }
			else { $clicksfrompercent=int((($clicksfrom/$TotalX)*($monthcounter*$MultX))+.5); }
			$exposures = $accesses{$exposures};
			if (($TotalE == 0) || ($exposures==$TotalE)) { $exposurespercent = 0; }
			else { $exposurespercent=int((($exposures/$TotalE)*($monthcounter*$MultE))+.5); }
			$clicks = $accesses{$clicks};
			if (($TotalC == 0) || ($clicks==$TotalC)) { $clickspercent = 0; }
			else { $clickspercent=int((($clicks/$TotalC)*($monthcounter*$MultC))+.5); }
			if ($banners < 1) { $banners = "0"; }
			if ($clicksfrom < 1) { $clicksfrom = "0"; }
			if ($exposures < 1) { $exposures = "0"; }
			if ($clicks < 1) { $clicks = "0"; }
			if ($bannex) {
				print "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
				print "<TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($banners),"</FONT></SMALL></TD>";
				if ($bannerspercent==0) { print "<TD>&nbsp;</TD>"; }
				else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$bannerspercent NOSHADE COLOR=\"#666666\"></TD>"; }
				if ($INPUT{'showclicksfrom'}) {
					print "<TD>&nbsp;</TD><TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($clicksfrom),"</FONT></SMALL></TD>";
					if ($clicksfrompercent==0) { print "<TD>&nbsp;</TD>"; }
					else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$clicksfrompercent NOSHADE COLOR=\"#666666\"></TD>"; }
					if ($banners == 0) { $perc = "-"; }
					elsif ($clicksfrom == 0) { $perc = "-"; }
					else { $perc = ((100*($clicksfrom/$banners))+.05001); }
					unless ($perc eq "-") {
						$perc =~ s/(\d+\.\d).*/$1/;
						$perc = $perc."%";
					}
					print "<TD>&nbsp;</TD><TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD>";
				}
			}
			if ($beingshown) {
				print "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
				print "<TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($exposures),"</FONT></SMALL></TD>";
				if ($exposurespercent==0) { print "<TD>&nbsp;</TD>"; }
				else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$exposurespercent NOSHADE COLOR=\"#666666\"></TD>"; }
				print "<TD>&nbsp;</TD><TD ALIGN=RIGHT><SMALL><FONT $fontspec>",&commas($clicks),"</FONT></SMALL></TD>";
				if ($clickspercent==0) { print "<TD>&nbsp;</TD>"; }
				else { print "<TD><HR ALIGN=LEFT SIZE=5 WIDTH=$clickspercent NOSHADE COLOR=\"#666666\"></TD>"; }
				if ($exposures == 0) { $perc = "-"; }
				elsif ($clicks == 0) { $perc = "-"; }
				else { $perc = ((100*($clicks/$exposures))+.05001); }
				unless ($perc eq "-") {
					$perc =~ s/(\d+\.\d).*/$1/;
					$perc = $perc."%";
				}
				print "<TD>&nbsp;</TD><TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD>";
				print "</TR>\n";
			}
		}
	}
	print "</TABLE></CENTER>\n";
	print "<P><CENTER><TABLE><TR ALIGN=CENTER>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=reviewone VALUE=";
	if ($INPUT{'groupstatus'}) {
		print "\"$INPUT{'groupstatus'}\"";
	}
	else {
		print "$AccountName";
	}
	print ">\n";
	print "<INPUT TYPE=SUBMIT ";
	print "VALUE=\"$text{'2206'}\"> ";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($LogByZone) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=logbyzone ";
		print "VALUE=\"$text{'2202'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "<FORM METHOD=POST ";
	print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert ";
	print "VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=dailystats ";
	print "VALUE=\"$text{'2203'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($ADVLogIP) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert ";
		print "VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($INPUT{'showclicksfrom'}) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=iplog ";
		print "VALUE=\"$text{'2205'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "</TR></TABLE></CENTER>\n";
	&Footer;
}

sub iplog {
	$AccountName = $INPUT{'advert'};
	&CheckName;
	&ConfirmUserPassword;
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>$text{'2240'} ",
	  "<EM>$AccountName</EM> $text{'2241'}:</STRONG></BIG></BIG>\n",
	  "<P>$text{'2242'}\n",
	  "<P><FONT FACE=\"Courier\"><PRE>";
	($mday,$mon) = (localtime($time-86400+($HourOffset*3600)))[3,4];
	if ($mday < 10) { $mday = "0".$mday; }
	$mon++;
	if ($mon < 10) { $mon = "0".$mon; }
	open (DISPLAY, "$adverts_dir/$subdir/$AccountName.$mon$mday.log");
	&ShowIPs;
	close (DISPLAY);
	($mday,$mon) = (localtime($time+($HourOffset*3600)))[3,4];
	if ($mday < 10) { $mday = "0".$mday; }
	$mon++;
	if ($mon < 10) { $mon = "0".$mon; }
	open (DISPLAY, "$adverts_dir/$subdir/$AccountName.$mon$mday.log");
	&ShowIPs;
	close (DISPLAY);
	print "</PRE></FONT>\n";
	print "<P>$text{'2243'}: <STRONG>",&commas($ExposureCount),"</STRONG>\n";
	print "<BR>$text{'2244'}: <STRONG>",&commas($ClickCount),"</STRONG>\n";
	print "<BR>$text{'2245'}: <STRONG>",&commas($IPCount),"</STRONG>\n";
	if ($IPCount<1) { $AverageEntries = 0; }
	else { $AverageEntries = (($ExposureCount+$ClickCount)/$IPCount)+.05; }
	if ($AverageEntries < 10) {
		$AverageEntries =~ s/(...).*/$1/;
	}
	else {
		$AverageEntries =~ s/(....).*/$1/;
	}
	print "<P>$text{'2246'}: <STRONG>${AverageEntries}</STRONG>\n";
	print "<P><CENTER><TABLE><TR ALIGN=CENTER>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=reviewone VALUE=";
	if ($INPUT{'groupstatus'}) {
		print "\"$INPUT{'groupstatus'}\"";
	}
	else {
		print "$AccountName";
	}
	print ">\n";
	print "<INPUT TYPE=SUBMIT ";
	print "VALUE=\"$text{'2206'}\"> ";
	print "</FONT></SMALL></TD></FORM>\n";
	if ($LogByZone) {
		print "<FORM METHOD=POST ";
		print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
		print "<INPUT TYPE=HIDDEN NAME=password ";
		print "VALUE=$INPUT{'password'}>\n";
		if ($INPUT{'groupstatus'}) {
			print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
			print "VALUE=\"$INPUT{'groupstatus'}\">\n";
		}
		print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		if ($clicksratio || $cryptword || $ShowClicksFrom) {
			print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
		}
		print "<INPUT TYPE=SUBMIT NAME=logbyzone ";
		print "VALUE=\"$text{'2202'}\">";
		print "</FONT></SMALL></TD></FORM>\n";
	}
	print "<FORM METHOD=POST ";
	print "ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert ";
	print "VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=dailystats ";
	print "VALUE=\"$text{'2203'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi><TD><SMALL><FONT $fontspec>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	if ($INPUT{'groupstatus'}) {
		print "<INPUT TYPE=HIDDEN NAME=groupstatus ";
		print "VALUE=\"$INPUT{'groupstatus'}\">\n";
	}
	print "<INPUT TYPE=HIDDEN NAME=advert VALUE=$AccountName>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	if ($INPUT{'showclicksfrom'}) {
		print "<INPUT TYPE=HIDDEN NAME=showclicksfrom VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=monthlystats ";
	print "VALUE=\"$text{'2204'}\">";
	print "</FONT></SMALL></TD></FORM>\n";
	print "</TR></TABLE></CENTER>\n";
	&Footer;
}

sub ShowIPs {
	if ($IPLog) { dbmopen(%Resolved,"$IPLog",0666); }
	while (<DISPLAY>) {
		next if (length($_) < 5);	
		if (/([^\s]* E) ([^\s]*)\n/) {
			$ExposureCount ++;
			$ThisTime = $1;
			$ThisIP = $2;
		}
		elsif (/([^\s]* C) ([^\s]*)\n/) {
			$ClickCount ++;
			$ThisTime = $1;
			$ThisIP = $2;
		}
		$ThisTrimmedIP = $ThisIP;
		if ($ThisTrimmedIP =~ /\d+\.\d+\.\d+\.\d+/) {
			$ThisTrimmedIP =~ s/(\d+\.\d+\.\d+)\.\d+/$1\.XXX/;
			if ($Resolved{$ThisTrimmedIP}
			  && ($Resolved{$ThisTrimmedIP} ne "unresolved")) {
				foreach $key (8..15) {
					if (length($ThisIP)<$key) { $ThisIP .= " "; }
				}
				$ThisIP .= "  $Resolved{$ThisTrimmedIP}";
			}
		}
		unless ($NoPrintIPs) { print "$mon/$mday $ThisTime  $ThisIP\n"; }
		$IPCount{$ThisIP} ++;
		if ($IPCount{$ThisIP} == 1) {
			$IPCount ++;
		}
	}
	if ($IPLog) { dbmclose(%Resolved); }
}

sub masteriplog {
	&ConfirmAdminPassword(1);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>",
	  "Master IP Address Report</STRONG></BIG></BIG>\n",
	  "<P>The following report details the IP addresses which ",
	  "have seen or clicked on banners in the rotation ",
	  "in the last <STRONG>$MasterIPLogDays</STRONG> days.\n";
	$NoPrintIPs = 1;
	foreach $key (a..z,0..9) {
		opendir (FILES,"$adverts_dir/$key");
		@files = readdir(FILES);
		closedir (FILES);
		foreach $file (@files) {
			next unless (-d "$adverts_dir/$key/$file");
			opendir (SUBFILES,"$adverts_dir/$key/$file");
			@subfiles = readdir(SUBFILES);
			closedir (SUBFILES);
			foreach $subfile (@subfiles) {
				next unless ($subfile=~/\d\d\d\d\.log/);
				open (DISPLAY, "$adverts_dir/$key/$file/$subfile");
				&ShowIPs;
				close (DISPLAY);
			}
		}
	}
	print "<P>Total exposures logged: <STRONG>",&commas($ExposureCount),"</STRONG>\n";
	print "<BR>Total clicks logged: <STRONG>",&commas($ClickCount),"</STRONG>\n";
	print "<BR>Total IP addresses logged: <STRONG>",&commas($IPCount),"</STRONG>\n";
	if ($IPCount<1) { $AverageEntries = 0; }
	else { $AverageEntries = (($ExposureCount+$ClickCount)/$IPCount)+.05; }
	if ($AverageEntries < 10) {
		$AverageEntries =~ s/(...).*/$1/;
	}
	else {
		$AverageEntries =~ s/(....).*/$1/;
	}
	print "<P>Average log entries per IP address: <STRONG>${AverageEntries}</STRONG>\n";
	print "<P><STRONG>The Top 50 Most Active IP Addresses:</STRONG>\n";
	print "<FONT FACE=\"Courier\"><PRE>\n";
	foreach $key (sort ByCount keys(%IPCount)) {
		last if ($Counter > 49);
		$ip = $key;
		printf "%10s%-s\n",&commas($IPCount{$key}),"       $ip";
		$Counter++;
	}
	print "</PRE></FONT>\n";
	&LinkBack;
	&Footer;
}

sub ByCount {
	$IPCount{$b}<=>$IPCount{$a};
}

sub cheatercheck {
	&ConfirmAdminPassword(1);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>",
	  "&quot;Cheater Check&quot; Report</STRONG></BIG></BIG>\n",
	  "<P>The following report lists the number of banners shown on, ",
	  "and the number of click-thrus from, each exchange member site ",
	  "which &quot;earns&quot; exposures. Any sites with unusually ",
	  "high or low click-thru ratios may be trying to &quot;cheat&quot; ",
	  "the system by artificially inflating one or the other of those ",
	  "counts.\n";
	open (LIST, "<$adverts_dir/adlist.txt");
	@advertisements = <LIST>;
	close (LIST);
	chomp (@advertisements);
	@sortedadverts = sort (@advertisements);
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			print "<P><CENTER><TABLE CELLPADDING=3>\n",
			  "<TR ALIGN=CENTER VALIGN=BOTTOM>",
			  "<TD><SMALL><FONT $fontspec><EM>Account</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
			  "<TD><SMALL><FONT $fontspec><EM>Banners Shown<BR>on Your Site</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
			  "<TD><SMALL><FONT $fontspec><EM>Clicks From<BR>Your Site</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
			  "<TD><SMALL><FONT $fontspec><EM>%</EM><BR><HR NOSHADE></FONT></SMALL></TD>",
			  "<TD><SMALL><FONT $fontspec><EM>Ratio</EM><BR><HR NOSHADE></FONT></SMALL></TD></TR>\n";
			foreach $advertiser (@sortedadverts) {
				$name = $advertiser;
				next if (length($advertiser) < 1);
				($max,$shown,$visits,$image,$start,$weight,
				  $zone,$raw,$displayratio,$clicksfrom) = split(/\t/,$DBMList{$name});
				($displayratio,$displaycount) = split(/\|/, $displayratio);
				($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
				next unless (($displayratio > 0) || ($displaycount > 0)
				  || ($clicksfrom > 0) || ($clicksratio > 0));
				if (($displaycount == 0) || ($clicksfrom == 0)) {
					$perc = "$text{'2020'}";
					$ratio = "$text{'2020'}";
				}
				else {
					$perc = ((100*($clicksfrom/$displaycount))+.05001);
					$ratio = (($displaycount/$clicksfrom)+.5001);
				}
				unless ($perc eq "$text{'2020'}") {
					$perc =~ s/(\d+\.\d).*/$1/;
					$perc = $perc."%";
				}
				unless ($ratio eq "$text{'2020'}") {
					$ratio =~ s/(\d+)\.\d.*/$1/;
					$ratio = $ratio.":1";
				}
				print "<TR ALIGN=CENTER>\n",
				  "<TD><SMALL><FONT $fontspec>$advertiser</FONT></SMALL></TD>\n";
				print "<TD><SMALL><FONT $fontspec>",&commas($displaycount),"</FONT></SMALL></TD>";
				print "<TD><SMALL><FONT $fontspec>",&commas($clicksfrom),"</FONT></SMALL></TD>";
				print "<TD><SMALL><FONT $fontspec>$perc</FONT></SMALL></TD><TD><SMALL><FONT $fontspec>$ratio</FONT></SMALL></TD></TR>\n";
			}
			&ADVDBMClose;
			print "</TABLE></CENTER>\n";
		}
	}
	print "<P><HR>";
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	&LinkBack;
	&Footer;
}

sub ShowAdvert {
	if ($raw || $image) {
		print "<CENTER><P><TABLE BORDER=1 CELLSPACING=0 CELLPADDING=12><TR>";
	}
	@image = split(/\|/,$image);
	if ($raw) {
		$realraw = $raw;
		$realraw =~ s/<NLB>/\n/g;
		$realraw =~ s/<RAND>/$time/g;
		$realraw =~ s/<URL>//g;
		print "<TD>$realraw</TD>";
	}
	else {
		$url =~ s/<RAND>/$time/g;
		print "<TD ALIGN=CENTER>";
		foreach $image (@image) {
			$img = $image;
			$img =~ s/<RAND>/$time/g;
			if ($NotFirst) { print "<P>"; }
			else { $NotFirst=1; }
			if ($text && ($texttype eq "T")) {
				print "<SMALL>$text</SMALL><BR>";
			}
			if ($url) {
				print "<A HREF=\"$url\"";
				if ($target eq "_top") { print " TARGET=\"$target\""; }
				elsif ($target) { print " $target"; }
				print ">";
			}
			print "<IMG SRC=\"$img\"";
			if ($ExchangeLogo && $ExchangeBannerHeight && $ExchangeBannerWidth) {
				print " HEIGHT=$ExchangeBannerHeight WIDTH=$ExchangeBannerWidth";
			}
			elsif ($height && $width) {
				print " HEIGHT=$height WIDTH=$width";
			}
			print " ALT=\"$alt\"";
			unless ($border) { $border="0"; }
			print " BORDER=$border>";
			if ($url) { print "</A>"; }
			if ($text && ($texttype eq "B")) {
				print "<BR><SMALL>$text</SMALL>";
			}
			print "\n";
		}
		print "</TD>";
	}
	if ($raw || $image) {
		print "</TR></TABLE></CENTER>\n";
	}
	if ($url) {
		print "<P ALIGN=CENTER>Destination: ";
		print "<A HREF=\"$url\">$url</A>\n";
	}
	if ($username || $email) {
		if ($url) { print "<BR>"; }
		else { print "<P ALIGN=CENTER>"; }
		print "Account Holder: ";
		if ($email) {
			print "<A HREF=\"mailto:$email\">";
		}
		if ($username) { print "$username"; }
		else { print "$email"; }
		if ($email) {
			print "</A>";
		}
		print "\n";
	}
}

sub resetadminlog {
	&ConfirmAdminPassword(1);
	unlink "$adverts_dir/adminlog.txt";
	if ($AdminDisplaySetup) { &defineview; }
	else {
		$INPUT{'whichtype'} = "pending established groups";
		$INPUT{'whichtime'} = "active expired disabled";
		$INPUT{'whichzone'} = "";
		&reviewall;
	}
}

sub adminlog {
	&ConfirmAdminPassword(1);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>",
	  "Admin Accesses Report</STRONG></BIG></BIG>\n",
	  "<P>The following report lists the IP addresses ",
	  "which have accessed administrative functions.\n";
	print "<FONT FACE=\"Courier\"><PRE>\n";
	open (ADMINLOG,"$adverts_dir/adminlog.txt");
	while (<ADMINLOG>) { print "$_"; }
	close (ADMINLOG);
	print "</PRE></FONT>\n";
	&LinkBack;
	&Footer;
}

sub RenameAccount {
	&ConfirmAdminPassword(1);
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			$AccountName = $INPUT{'oldname'};
			&CheckName;
			$oldname = $AccountName;
			$oldsubdir = $subdir;
			unless ($DBMList{$oldname}) {
				&Header("$text{'9000'}","Rename Error!");
				print "<P ALIGN=CENTER>No account &quot;$oldname&quot; exists!\n";
				&ADVDBMClose;
				&ADVLockClose (DBMLIST, "dbmlist.txt");
				&Footer;
			}
			$AccountName = $INPUT{'newname'};
			&CheckName;
			$newname = $AccountName;
			$newsubdir = $subdir;
			if ($DBMList{$newname}) {
				&Header("$text{'9000'}","Rename Error!");
				print "<P ALIGN=CENTER>Account name &quot;$newname&quot; is already in use!\n";
				&ADVDBMClose;
				&ADVLockClose (DBMLIST, "dbmlist.txt");
				&Footer;
			}
			unless (-d "$adverts_dir/$newsubdir") {
				mkdir ("$adverts_dir/$newsubdir",0777);
				chmod 0777,"$adverts_dir/$newsubdir";
			}
			opendir (FILES,"$adverts_dir/$oldsubdir");
			@files = readdir(FILES);
			closedir (FILES);
			foreach $file (@files) {
				next if ($file =~ /^\./);
				$_ = $file; /^(.+)$/; $file = $1;
				$newfile = $file;
				$newfile =~ s/$oldname/$newname/;
				rename ("$adverts_dir/$oldsubdir/$file","$adverts_dir/$newsubdir/$newfile");
			}
			rmdir ("$adverts_dir/$oldsubdir");
			if ($UserUploadDir) {
				rename ("$UserUploadDir/$oldname\.gif","$UserUploadDir/$newname\.gif");
				rename ("$UserUploadDir/$oldname\.jpg","$UserUploadDir/$newname\.jpg");
				&ADVLockOpen (COUNT, "$newsubdir/$newname.txt");
				@lines = <COUNT>;
				chomp (@lines);
				seek (COUNT,0,0);
				foreach $line (@lines) {
					$line =~ s/$oldname\.gif/$newname\.gif/;
					$line =~ s/$oldname\.jpg/$newname\.jpg/;
					print COUNT "$line\n";
				}
				truncate (COUNT, tell(COUNT));
				&ADVLockClose (COUNT,"$newsubdir/$newname.txt");
			}
			if (-s "$adverts_dir/adlist.txt") {
				&ADVLockOpen (COUNT, "adlist.txt");
				@lines = <COUNT>;
				chomp (@lines);
				seek (COUNT,0,0);
				foreach $line (@lines) {
					if ($line eq $oldname) { print COUNT "$newname\n"; }
					else { print COUNT "$line\n"; }
				}
				truncate (COUNT, tell(COUNT));
				&ADVLockClose (COUNT,"adlist.txt");
			}
			if (-s "$adverts_dir/adnew.txt") {
				&ADVLockOpen (COUNT, "adnew.txt");
				@lines = <COUNT>;
				chomp (@lines);
				seek (COUNT,0,0);
				foreach $line (@lines) {
					if ($line eq $oldname) { print COUNT "$newname\n"; }
					else { print COUNT "$line\n"; }
				}
				truncate (COUNT, tell(COUNT));
				&ADVLockClose (COUNT,"adnew.txt");
			}
			$DBMList{$newname} = $DBMList{$oldname};
			delete ($DBMList{$oldname});
			&ADVDBMClose;
		}
	}
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER><BIG><BIG><STRONG>Account Renamed</STRONG></BIG></BIG>\n",
	  "<P ALIGN=CENTER>The &quot;$oldname&quot; account has been renamed &quot;$newname.&quot;\n";
	&LinkBack;
	&Footer;
}

sub edit {
	&ConfirmAdminPassword(1);
	if ($INPUT{'reviewone'} && !($INPUT{'editad'})) {
		$INPUT{'editad'} = $INPUT{'reviewone'};
	}
	$AccountName = $INPUT{'editad'};
	&CheckName;
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		foreach $line (@lines) {
			$line =~ s/&/&amp;/g;
			$line =~ s/>/&gt;/g;
			$line =~ s/</&lt;/g;
			$line =~ s/"/&quot;/g;
		}
		($pass,$username,$email,$comments) = @lines;
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		foreach $line (@lines) {
			$line =~ s/&/&amp;/g;
			$line =~ s/>/&gt;/g;
			$line =~ s/</&lt;/g;
			$line =~ s/"/&quot;/g;
		}
		($max,$shown,$visits,$url,$image,$height,$width,
		  $alt,$nada,$text,$start,$weight,$zone,
		  $border,$target,$raw,$displayratio,$nada,$nada,
		  $displayzone,$clicksfrom) = @lines;
		($max,$maxtype) = split(/\|/, $max);
		($text,$texttype) = split(/\|/, $text);
		($displayratio,$displaycount) = split(/\|/, $displayratio);
		($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
	}
	unless ($weight) {
		if ($maxtype) { $weight = "0"; }
		else { $weight = $DefaultWeight; }
	}
	unless ($border) {
		if ($maxtype) { $border = "0"; }
		else { $border = $DefaultBorder; }
	}
	unless ($displayratio) {
		if ($maxtype) { $displayratio = "0"; }
		else { $displayratio = $DefaultDisplayRatio; }
	}
	unless ($clicksratio) {
		if ($maxtype) { $clicksratio = "0"; }
		else { $clicksratio = $DefaultClicksRatio; }
	}
	unless ($target) {
		if ($maxtype) { $target = ""; }
		else { $target = $DefaultLinkAttribute; }
	}
	if ($target eq "_top") { $target = "TARGET=&quot;_top&quot;"; }
	unless ($maxtype) { $maxtype = "E"; }
	unless ($texttype) { $texttype = "B"; }
	unless ($url) { $url = "http://"; }
	$image =~ s/\|/\n/g;
	unless ($image) { $image = "http://"; }
	&Header("$text{'1000'}","$text{'1001'}");
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<P ALIGN=CENTER><BIG><STRONG>Info for the ",
	  "<EM>$AccountName</EM> Account:",
	  "</STRONG></BIG>\n",
	  "<P><CENTER><TABLE CELLPADDING=3>\n",
	  "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "<TR><TD COLSPAN=2><SMALL><FONT $fontspec>",
	  "<STRONG>I. General Information</STRONG>: ",
	  "The following is general &quot;background&quot; ",
	  "information. The purpose of the &quot;Name&quot; and &quot;E-Mail&quot; ",
	  "fields should of course be obvious; the &quot;Password&quot; field records ",
	  "the password the account holder will use to view his stats. The ",
	  "&quot;Comments&quot; field can be used for whatever other information ",
	  "you deem important. (It will be seen only by you, and never by the ",
	  "account holder, so you may be as candid as you like.) The other ",
	  "fields help to determine how, where, and how often the account's banner ",
	  "will be shown.</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Name:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=username VALUE=\"$username\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>E-Mail:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=email VALUE=\"$email\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Comments:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=comments VALUE=\"$comments\" SIZE=50></FONT></SMALL></TD></TR>\n";
	print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Start Day:</FONT></SMALL></TD><TD><SMALL>";
	if ($start) { ($mday,$mon,$year) = (localtime($start+($HourOffset*3600)))[3,4,5]; }
	else { ($mday,$mon,$year) = (localtime($time+($HourOffset*3600)))[3,4,5]; }
	$year += 1900;
	print "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=\"StartDateA\" SIZE=2 VALUE=$mday> ";
	print "<SELECT NAME=\"StartDateB\">";
	foreach $key (0..11) {
		print "<OPTION VALUE=\"$key\"";
		if ($key == $mon) { print " SELECTED"; }
		print ">$months[$key]";
	}
	print "</SELECT> ";
	print "<INPUT TYPE=TEXT NAME=\"StartDateC\" SIZE=4 VALUE=$year>";
	print "</FONT><FONT $fontspec><BR><SMALL><EM>(Input the date ",
	  "on which the account should start running, or leave unaltered ",
	  "to start the run immediately.)</EM></SMALL>",
	  "</FONT></SMALL></TD></TR>\n";
	print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Expiration:</FONT></SMALL></TD>";
	print "<TD><SMALL><INPUT TYPE=RADIO NAME=purchtype VALUE=N";
	if ($maxtype eq "N") { print " CHECKED"; }
	print "> Never Expires<BR><INPUT TYPE=RADIO NAME=purchtype VALUE=D";
	if ($maxtype eq "D") { print " CHECKED"; }
	print "> Expires on Date: ";
	if ($max && ($maxtype eq "D")) {
		($mday,$mon,$year) = (localtime($max+($HourOffset*3600)))[3,4,5];
		$max = "0";
	}
	else { ($mday,$mon,$year) = (localtime($time+($HourOffset*3600)))[3,4,5]; }
	$year += 1900;
	print "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=\"EndDateA\" SIZE=2 VALUE=$mday> ";
	print "<SELECT NAME=\"EndDateB\">";
	foreach $key (0..11) {
		print "<OPTION VALUE=\"$key\"";
		if ($key == $mon) { print " SELECTED"; }
		print ">$months[$key]";
	}
	print "</SELECT> ";
	print "<INPUT TYPE=TEXT NAME=\"EndDateC\" SIZE=4 VALUE=$year><BR>";
	unless ($max) { $max = "0"; }
	print "</FONT><FONT $fontspec>Expires After: </FONT><FONT FACE=\"Courier\">";
	print "<INPUT TYPE=TEXT NAME=purch VALUE=\"$max\" SIZE=12>";
	print "</FONT><FONT $fontspec> <INPUT TYPE=RADIO NAME=purchtype VALUE=E";
	if ($maxtype eq "E") { print " CHECKED"; }
	print "> Exposures <INPUT TYPE=RADIO NAME=purchtype VALUE=C";
	if ($maxtype eq "C") { print " CHECKED"; }
	print "> Clicks\n";
	print "<BR><SMALL><EM>(Input the date on which the run will end, or the maximum number ",
	  "of exposures or click-thrus to be allowed for the run.)</EM></SMALL>",
	  "</FONT></SMALL></TD></TR>\n",
	  "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Display Ratio:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=displayratio VALUE=\"$displayratio\" SIZE=5></FONT><FONT $fontspec> displays earn 1 exposure",
	  "<BR>1 click earns </FONT><FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=clicksratio VALUE=\"$clicksratio\" SIZE=5></FONT><FONT $fontspec> exposures",
	  "<BR><SMALL><EM>(If this account is to &quot;earn&quot; exposures by ",
	  "showing other banners, input the appropriate details here.)</SMALL>",
	  "</FONT></SMALL></TD></TR>\n",
	  "<TR><TD></TD><TD><SMALL><FONT $fontspec>",
	  "Note that while <EM>either</EM> an expiration or a display ",
	  "ratio must be set, it is not necessary to set both. ",
	  "If you're running a &quot;banner exchange,&quot; ",
	  "the display ratio will define the rate at which banner exposures ",
	  "are earned, based upon displays and/or click-thrus generated by ",
	  "the member. In that case, generally, you'll want to leave the ",
	  "expiration set either to &quot;0 Exposures&quot; or &quot;Never Expires.&quot; ",
	  "If, on the other hand, you're running straight advertisements, leave the ",
	  "display ratios set to 0 (or undefined), and simply set the appropriate ",
	  "expiration criteria instead. Define both only if you're running ",
	  "an exchange in which the member is to get extra ",
	  "&quot;bonus&quot; exposures; in that case, define the bonus number ",
	  "in the &quot;expiration&quot; slot.</EM>",
	  "</FONT></SMALL></TD></TR>\n",
	  "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Weight (Wt.):</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=weight VALUE=\"$weight\" SIZE=5>",
	  "</FONT><FONT $fontspec><BR><SMALL><EM>(Define how often this banner will be eligible for display. 0 = never, 1 = every cycle through the list, ",
	  "2 = every other cycle, 3 = every third cycle, etc.)</EM></SMALL>",
	  "</FONT></SMALL></TD></TR>\n";
	if (@zones) {
		print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Zone(s):</FONT></SMALL></TD><TD><SMALL><FONT $fontspec>";
		foreach $setzone (sort (@zones)) {
			print "<INPUT TYPE=CHECKBOX NAME=zone ";
			print "VALUE=\"$setzone\"";
			if ($zone =~ /\s$setzone\s/) {
				print " CHECKED";
			}
			print ">$setzone\n<BR>";
		}
		print "<SMALL><EM>(Select above, the zones -- or &quot;target categories&quot; -- ";
		print "in which this banner should be displayed.)</EM></SMALL>";
		print "</FONT></SMALL></TD></TR>\n";
		print "<TR><TD></TD><TD><SMALL><FONT $fontspec>";
		$displayzone = "\+".$displayzone."\+";
		foreach $setzone (sort (@zones)) {
			print "<INPUT TYPE=CHECKBOX NAME=displayzone ";
			print "VALUE=\"$setzone\"";
			if ($displayzone =~ /\+$setzone\+/) {
				print " CHECKED";
			}
			print ">$setzone\n<BR>";
		}
		print "<SMALL><EM>(If this account holder is an exchange member, ";
		print "select above, the category or categories of banners which should be displayed on ";
		print "his pages.)</EM></SMALL>";
		print "</FONT></SMALL></TD></TR>\n";
	}
	else {
		print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Zone(s):</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ";
		print "NAME=zone VALUE=\"$zone\" SIZE=25>";
		print "</FONT><FONT $fontspec><BR><SMALL><EM>(List the zones -- or &quot;target categories&quot; -- ";
		print "in which this banner should be displayed.)</EM></SMALL>";
		print "</FONT></SMALL></TD></TR>\n";
	}
	print "<TR><TD><SMALL><FONT $fontspec>Password:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=pass VALUE=\"$pass\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "<TR><TD COLSPAN=2><SMALL><FONT $fontspec>",
	  "<STRONG>II. Banner Details</STRONG>: ",
	  "The following information will be used to generate ",
	  "the account's banner links.</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Site URL:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=url VALUE=\"$url\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Banner URL(s):</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\">",
	  "<TEXTAREA COLS=70 ROWS=5 NAME=image WRAP=VIRTUAL>",
	  "$image</TEXTAREA>",
	  "</FONT><FONT $fontspec><BR><SMALL><EM>(Input more than one banner URL only if you don't want ",
	  "distinct performance data for each banner. If you <EM>do</EM> want to know ",
	  "individually how each banner performs, create a distinct ",
	  "account for each one.)</EM></SMALL>",
	  "</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Link Attributes:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=target VALUE=\"$target\" SIZE=50>",
	  "</FONT><FONT $fontspec><BR><SMALL><EM>(Select TARGET or other attributes -- example: TARGET=&quot;_blank&quot; -- that should be included ",
	  "in the banner's link code.)</EM></SMALL>",
	  "</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Banner Width:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=width VALUE=\"$width\" SIZE=5> </FONT><FONT $fontspec>pixels</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Banner Height:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=height VALUE=\"$height\" SIZE=5> </FONT><FONT $fontspec>pixels</FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>Border:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=border VALUE=\"$border\" SIZE=5></FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>ALT Text:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=alt VALUE=\"$alt\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>Link Text:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=text VALUE=\"$text\" SIZE=50>",
	  "</FONT><FONT $fontspec><BR><INPUT TYPE=RADIO NAME=texttype VALUE=T";
	if ($texttype eq "T") { print " CHECKED"; }
	print "> Above Banner <INPUT TYPE=RADIO NAME=texttype VALUE=B";
	if ($texttype eq "B") { print " CHECKED"; }
	print "> Below Banner</FONT></SMALL></TD></TR>\n",
	  "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "<TR><TD COLSPAN=2><SMALL><FONT $fontspec>",
	  "<STRONG>III. &quot;Raw Mode&quot; Information</STRONG>: ",
	  "If you so choose, you can specify below <EM>exactly</EM> ",
	  "how a banner is to appear on your pages. <EM>Only use this option if you're sure ",
	  "you know what you're doing!</EM> Anything input here will appear on your pages <EM>exactly</EM> ",
	  "as you enter it here; ",
	  "the information in section II will only be used if this information <EM>cannot</EM> be displayed ",
	  "(usually because a banner was called from an IMG tag rather than from an SSI, IFRAME or JavaScript tag).</EM></FONT></SMALL></TD></TR>\n",
	  "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>&quot;Raw&quot; HTML:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\">",
	  "<TEXTAREA COLS=70 ROWS=10 NAME=raw WRAP=VIRTUAL>";
	$raw =~ s/&lt;NLB&gt;/\n/g;
	print "$raw</TEXTAREA></FONT></SMALL></TD></TR>\n",
	  "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "</TABLE>\n",
	  "<P><INPUT TYPE=HIDDEN NAME=editad ",
	  "VALUE=\"$AccountName\">\n",
	  "<INPUT TYPE=HIDDEN NAME=start VALUE=\"$start\">\n",
	  "<INPUT TYPE=HIDDEN NAME=password ",
	  "VALUE=$INPUT{'password'}>\n",
	  "Check here to reset account exposures &amp; clicks: ",
	  "<INPUT TYPE=CHECKBOX NAME=\"resetadvert\">";
	if ($mailprog && (-s "$adverts_dir/welcome.txt")) {
		print "<BR>Check here to send a welcome letter: ",
		  "<INPUT TYPE=CHECKBOX NAME=\"welcomeletter\">";
	}
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<P><INPUT TYPE=SUBMIT NAME=editfinal ";
	print "VALUE=\"$text{'2200'}\">\n";
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		if ($mailprog && (-s "$adverts_dir/reject.txt") && $email) {
			print "<P>Check here to send a rejection letter: ",
			  "<INPUT TYPE=CHECKBOX NAME=\"rejectionletter\" VALUE=\"$email\">";
		}
		print "<P><INPUT TYPE=SUBMIT NAME=del ";
		print "VALUE=\"$text{'2201'}\"> ";
		print "<INPUT TYPE=HIDDEN NAME=delad ";
		print "VALUE=\"$AccountName\">\n";
	}
	print "</CENTER></FORM>\n";
	&Footer;
}

sub UserEdit {
	$AccountName = $INPUT{'reviewone'};
	&CheckName;
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		foreach $line (@lines) {
			$line =~ s/&/&amp;/g;
			$line =~ s/>/&gt;/g;
			$line =~ s/</&lt;/g;
			$line =~ s/"/&quot;/g;
		}
		($pass,$username,$email,$comments) = @lines;
		unless ($INPUT{'password'} eq $pass) {
			&ConfirmAdminPassword(2);
		}
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		foreach $line (@lines) {
			$line =~ s/&/&amp;/g;
			$line =~ s/>/&gt;/g;
			$line =~ s/</&lt;/g;
			$line =~ s/"/&quot;/g;
		}
		($max,$shown,$visits,$url,$image,$height,$width,
		  $alt,$nada,$text,$start,$weight,$zone,
		  $border,$target,$raw,$displayratio,$nada,$nada,
		  $displayzone,$clicksfrom) = @lines;
	}
	else {
		$pass = $INPUT{'password'};
	}
	unless ($url) { $url = "http://"; }
	$image =~ s/\|/\n/g;
	unless ($image) { $image = "http://"; }
	&Header("$text{'1000'}","$text{'1001'}");
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<P ALIGN=CENTER><BIG><STRONG>$text{'5000'} ",
	  "<EM>$AccountName</EM> $text{'5001'}:",
	  "</STRONG></BIG>\n",
	  "<P><CENTER><TABLE CELLPADDING=3>\n",
	  "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>$text{'5100'}:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=username VALUE=\"$username\" SIZE=50></FONT></SMALL></TD></TR>\n",
	  "<TR><TD><SMALL><FONT $fontspec>$text{'5101'}:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=email VALUE=\"$email\" SIZE=50></FONT></SMALL></TD></TR>\n";
	unless ($NoBanners) {
		print "<TR><TD><SMALL><FONT $fontspec>$text{'5102'}:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
		  "NAME=url VALUE=\"$url\" SIZE=50></FONT></SMALL></TD></TR>\n";
		unless ($UserUploadDir && $RequireUpload && !$cryptword) {
			print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>$text{'5103'}:</FONT></SMALL></TD><TD><SMALL><FONT FACE=\"Courier\">",
			  "<TEXTAREA COLS=70 ROWS=5 NAME=image WRAP=VIRTUAL>",
			  "$image</TEXTAREA>";
			if ($UserUploadDir && !(-s "$adverts_dir/$subdir/$AccountName.txt")) {
				print "</FONT><FONT $fontspec><BR><SMALL><EM>$text{'5200'}</EM></SMALL>";
			}
		}
		else {
			print "<TR><TD></TD><TD><SMALL><FONT $fontspec>$text{'5201'}</EM>";
		}
		print "</FONT></SMALL></TD></TR>\n";
	}
	if (@zones) {
		print "<TR><TD VALIGN=TOP><SMALL><FONT $fontspec>$text{'5104'}:</FONT></SMALL></TD>";
		unless ($NoBanners) {
			print "<TD><SMALL><FONT $fontspec>";
			foreach $setzone (sort (@zones)) {
				print "<INPUT TYPE=CHECKBOX NAME=zone ";
				print "VALUE=\"$setzone\"";
				if ($zone =~ /\s$setzone\s/) {
					print " CHECKED";
				}
				print ">$setzone\n<BR>";
			}
			print "<SMALL><EM>$text{'5300'}</EM></SMALL>";
			print "</FONT></SMALL></TD></TR>\n";
			print "<TR><TD></TD>";
		}
		print "<TD><SMALL><FONT $fontspec>";
		$displayzone = "\+".$displayzone."\+";
		foreach $setzone (sort (@zones)) {
			print "<INPUT TYPE=CHECKBOX NAME=displayzone ";
			print "VALUE=\"$setzone\"";
			if ($displayzone =~ /\+$setzone\+/) {
				print " CHECKED";
			}
			print ">$setzone\n<BR>";
		}
		print "<SMALL><EM>$text{'5301'}</EM></SMALL>";
		print "</FONT></SMALL></TD></TR>\n";
	}
	print "<TR><TD COLSPAN=2><HR WIDTH=50%></TD></TR>\n",
	  "</TABLE>\n",
	  "<P><INPUT TYPE=HIDDEN NAME=editad ",
	  "VALUE=\"$AccountName\">\n",
	  "<INPUT TYPE=HIDDEN NAME=password ",
	  "VALUE=$INPUT{'password'}>\n",
	  "<INPUT TYPE=HIDDEN NAME=pass VALUE=\"$pass\">\n";
	if ($AllowUserEdit && $INPUT{'newuser'}) {
		print "<INPUT TYPE=HIDDEN NAME=newuser ";
		print "VALUE=$INPUT{'newuser'}>\n";
	}
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<P><INPUT TYPE=SUBMIT NAME=editfinal ";
	print "VALUE=\"$text{'2200'}\">\n";
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		print "<P><INPUT TYPE=SUBMIT NAME=del ";
		print "VALUE=\"$text{'2201'}\"> ";
		print "<INPUT TYPE=HIDDEN NAME=delad ";
		print "VALUE=\"$AccountName\">\n";
	}
	print "</CENTER></FORM>\n";
	print "<P><HR><P>$text{'5400'}\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<CENTER><P>$text{'5401'}: <FONT FACE=\"Courier\"><INPUT TYPE=TEXT ",
	  "NAME=pass VALUE=\"$pass\" SIZE=50></FONT>\n",
	  "<P><INPUT TYPE=HIDDEN NAME=reviewone ",
	  "VALUE=\"$AccountName\">\n",
	  "<INPUT TYPE=HIDDEN NAME=password ",
	  "VALUE=$INPUT{'password'}>\n";
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<P><INPUT TYPE=SUBMIT NAME=newuserpassword ";
	print "VALUE=\"$text{'5402'}\"></CENTER></FORM>\n";
	if ($UserUploadDir && (-s "$adverts_dir/$subdir/$AccountName.txt")) {
		print "<P><HR><P>$text{'5500'}</EM>\n";
		print "<FORM ENCTYPE=\"multipart/form-data\" METHOD=POST ACTION=$admin_cgi>\n",
		  "<CENTER><P>$text{'5501'}: <FONT FACE=\"Courier\"><INPUT TYPE=FILE NAME=\"banner\" SIZE=35></FONT>",
		  "<P><INPUT TYPE=HIDDEN NAME=reviewone ",
		  "VALUE=\"$AccountName\">\n",
		  "<INPUT TYPE=HIDDEN NAME=password ",
		  "VALUE=$INPUT{'password'}>\n";
		if ($cryptword) {
			print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
		}
		print "<P><INPUT TYPE=SUBMIT NAME=uploadbanner ";
		print "VALUE=\"$text{'5502'}\"></CENTER></FORM>\n";
	}
	&Footer;
}

sub NewUserPassword {
	$AccountName = $INPUT{'reviewone'};
	&CheckName;
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		($pass) = $lines[0];
		unless ($INPUT{'password'} eq $pass) {
			&ConfirmAdminPassword(2);
		}
	}
	else {
		&Header("$text{'9000'}","$text{'9050'}");
		print "<P ALIGN=CENTER>$text{'9051'} ";
		print "<STRONG>&quot;$AccountName&quot;</STRONG> $text{'9052'}\n";
		&Footer;
	}
	$lines[0] = $INPUT{'pass'};
	$INPUT{'password'} = $INPUT{'pass'};
	&ADVLockOpen (DISPLAY, "$subdir/$AccountName.dat","x");
	seek (DISPLAY,0,0);
	foreach $key (0..3) {
		print DISPLAY "$lines[$key]\n";
	}
	truncate (DISPLAY,tell(DISPLAY));
	&ADVLockClose (DISPLAY, "$AccountName.dat");
	&reviewone;
}

sub UploadBanner {
	$AccountName = $INPUT{'reviewone'};
	&CheckName;
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		$pass = $lines[0];
		unless ($INPUT{'password'} eq $pass) {
			&ConfirmAdminPassword(2);
		}
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		$image = $lines[4];
	}
	else {
		&Header("$text{'9000'}","$text{'9050'}");
		print "<P ALIGN=CENTER>$text{'9051'} ";
		print "<STRONG>&quot;$AccountName&quot;</STRONG> $text{'9052'}\n";
		&Footer;
	}
	if ($BannerType eq "GIF") { $bannername = "$AccountName.gif"; }
	elsif ($BannerType eq "JPG") { $bannername = "$AccountName.jpg"; }
	else {
		&Header("$text{'9000'}","$text{'9060'}");
		print "<P ALIGN=CENTER>$text{'9061'}\n";
		&Footer;
	}
	if (length($INPUT{'BannerFile'}) > ($MaxBannerSize*1024)) {
		&Header("$text{'9000'}","$text{'9070'}");
		print "<P ALIGN=CENTER>$text{'9071'} ";
		print "<STRONG>$MaxBannerSize</STRONG> $text{'9072'} ";
		print int((length($INPUT{'BannerFile'})/1024)+.5)," $text{'9073'}\n";
		&Footer;
	}	
	unless (open (BANNER,">$UserUploadDir/$bannername")) {
		&Header("$text{'9000'}","$text{'9080'}");
		print "<P ALIGN=CENTER>$text{'9081'}\n";
		&Footer;
	}
	binmode BANNER;
	print BANNER $INPUT{'BannerFile'};
	close (BANNER);
	$image = "$UserUploadURL/$bannername";
	&ADVLockOpen (DISPLAY, "$subdir/$AccountName.txt","x");
	seek (DISPLAY,0,0);
	$lines[4] = $image;
	foreach $key (0..20) {
		print DISPLAY "$lines[$key]\n";
	}
	truncate (DISPLAY,tell(DISPLAY));
	&ADVLockClose (DISPLAY, "$AccountName.txt");
	$PresenceCheck = 0;
	unless ($cryptword || !($RequireAdminApproval)) {
		&ADVLockOpen (COUNT, "adlist.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek(COUNT, 0, 0);
		foreach $line (@lines) {
			if ($line eq $AccountName) { $PresenceCheck = 1; }
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT, "adlist.txt");
	}
	if ($PresenceCheck) {
		$PresenceCheck = 0;
		&ADVLockOpen (COUNT, "adnew.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek(COUNT, 0, 0);
		foreach $line (@lines) {
			if ($line eq $AccountName) { $PresenceCheck = 1; }
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		unless ($PresenceCheck) {
			print COUNT "$AccountName\n";
			&SendMail($email_address,"admin");
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT, "adnew.txt");
	}
	&reviewone;
}

sub editgroup {
	&ConfirmAdminPassword(1);
	$AccountName = $INPUT{'editgroup'};
	&CheckName;
	if (-s "$adverts_dir/$AccountName.grp") {
		open (DISPLAY, "<$adverts_dir/$AccountName.grp");
		@lines = <DISPLAY>;
		close (DISPLAY);
		chomp (@lines);
		$grouppassword = $lines[0];
		$adverts = join(' ',@lines);
	}
	&Header("$text{'1000'}","$text{'1001'}");
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<P><CENTER><BIG><STRONG>Info for the ";
	print "<EM>$AccountName</EM> Group:";
	print "</STRONG></BIG>\n";
	print "<P>Select the adverts to be included in this group:\n";
	open (COUNT, "<$adverts_dir/adlist.txt");
	@lines = <COUNT>;
	close (COUNT);
	chomp (@lines);
	@sortedlines = sort (@lines);
	$size = @lines;
	if ($size > 10) { $size = 10; }
	print "<P><FONT FACE=\"Courier\"><SELECT NAME=groupadverts MULTIPLE ";
	print "SIZE=$size>\n";
	foreach $advertiser (@sortedlines) {
		next if (length($advertiser) < 1);
		print "<OPTION VALUE=\"$advertiser\"";
		if ($adverts && ($adverts =~ $advertiser)) {
			print " SELECTED";
		}
		print "> $advertiser ";
	}
	print "</SELECT></FONT>\n";
	print "<P>Password: <FONT FACE=\"Courier\"><INPUT TYPE=TEXT ";
	print "NAME=pass VALUE=\"$grouppassword\" SIZE=25></FONT>\n";
	print "<P><INPUT TYPE=HIDDEN NAME=editgroup ";
	print "VALUE=\"$AccountName\">\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	print "<INPUT TYPE=SUBMIT NAME=editgroupfinal ";
	print "VALUE=\"Add/Edit Group\">\n";
	if (-s "$adverts_dir/$AccountName.grp") {
		print "<P><INPUT TYPE=SUBMIT NAME=delgroup ";
		print "VALUE=\"Delete Group\"> ";
		print "<INPUT TYPE=HIDDEN NAME=delgroupname ";
		print "VALUE=\"$AccountName\">\n";
	}
	print "</CENTER></FORM>\n";
	&Footer;
}

sub del {
	$AccountName = $INPUT{'delad'};
	&CheckName;
	unless (-s "$adverts_dir/$subdir/$AccountName.txt") {
		&Header("$text{'9000'}","$text{'9050'}");
		print "<P ALIGN=CENTER>$text{'9051'} ";
		print "<STRONG>&quot;$AccountName&quot;</STRONG> $text{'9052'}\n";
		&Footer;
	}
	&ConfirmUserPassword;
	&Header("$text{'1000'}","$text{'1001'}");
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	print "<P><CENTER>Are you <EM>sure</EM> you want to delete the ";
	print "<STRONG>$AccountName</STRONG> account?\n";
	print "<INPUT TYPE=HIDDEN NAME=delad VALUE=$AccountName>\n";
	if ($INPUT{'rejectionletter'}) {
		print "<INPUT TYPE=HIDDEN NAME=rejectionletter ";
		print "VALUE=\"$INPUT{'rejectionletter'}\">\n";
	}
	if ($cryptword) {
		print "<INPUT TYPE=HIDDEN NAME=admincheck VALUE=1>\n";
	}
	print "<INPUT TYPE=SUBMIT NAME=delfinal VALUE=\"Yes\">\n";
	print "</CENTER></FORM>\n";
	&Footer;
}

sub delgroup {
	$AccountName = $INPUT{'delgroupname'};
	&CheckName;
	unless (-s "$adverts_dir/$AccountName.grp") {
		&Header("$text{'9000'}","$text{'9050'}");
		print "<P ALIGN=CENTER>$text{'9051'} ";
		print "<STRONG>&quot;$AccountName&quot;</STRONG> $text{'9052'}\n";
		&Footer;
	}
	&ConfirmAdminPassword(1);
	&Header("$text{'1000'}","$text{'1001'}");
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<P><CENTER>Are you <EM>sure</EM> you want to delete the ";
	print "<STRONG>$AccountName</STRONG> group? ";
	print "<INPUT TYPE=HIDDEN NAME=delgroupname ";
	print "VALUE=$AccountName>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	print "<INPUT TYPE=SUBMIT NAME=delgroupfinal VALUE=\"Yes\">\n";
	print "</CENTER></FORM>\n";
	print "<P>(Please note that deleting the group will ";
	print "<EM>not</EM> delete or otherwise affect the adverts ";
	print "themselves. Only the ability to view all their stats ";
	print "on a single page will be gone!)\n";
	&Footer;
}

sub newpass {
	unless ($INPUT{'passad'} && ($INPUT{'passad'} eq $INPUT{'passad2'})) {
		&Header("$text{'9000'}","$text{'9024'}");
		print "<P ALIGN=CENTER>$text{'9025'}\n";
		&Footer;
	}
	open (PASSWORD, "<$adverts_dir/adpassword.txt");
	$password = <PASSWORD>;
	close (PASSWORD);
	chomp ($password);
	if ($password) {
		if ($INPUT{'password'}) {
			$newpassword = crypt($INPUT{'password'}, "aa");
		}
		else {
			&Header("$text{'9000'}","$text{'9020'}");
			print "<P ALIGN=CENTER>$text{'9021'}\n";
			&Footer;
		}
		unless ($newpassword eq $password) {
			&Header("$text{'9000'}","$text{'9022'}");
			print "<P ALIGN=CENTER>$text{'9023'}\n";
			&Footer;
		}
	}
	$newpassword = crypt($INPUT{'passad'}, "aa");
	&ADVLockOpen (PASSWORD, "adpassword.txt");
	seek (PASSWORD,0,0);
	print PASSWORD "$newpassword";
	truncate (PASSWORD,tell(PASSWORD));
	&ADVLockClose (PASSWORD,"adpassword.txt");
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER>Your administrative password ";
	print "has been set.\n";
	$INPUT{'password'} = $INPUT{'passad'};
	&LinkBack;
	&Footer;
}

sub resetcount {
	&ConfirmAdminPassword(1);
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			$DBMList{'adcount.txt'} = "1\n0\n$time";
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

sub editfinal {
	$AccountName = $INPUT{'editad'};
	&CheckName;
	unless (!(-s "$adverts_dir/$subdir/$AccountName.txt")
	  && $AllowUserEdit && $INPUT{'newuser'}) {
		&ConfirmUserPassword;
	}
	if (-s "$adverts_dir/$subdir/$AccountName.txt") {
		open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.txt");
		@advertlines = <DISPLAY>;
		close (DISPLAY);
		chomp (@advertlines);
		if ($cryptword) {
			($shown,$visits,$start,$displaycount,$clicksfrom)
			  = @advertlines[1,2,10,16,20];
			($other,$displaycount) = split(/\|/, $displaycount);
			($clicksfrom,$other) = split(/\|/, $clicksfrom);
			$comments = $INPUT{'comments'};
		}
		else {
			($max,$shown,$visits,$dmy,$dmy,$height,$width,
			  $alt,$dmy,$text,$start,$weight,$zone,
			  $border,$target,$raw,$displayratio,
			  $dmy,$dmy,$displayzone,$clicksfrom) = @advertlines;
			($max,$maxtype) = split(/\|/, $max);
			($text,$texttype) = split(/\|/, $text);
			($displayratio,$displaycount) = split(/\|/, $displayratio);
			($clicksfrom,$clicksratio) = split(/\|/, $clicksfrom);
			open (DISPLAY, "<$adverts_dir/$subdir/$AccountName.dat");
			@advertlines = <DISPLAY>;
			close (DISPLAY);
			chomp (@advertlines);
			$comments = $advertlines[3];
		}
	}
	elsif (!($cryptword)) {
		$maxtype = "E";
		$texttype = "B";
		$displayratio = $DefaultDisplayRatio;
		$clicksratio = $DefaultClicksRatio;
		$target = $DefaultLinkAttribute;
		$weight = $DefaultWeight;
		$border = $DefaultBorder;
		if ($NoBanners) {
			$INPUT{'url'} = "";
			$INPUT{'image'} = "";
			$INPUT{'zone'} = "";
		}
	}
	$INPUT{'email'} =~ s/\s//g;
	unless ($INPUT{'email'} =~ /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)|,|;|\//
	  || $INPUT{'email'} !~
	  /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/)
	  {
		$email = "$INPUT{'email'}";
	}
	$INPUT{'url'} =~ s/\s//g;
	unless ($INPUT{'url'} =~ /(\.\.)|(^\.)|(\/\/\.)/ ||
	  $INPUT{'url'} !~ /(.*\:\/\/.*\..*|mailto:.*@.*)/) {
		$url = $INPUT{'url'};
	}
	if ($UserUploadDir && $RequireUpload && !$cryptword) { $INPUT{'image'} = ""; }
	@image = split(/\cM|\n/,$INPUT{'image'});
	foreach $fauximage (@image) {
		$fauximage =~ s/\s//g;
		unless ($fauximage =~ /(\.\.)|(^\.)|(\/\/\.)/ ||
		  $fauximage !~ /.*\:\/\/.*\..*/) {
			$image = $image.$fauximage."|";
		}
	}
	chop ($image);
	$pass = $INPUT{'pass'};
	if ($cryptword) {
		$displayratio = $INPUT{'displayratio'};
		if ($displayratio < 1) { $displayratio = 0; }
		$clicksratio = $INPUT{'clicksratio'};
		if ($clicksratio < 1) { $clicksratio = 0; }
		$weight = int($INPUT{'weight'});
	}
	if ($INPUT{'zone'}) {
		$zone = $INPUT{'zone'};
		$zone =~ s/^\s+//;
		$zone =~ s/\s+$//;
		$zone =~ s/\s+/ /g;
	}
	if ($INPUT{'displayzone'}) {
		$displayzone = $INPUT{'displayzone'};
		$displayzone =~ s/^\s+//;
		$displayzone =~ s/\s+$//;
		$displayzone =~ s/\s+/\+/g;
	}
	if ($cryptword) {
		if ($INPUT{'purchtype'} eq "D") {
			if ($max) { ($mday,$mon,$year) = (localtime($max+($HourOffset*3600)))[3,4,5]; }
			else { ($mday,$mon,$year) = (localtime($time+($HourOffset*3600)))[3,4,5]; }
			$year += 1900;
			unless ($INPUT{'EndDateA'}) { $INPUT{'EndDateA'} = $mday; $INPUT{'EndDateB'} = $mon; }
			if ($INPUT{'EndDateC'} < 1990) { $INPUT{'EndDateC'} = $year; }
			$max = &rangedate($INPUT{'EndDateB'}+1,$INPUT{'EndDateA'}+1,$INPUT{'EndDateC'}-1900);
			$max -= 1;
		}
		else {
			$INPUT{'purch'} =~ s/[^-\d]//g;
			$max = $INPUT{'purch'};
		}
		$maxtype = $INPUT{'purchtype'};
		if (($max < 0) && ($maxtype ne "E")) { $max = 0; }
		if ($start) { ($mday,$mon,$year) = (localtime($start+($HourOffset*3600)))[3,4,5]; }
		else { ($mday,$mon,$year) = (localtime($time+($HourOffset*3600)))[3,4,5]; }
		$year += 1900;
		unless ($INPUT{'StartDateA'}) { $INPUT{'StartDateA'} = $mday; $INPUT{'StartDateB'} = $mon; }
		if ($INPUT{'StartDateC'} < 1990) { $INPUT{'StartDateC'} = $year; }
		$start = &rangedate($INPUT{'StartDateB'}+1,$INPUT{'StartDateA'},$INPUT{'StartDateC'}-1900);
		$height = int($INPUT{'height'});
		$width = int($INPUT{'width'});
		$alt = $INPUT{'alt'};
		$INPUT{'text'} =~ s/^\s+//;
		$INPUT{'text'} =~ s/\s+$//;
		$INPUT{'text'} =~ s/\s+/ /g;
		$text = $INPUT{'text'};
		$texttype = $INPUT{'texttype'};
		$border = int($INPUT{'border'});
		$target = $INPUT{'target'};
		$INPUT{'raw'} =~ s/(\cM|\n)+/<NLB>/g;
		$raw = $INPUT{'raw'};
	}
	unless ($pass) {
		&Header("$text{'9000'}","$text{'9100'}");
		print "<P>$text{'9101'}\n";
		&Footer;
	}
	if ((($maxtype eq "C") || ($maxtype eq "D")) && ($displayratio || $clicksratio)) {
		&Header("$text{'9000'}","$text{'9110'}");
		print "<P>$text{'9111'}\n";
		&Footer;
	}
	$PresenceCheck = 0;
	if (-s "$adverts_dir/adnew.txt") {
		&ADVLockOpen (COUNT, "adnew.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek(COUNT, 0, 0);
		foreach $line (@lines) {
			if ($line eq $AccountName) { $PresenceCheck = 1; }
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT, "adnew.txt");
	}
	if (-s "$adverts_dir/adlist.txt") {
		&ADVLockOpen (COUNT, "adlist.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek(COUNT, 0, 0);
		foreach $line (@lines) {
			if ($line eq $AccountName) { $PresenceCheck = 1; }
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT, "adlist.txt");
	}
	if ($INPUT{'resetadvert'}
	  || (!$PresenceCheck && !$shown)) {
		$shown = 0;
		$visits = 0;
		unless ($start > $time) { $start = $time; }
		$displaycount = 0;
		$clicksfrom = 0;
		unlink ("$adverts_dir/$subdir/$AccountName.log");
	}
	if ($maxtype eq "N") { $max = 0; }
	&ADVLockOpen (DISPLAY, "$subdir/$AccountName.txt","x");
	seek (DISPLAY,0,0);
	print DISPLAY "$max|$maxtype\n";
	print DISPLAY "$shown\n";
	print DISPLAY "$visits\n";
	print DISPLAY "$url\n";
	print DISPLAY "$image\n";
	print DISPLAY "$height\n";
	print DISPLAY "$width\n";
	print DISPLAY "$alt\n\n";
	print DISPLAY "$text|$texttype\n";
	print DISPLAY "$start\n";
	print DISPLAY "$weight\n";
	print DISPLAY " $zone \n";
	print DISPLAY "$border\n";
	print DISPLAY "$target\n";
	print DISPLAY "$raw\n";
	print DISPLAY "$displayratio|$displaycount\n\n\n";
	print DISPLAY "$displayzone\n";
	print DISPLAY "$clicksfrom|$clicksratio\n";
	if ((@advertlines > 21) && !($INPUT{'resetadvert'})) {
		foreach $key (21..(@advertlines-1)) {
			print DISPLAY "$advertlines[$key]\n";
		}
	}
	truncate (DISPLAY,tell(DISPLAY));
	&ADVLockClose (DISPLAY, "AccountName.txt");
	&ADVLockOpen (DISPLAY, "$subdir/$AccountName.dat","x");
	seek (DISPLAY,0,0);
	print DISPLAY "$pass\n";
	print DISPLAY "$INPUT{'username'}\n";
	print DISPLAY "$email\n";
	print DISPLAY "$comments\n";
	truncate (DISPLAY,tell(DISPLAY));
	&ADVLockClose (DISPLAY, "AccountName.dat");
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			if ($image) { $image = "X"; }
			if ($raw =~ /<SCRIPT/) { $raw = "J"; }
			elsif ($raw) { $raw = "X"; }
			$DBMList{$AccountName} = "$max|$maxtype\t$shown\t$visits\t$image\t$start\t$weight\t";
			$DBMList{$AccountName} .= " $zone \t$raw\t$displayratio|$displaycount\t$clicksfrom|$clicksratio";
			&ADVDBMClose;
		}
	}
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	if ($cryptword || !($RequireAdminApproval)) {
		&ADVLockOpen (COUNT, "adlist.txt");
		@adlist = <COUNT>;
		seek (COUNT,0,0);
		foreach $adlist (@adlist) {
			print COUNT "$adlist";
		}
		print COUNT "$AccountName\n";
		truncate (COUNT,tell(COUNT));
		&ADVLockClose (COUNT, "adlist.txt");
	}
	else {
		&ADVLockOpen (COUNT, "adnew.txt");
		@adnew = <COUNT>;
		seek (COUNT,0,0);
		foreach $adnew (@adnew) {
			print COUNT "$adnew";
		}
		print COUNT "$AccountName\n";
		truncate (COUNT,tell(COUNT));
		&ADVLockClose (COUNT, "adnew.txt");
		&SendMail($email_address,"admin");
	}
	$INPUT{'reviewone'} = $AccountName;
	&reviewone;
}

sub rangedate {
	($perp_mon,$perp_day,$perp_year) = @_;
	%day_counts =
	  (1,0,2,31,3,59,4,90,5,120,6,151,7,181,
	  8,212,9,243,10,273,11,304,12,334);
	$perp_days = (($perp_year-69)*365)+(int(($perp_year-69)/4));
	$perp_days += $day_counts{$perp_mon};
	if ((int(($perp_year-68)/4) eq (($perp_year-68)/4))
	  && ($perp_mon>2)) {
		$perp_days++;
	}
	$perp_days += $perp_day;
	$perp_days -= 366;
	$perp_secs = ($perp_days*86400)+18000;
	$hour = (localtime($perp_secs))[2];
	if ($hour>0) { $perp_secs-=3600; }
	$perp_secs -= ($HourOffset*3600);
	return $perp_secs;
}

sub editgroupfinal {
	&ConfirmAdminPassword(1);
	$AccountName = $INPUT{'editgroup'};
	&CheckName;
	$pass = $INPUT{'pass'};
	@groupadverts = split(' ',$INPUT{'groupadverts'});
	unless ($pass && (@groupadverts > 0)) {
		&Header("$text{'9000'}","$text{'9105'}");
		print "<P>$text{'9106'} ";
		print "<STRONG>$AccountName</STRONG> $text{'9107'}\n";
		&Footer;
	}
	&ADVLockOpen (GROUP, "$AccountName.grp");
	seek (GROUP,0,0);
	print GROUP "$pass\n";
	foreach $advert (@groupadverts) {
		print GROUP "$advert\n";
	}
	truncate (GROUP,tell(GROUP));
	&ADVLockClose (GROUP,"$AccountName.grp");
	$PresenceCheck = 0;
	if (-s "$adverts_dir/groups.txt") {
		open (COUNT, "<$adverts_dir/groups.txt");
		@lines = <COUNT>;
		close (COUNT);
		chomp (@lines);
	}
	foreach $line (@lines) {
		if ($line eq $AccountName) { $PresenceCheck = 1; }
	}
	unless ($PresenceCheck) {
		&ADVLockOpen (COUNT, "groups.txt");
		@groups = <COUNT>;
		seek (COUNT,0,0);
		foreach $group (@groups) {
			print COUNT "$group";
		}
		print COUNT "$AccountName\n";
		truncate (COUNT,tell(COUNT));
		&ADVLockClose (COUNT,"groups.txt");
	}
	&Header("$text{'1000'}","$text{'1001'}");
	print "<P ALIGN=CENTER>";
	print "The <STRONG>$AccountName</STRONG> group now includes ";
	print "the following adverts:\n";
	print "<P ALIGN=CENTER><STRONG>";
	foreach $advert (@groupadverts) {
		print "$advert ";
	}
	print "</STRONG>\n";
	&LinkBack;
	&Footer;
}

sub delfinal {
	$AccountName = $INPUT{'delad'};
	&CheckName;
	&ConfirmUserPassword;
	opendir (FILES,"$adverts_dir/$subdir");
	@files = readdir(FILES);
	closedir (FILES);
	foreach $file (@files) {
		$_ = $file; /^(.+)$/; $file = $1;
		unlink ("$adverts_dir/$subdir/$file");
	}
	rmdir ("$adverts_dir/$subdir");
	if ($UserUploadDir) {
		unlink ("$UserUploadDir/$AccountName.gif");
		unlink ("$UserUploadDir/$AccountName.jpg");
	}
	if (-s "$adverts_dir/adlist.txt") {
		&ADVLockOpen (COUNT, "adlist.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek (COUNT,0,0);
		foreach $line (@lines) {
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT,"adlist.txt");
	}
	if (-s "$adverts_dir/adnew.txt") {
		&ADVLockOpen (COUNT, "adnew.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek (COUNT,0,0);
		foreach $line (@lines) {
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT,"adnew.txt");
	}
	&ADVLockOpen (DBMLIST, "dbmlist.txt");
	if ($ADVlockerror) { &Error_DBM; }
	else {
		&ADVDBMOpen;
		if ($ADVdbmerror) { &Error_DBM; }
		else {
			delete ($DBMList{$AccountName});
			&ADVDBMClose;
		}
	}
	&ADVLockClose (DBMLIST, "dbmlist.txt");
	if ($INPUT{'rejectionletter'}) {
		open (REJECT, "<$adverts_dir/reject.txt");
		$body = "";
		while (defined($line = <REJECT>)) {
			$body .= $line;
		}
		close (REJECT);
		&SendMail($INPUT{'rejectionletter'},"reject");
	}
	if ($cryptword) {
		if ($AdminDisplaySetup) { &defineview; }
		else {
			$INPUT{'whichtype'} = "pending established groups";
			$INPUT{'whichtime'} = "active expired disabled";
			$INPUT{'whichzone'} = "";
			&reviewall;
		}
	}
	&userintro;
}

sub delgroupfinal {
	&ConfirmAdminPassword(1);
	$AccountName = $INPUT{'delgroupname'};
	&CheckName;
	unlink ("$adverts_dir/$AccountName.grp");
	if (-s "$adverts_dir/groups.txt") {
		&ADVLockOpen (COUNT, "groups.txt");
		@lines = <COUNT>;
		chomp (@lines);
		seek (COUNT,0,0);
		foreach $line (@lines) {
			unless (($line eq $AccountName) || (length($line) < 1)) {
				print COUNT "$line\n";
			}
		}
		truncate (COUNT, tell(COUNT));
		&ADVLockClose (COUNT,"groups.txt");
	}
	if ($AdminDisplaySetup) { &defineview; }
	else {
		$INPUT{'whichtype'} = "pending established groups";
		$INPUT{'whichtime'} = "active expired disabled";
		$INPUT{'whichzone'} = "";
		&reviewall;
	}
}

sub LinkBack {
	print "<P><CENTER>\n";
	print "<FORM METHOD=POST ACTION=$admin_cgi>\n";
	print "<INPUT TYPE=HIDDEN NAME=password ";
	print "VALUE=$INPUT{'password'}>\n";
	print "<INPUT TYPE=HIDDEN NAME=reviewone ";
	print "VALUE=\"Define View\">\n";
	print "<INPUT TYPE=SUBMIT ";
	print "VALUE=\"Reload Account Index\">\n";
	print "</FORM></CENTER>\n";
}

sub FindSpecifics {
	$DOMAIN = $ENV{'HTTP_HOST'};
	$ROOT_URL = "http://$DOMAIN";
	$FULL_PATH = $0;
	$FULL_PATH =~ tr/\\/\//;
	if ($ENV{'PATH_INFO'}) { $URI_PATH = $ENV{'PATH_INFO'}; }
	elsif ($ENV{'SCRIPT_NAME'}) { $URI_PATH = $ENV{'SCRIPT_NAME'}; }
	$URI_DIR = $URI_PATH;
	$URI_DIR =~ s/^(.*?)\/[^\/\\]*\.[^\.\/]+$/$1/;
	unless($URI_DIR){ $URI_DIR = "/"; }
	if ($ENV{'DOCUMENT_ROOT'}) { $DOC_ROOT = $ENV{'DOCUMENT_ROOT'}; }
	elsif ($ENV{'PWD'}) { $DOC_ROOT = $ENV{'PWD'}; }
	else { $DOC_ROOT = $ENV{'PATH_TRANSLATED'}; }
	$DOC_ROOT =~ tr/\\/\//;
	$DOC_ROOT =~ s/^(.+?)$URI_PATH$/$1/;
	$DOC_ROOT =~ s/^(.+?)$URI_DIR(\/)*$/$1/;
	$DOC_ROOT =~ s/^(.+?)\/$/$1/;
	$ENV{'DOCUMENT_ROOT'} = $DOC_ROOT;
	unless ($FULL_PATH =~ /^(\w\:)*\//) {
		if ($ENV{'SCRIPT_FILENAME'}) { $FULL_PATH = $ENV{'SCRIPT_FILENAME'}; }
		elsif ($ENV{'SCRIPT_NAME'} && $DOC_ROOT) { $FULL_PATH = "$DOC_ROOT$ENV{'SCRIPT_NAME'}"; }
	}
	$FILE_NAME = $FULL_PATH;
	$FILE_NAME =~ s/^(.*?\/*)([^\/\\]*(\.[^\.\/]+)*)$/$2/;
	$FILE_TYPE = $FILE_NAME;
	$FILE_TYPE =~ s/^(.*?)(\.[^\.\/]+)$/$2/;
	if ($ENV{'SCRIPT_URI'}) { $THIS_URL = $ENV{'SCRIPT_URI'}; }
	else { $THIS_URL = "$ROOT_URL$URI_PATH"; }
	$THIS_DIR = $URI_DIR;
	$THIS_DIR =~ s/^$ROOT_URL//i;
	$THIS_DIR = "$ENV{'DOCUMENT_ROOT'}/$THIS_DIR";
	$THIS_DIR =~ s/\/\//\//g;
	$THIS_DIR =~ s/\/$//;
	return ($DOMAIN,$DOC_ROOT,$THIS_URL,$FULL_PATH,$ROOT_URL,$URI_PATH,$URI_DIR,$FILE_NAME,$FILE_TYPE,$THIS_DIR);
}

sub SSI_Functions {
	my $PASSED = @_[0];
	my (@included,$included,$included_line,@EXECD,$execd);
	$PASSED =~ s/<!--\s*#\s*echo \s*var\s*=\s*"(.+?)"\s*-->/$ENV{$1}/i;
	if ($PASSED =~ s/<!--\s*#\s*include \s*virtual\s*=\s*"(.+?)"\s*-->//i) {
		open (INCLUDED,"$SSIvirtual$1");
		@included=<INCLUDED>;
		close (INCLUDED);
		foreach $included (@included) {
			$included =~ s/\n$//;
			&SSI_Functions($included);
			$PASSED = "";
		}
	}
	if ($PASSED =~ s/<!--\s*#\s*include \s*file\s*=\s*"(.+?)"\s*-->//i) {
		open (INCLUDED,"$SSIfile/$1");
		@included=<INCLUDED>;
		close (INCLUDED);
		foreach $included_line (@included) {
			$included_line =~ s/\n$//;
			&SSI_Functions($included_line);
			$PASSED = "";
		}
	}
	if ($PASSED =~ s/<!--\s*#\s*exec \s*cgi\s*=\s*"(.+?)"\s*-->//i) {
		open (EXECD,"$SSIvirtual$1");
		@EXECD = <EXECD>;
		close (EXECD);
		my $execd = join(/\n/, @EXECD);
		eval($execd);
		$PASSED = "";
	}
	if ($PASSED =~ s/<!--\s*#\s*exec \s*cmd\s*=\s*"(.+?)"\s*-->//i) {
		($cmd,$path) = split (/ /,$1);
		if ((-e $path) && ($cmd =~ /^perl$/i)) {
			$execute = $path;
			$ok = 1;
		} 
		elsif ((-e $cmd) && (!$path)) {
			$execute = $cmd;
			$ok = 1;
		}
		if ($ok) {
			open (EXECD, "$execute");
			@EXECD = <EXECD>;
			close (EXECD);
			$execd = join(/\n/, @EXECD);
			eval($execd);
		}
		$PASSED = "";
	}
	$PASSED =~ s/\n$//s;
	if ($PASSED) { print "$PASSED\n"; }
}

sub Header {
	local($title,$header) = @_;
	if ($ExchangeName) {
		$title =~ s/$text{'1000'}/$ExchangeName/g;
		$header =~ s/$text{'1000'}/$ExchangeName/g;
	}
	print "<HTML><HEAD><TITLE>$title</TITLE>\n";
	if ($MetaFile) {
		open (HEADLN,"$MetaFile");
		@headln = <HEADLN>;
		close (HEADLN);
		foreach $line (@headln) { &SSI_Functions($line); }
	}
	print "</HEAD><BODY $bodyspec><SMALL><FONT $fontspec>\n";
	if ($header_file) {
		open (HEADER,"<$header_file");
		@header = <HEADER>;
		close (HEADER);
		foreach $line (@header) { &SSI_Functions($line); }
	}
	print "<P><HR><H2 ALIGN=CENTER>$header</H2><P><HR>\n";
}

sub Footer {
	local($adminlog) = @_;
	if ($LogAdminAccesses && (length($adminlog)>7)) {
		($min,$hour,$mday,$mon,$year) = (localtime($time+($HourOffset*3600)))[1,2,3,4,5];
		$mon++;
		if ($mon<10) { $mon = "0".$mon; }
		if ($mday<10) { $mday = "0".$mday; }
		if ($year>99) { $year = $year-100; }
		if ($year<10) { $year = "0".$year; }
		if ($hour<10) { $hour = "0".$hour; }
		if ($min<10) { $min = "0".$min; }
		if (length($adminlog)>25) { $adminlog = substr($adminlog,0,25); }
		open (ADMINLOG,">>$adverts_dir/adminlog.txt");
		print ADMINLOG "$mon/$mday/$year $hour:$min  ";
		printf ADMINLOG "%-25s",$adminlog;
		print ADMINLOG "  $ENV{'REMOTE_ADDR'}";
		if ($ENV{'REMOTE_HOST'} ne $ENV{'REMOTE_ADDR'}) {
			print ADMINLOG " ($ENV{'REMOTE_HOST'})";
		}
		print ADMINLOG "\n";
		close (ADMINLOG);
	}
	print "<P ALIGN=CENTER><SMALL><EM>Maintained ";
	unless ($admin_name) { $admin_name = $email_address; }
	if ($admin_name) {
		print "by ";
		if ($email_address) { print "<A HREF=\"mailto:$email_address\">"; }
		print "$admin_name";
		if ($email_address) { print "</A>"; }
		print " ";
	}
	print "with <STRONG>";
	print "<A HREF=\"http://awsd.com/scripts/webadverts/\">";
	print "WebAdverts $version</A></STRONG>.</EM></SMALL>\n";
	if ($footer_file) {
		print "<P><HR>\n";
		open (FOOTER,"<$footer_file");
		@footer = <FOOTER>;
		close (FOOTER);
		foreach $line (@footer) { &SSI_Functions($line); }
	}
	print "</FONT></SMALL></BODY></HTML>\n";
	unless ($ADVUseLocking) { &ADVMasterLockClose; }
	reset 'A-Za-z';
	exit;
}

sub register {
	$register = $INPUT{'register'};
	$count = (length($register)-1);
	foreach $key (0..$count) {
		$fig = substr($register,$key,1); $fig = ord($fig); $checksum += $fig;
	}
	unless (($count==5) && ($checksum==688)) {
		&Header("$text{'9000'}","Incorrect Code!");
		print "<P ALIGN=CENTER>Sorry, but the registration code ";
		print "you entered is incorrect!\n";
		print "<P ALIGN=CENTER><A HREF=\"$admin_cgi?admin\">Back to Admin</A>\n";
		&Footer;
	}
	open (REGISTER, ">$adverts_dir/register.txt");
	print REGISTER "$INPUT{'register'}";
	close (REGISTER);
	&Header("$text{'1000'}","Thanks For Registering!");
	print "<P ALIGN=CENTER>Your support is appreciated!\n";
	print "<P ALIGN=CENTER><A HREF=\"$admin_cgi?admin\">Back to Admin</A>\n";
	&Footer;
}

sub reginfo {
	&Header("$text{'1000'}","Registration Information");
	print "<P>WebAdverts is distributed as shareware. While you ",
	  "are free to modify and use it as you see fit, any usage ",
	  "should be registered. The registration fee is just \$50 ",
	  "(US). Payment should be sent via check or money order ",
	  "to <STRONG>Darryl C. Burgdorf, Affordable Web Space ",
	  "Design, 3524 Pacific Street, Omaha NE 68105</STRONG>.\n",
	  "<P>(If you happen to live in a country other than the ",
	  "United States, you can write a check in your local ",
	  "currency for the equivalent of \$57.50. That will cover ",
	  "the \$50 registration fee and the \$7.50 ",
	  "service fee which my bank charges. Please do ",
	  "<STRONG><EM>not</EM></STRONG> write me a check ",
	  "in US funds drawn on a non-US bank; the service charge ",
	  "for those can be anywhere from \$10 to \$25!)\n",
	  "<P>Thank you for your support!\n",
	  "<P><CENTER>\n",
	  "<FORM METHOD=POST ACTION=$admin_cgi>\n",
	  "<INPUT TYPE=SUBMIT VALUE=\"Enter Registration Code:\">\n",
	  "<FONT FACE=\"Courier\"><INPUT TYPE=TEXT NAME=register SIZE=10></FONT>\n",
	  "</FORM></CENTER>\n";
	&Footer;
}

sub date_to_count {
	($perp_mon,$perp_day,$perp_year) = @_;
	%day_counts =
	  (1,0,2,31,3,59,4,90,5,120,6,151,7,181,
	  8,212,9,243,10,273,11,304,12,334);
	$perp_days = (($perp_year-93)*365)+(int(($perp_year-93)/4));
	$perp_days = $perp_days + $day_counts{$perp_mon};
	if ((int(($perp_year-92)/4) eq (($perp_year-92)/4))
	  && ($perp_mon>2)) {
		$perp_days++;
	}
	$perp_days = $perp_days + $perp_day;
}

sub count_to_date {
	local($perp_days) = @_;
	%day_counts =
	  (1,0,2,31,3,59,4,90,5,120,6,151,
	  7,181,8,212,9,243,10,273,11,304,12,334);
	$perp_year = (int(($perp_days-1)/1461))*4;
	$perp_days = $perp_days-(int(($perp_days-1)/1461)*1461);
	if ($perp_days == 1461) {
		$perp_year = 93+$perp_year+3;
		$perp_days = $perp_days-1095;
	}
	else {
		$perp_year = 93+$perp_year+(int(($perp_days-1)/365));
		$perp_days = $perp_days-(int(($perp_days-1)/365)*365);
	}
	foreach $key (sort ({$a <=> $b} keys %day_counts)) {
		$perp_count = $day_counts{$key};
		if ((int(($perp_year-92)/4) eq (($perp_year-92)/4))
		  && ($key>2)) {
			$perp_count++;
		}
		if ($perp_days > $perp_count) {
			$perp_mon = $key;
			$perp_subtract = $perp_count;
		}
	}
	$perp_day = $perp_days-$perp_subtract;
}

sub commas {
	local($_)=@_;
	1 while s/(.*\d)(\d\d\d)/$1,$2/;
	$_;
}

sub SendMail {
	local($To,$type) = @_;
	return unless $To;
	if ($type eq "admin") {
		$messagesubject = "$ExchangeName: New Account Waiting";
		$messagebody = "A new account, $AccountName, awaits approval!\n";
	}
	elsif ($type eq "getpass") {
		$messagesubject = "$ExchangeName: Password";
		$messagebody = "The password for the \"$AccountName\" account ";
		$messagebody .= "is \"$pass\" (case-sensitive).";
	}
	elsif ($type eq "welcome") {
		$messagesubject = "$ExchangeName: Welcome!";
		$messagebody = $body;
	}
	elsif ($type eq "reject") {
		$messagesubject = "$ExchangeName: Account Rejected";
		$messagebody = $body;
	}
	elsif ($type eq "groupmail") {
		$messagesubject = "$INPUT{'messagesubject'}";
		$messagebody = "$INPUT{'messagetext'}";
	}
	if ($mailprog eq "SMTP") {
		unless ($WEB_SERVER) { $WEB_SERVER = $ENV{'SERVER_NAME'}; }
		if (!$WEB_SERVER) { &Error_Mail; }
		unless ($SMTP_SERVER) {
			$SMTP_SERVER = "smtp.$WEB_SERVER";
			$SMTP_SERVER =~ s/^smtp\.[^.]+\.([^.]+\.)/smtp.$1/;
		}
#		local($AF_INET) = ($] > 5 ? AF_INET : 2);
#		local($SOCK_STREAM) = ($] > 5 ? SOCK_STREAM : 1);
		local($AF_INET) = 2; 
		local($SOCK_STREAM) = 1; 
		$, = ', ';
		$" = ', ';
		local($local_address) = (gethostbyname($WEB_SERVER))[4];
		local($local_socket_address) = pack('S n a4 x8', $AF_INET, 0, $local_address);
		local($server_address) = (gethostbyname($SMTP_SERVER))[4];
		local($server_socket_address) = pack('S n a4 x8', $AF_INET, '25', $server_address);
		local($protocol) = (getprotobyname('tcp'))[2];
		if (!socket(SMTP, $AF_INET, $SOCK_STREAM, $protocol)) { &Error_Mail; }
		bind(SMTP, $local_socket_address);
		if (!(connect(SMTP, $server_socket_address))) { &Error_Mail; }
		local($old_selected) = select(SMTP); 
		$| = 1; 
		select($old_selected);
		$* = 1;
		select(undef, undef, undef, .75);
		sysread(SMTP, $_, 1024);
		print SMTP "HELO $WEB_SERVER\r\n";
		sysread(SMTP, $_, 1024);
		while (/(^|(\r?\n))[^0-9]*((\d\d\d).*)$/g) { 
			$status = $4;
			$message = $3;
		}
		if ($status != 250) { &Error_Mail; }
		print SMTP "MAIL FROM:<$email_address>\r\n";
		sysread(SMTP, $_, 1024);
		if (!/[^0-9]*250/) { &Error_Mail; }
		local($good_addresses) = 0;
		$To = "<$To>";
		print SMTP "RCPT TO:$To\r\n";
		sysread(SMTP, $_, 1024);
		/[^0-9]*(\d\d\d)/;
		if ($1 eq '250') { $good_addresses++; }
		if (@bcc) {
			foreach $address (@bcc) {
				if ($address) {
					$address = "<$address>";
					print SMTP "RCPT TO:$address\r\n";
					sysread(SMTP, $_, 1024);
					/[^0-9]*(\d\d\d)/;
					if ($1 eq '250') { $good_addresses++; }
				}
			}
		}
		if (!$good_addresses) { &Error_Mail; }
		print SMTP "DATA\r\n";
		sysread(SMTP, $_, 1024);
		if (!/[^0-9]*354/) { &Error_Mail; }
		print SMTP "To: $To\r\n";
		print SMTP "From: $email_address\r\n";
		print SMTP "Subject: $messagesubject\r\n\r\n";
		print SMTP "$messagebody";
		print SMTP "\r\n\r\n.\r\n";
		sysread(SMTP, $_, 1024);
		shutdown(SMTP, 2);
	}
	elsif ($mailprog eq "libnet"){
		$smtp = Net::SMTP->new("$SMTP_SERVER");
		$smtp->mail( "$email_address");
		$smtp->to("$To");
		if (@bcc) {
			foreach $address (@bcc) {
				if ($address) {
					$address = "<$address>";
					$smtp->to("$address");
				}
			}
		}
		$smtp->data();
		$smtp->datasend("To: $To\n");
		$smtp->datasend("From: $email_address\n");
		$smtp->datasend("Subject: $messagesubject\n\n");
		$smtp->datasend("$messagebody");
		$smtp->quit;
	}
	elsif ($mailprog) {
		open (MAIL, "|$mailprog -t") || &Error_Mail;
		print MAIL "To: $To\n";
		if (@bcc) {
			print MAIL "Bcc: ";
			foreach $bcc (@bcc) {
				if ($notfirst) { print MAIL ","; }
				print MAIL "$bcc";
				$notfirst = 1;
			}
			print MAIL "\n";
		}
		print MAIL "From: $email_address\n",
		  "Subject: $messagesubject\n\n",
		  "$messagebody";
		close (MAIL);
	}
}

sub Error_Mail {
	&Header("$text{'9000'}","$text{'9120'}");
	print "<P>$text{'9121'}\n";
	&Footer;
}

sub Error_File {
	&Header("$text{'9000'}","$text{'9130'}");
	print "<P>$text{'9131'} ";
	print "<STRONG>$_[0]</STRONG> $text{'9132'} ";
	print "$adverts_dir $text{'9133'}\n";
	&Footer;
}

sub Error_NoStats {
	&Header("$text{'9000'}","$text{'9140'}");
	print "<P ALIGN=CENTER>$text{'9141'}\n";
	&Footer;
}

sub Error_DBM {
	print "<P><STRONG>$text{'9150'}</STRONG>\n";
}
