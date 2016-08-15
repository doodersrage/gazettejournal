############################################
##                                        ##
##      WebAdverts (Master Settings)      ##
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

# COPYRIGHT NOTICE:
#
# Copyright 2001 Darryl C. Burgdorf.  All Rights Reserved.
#
# This program is being distributed as shareware.  It may be used and
# modified by anyone, so long as this copyright notice and the header
# above remain intact, but any usage should be registered.  (See the
# program documentation for registration information.)  Selling the
# code for this program without prior written consent is expressly
# forbidden.  Obtain permission before redistributing this program
# over the Internet or in any other medium.  In all cases copyright
# and header must remain intact.
#
# Certain subroutines and code segments utilized in this program are
# adapted from code written by Kevin Dearing of webjourneymen.net
# and dacpro.com, and are used with permission.
#
# This program is distributed "as is" and without warranty of any
# kind, either express or implied.  (Some states do not allow the
# limitation or exclusion of liability for incidental or consequential
# damages, so this notice may not apply to you.)  In no event shall
# the liability of Darryl C. Burgdorf and/or Affordable Web Space
# Design for any damages, losses and/or causes of action exceed the
# total amount paid by the user for this software.

# DEFINE THESE CONFIGURATION VARIABLES!

# The following variables affect display of banners.  They should be
# defined in accordance with the detailed information provided in the
# documentation file.

$display_path = "/Users/newgazette/public_html/cgi-bin/webadverts/ads_display.cgi";
$adverts_dir = "/Users/newgazette/public_html/ads";

$ADVUseLocking = 1;

$ADVUseCookies = 1;
$CheckForCookie = 1;

$GraphicTimestamp = 0;

$ADVLogIP = 1;
$ADVResolveIPs = 0;

$DupViewTime = 0;
$DupClickTime = 0;
$ClickViewTime = 0;

$LogByZone = 1;

$ADVRandomizeList = 1;

$DefaultBanner = "";

$IgnoredIPs = "";
$RequireMember = 0;

$IFRAMEbodyspec = "BGCOLOR=\"#ffffff\" TEXT=\"#000000\"";
$IFRAMErefreshrate = 0;

$JSConflict = 1;

$DBMType = 0;

$HourOffset = 0;

# The following variables affect the functioning of your administrative
# script.  They should be defined in accordance with the detailed
# information provided in the documentation file.

$admin_cgi = "http://gazettejournal.net/cgi-bin/webadverts/ads_admin.cgi";

$nonssi_cgi = "http://gazettejournal.net/cgi-bin/webadverts/ads.cgi";

@zones = ('NBAN','E3','E3B','NAD','XAD','Survey','menu','static1');

$AdminDisplaySetup = 0;

$AllowUserEdit = 0;
$RequireAdminApproval = 0;

$LogAdminAccesses = 1;
$BannedIPs = "";

$MasterIPLogDays = 7;

$IPLog = "";

$UserUploadDir = "";
$UserUploadURL = "";
$MaxBannerSize = 20;
$RequireUpload = 0;

$DefaultDisplayRatio = 0;
$DefaultClicksRatio = 0;

$ShowClicksFrom = 0;

$DefaultWeight = 1;
$DefaultBorder = 0;

$NoBanners = 0;

$IFRAMEexchange = 1;
$JavaScriptExchange = 1;

$ExchangeName = "Gazette-Journal Advertisers";
$ExchangeURL = "";
$ExchangeLogo = "";
$ExchangeLogoWidth = 0;
$ExchangeLogoHeight = 0;
$ExchangeLogoPosition = "";
$ExchangeBannerWidth = 0;
$ExchangeBannerHeight = 0;
$ExchangeBorder = 0;

$DefaultLinkAttribute = "TARGET=&quot;_blank&quot;";

$bodyspec = "BGCOLOR=\"#ffffff\" TEXT=\"#000000\"";
$fontspec = "FACE=\"Arial\"";
$MetaFile = "";
$header_file = "";
$footer_file = "";

$admin_name = "Administrator";
$email_address = "webmaster\@gazettejournal.net";
$mailprog = '/usr/sbin/sendmail';
$WEB_SERVER = "";
$SMTP_SERVER = "";

# use Socket;
# use Net::SMTP;

# DO NOT REMOVE THE FOLLOWING ("1;") LINE!

1;
