#!/usr/bin/perl

############################################
##                                        ##
##       WebAdverts (Configuration)       ##
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

# Define the location of your "ads_settings" file.
$settings_path = "/Library/WebServer/Documents/gazette/cgi-bin/webadverts/ads_settings.cgi";

# Define the URL of this configuration file:
$display_cgi = "http://gazettejournal.net/cgi-bin/webadverts/ads.cgi";

# Define the zone, if any, unique to this configuration file:
$advertzone = "";

# NOTHING BELOW THIS LINE NEEDS TO BE ALTERED!

require $settings_path;

unless ($ADVNoPrint) {
	if ($ARGV[0]) { $ADVQuery = $ARGV[0]; }
	else { $ADVQuery = $ENV{'QUERY_STRING'}; }
}

if ($ADVUseCookies && $CheckForCookie && ($ADVQuery =~ /page=/)
  && ($ADVQuery !~ /advert=/) && ($ADVQuery !~ /banner=/)
  && ($ENV{'HTTP_COOKIE'} !~ /TestCookie=TestValue/)  
  && ($ADVQuery !~ /checkforcookie/)) {
	print "Set-Cookie: TestCookie=TestValue\n";
	print "location: $display_cgi?$ADVQuery;checkforcookie\n\n";
	exit;
}
elsif ($CheckForCookie
  && ($ENV{'HTTP_COOKIE'} !~ /TestCookie=TestValue/)) {
	$ADVUseCookies = 0;
}

require $display_path;
&ADVsetup;

unless ($ADVNoPrint) { reset 'A-Za-z'; exit; }

1;
