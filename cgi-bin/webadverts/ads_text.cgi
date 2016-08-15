############################################
##                                        ##
##       WebAdverts  (Text Strings)       ##
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

##################
# INITIAL ACCESS #
##################

$text{'1000'} = "Gazette-Journal";
$text{'1001'} = "Account Administration";

$text{'1010'} = "To view the status of any single account or defined group,<BR>enter its name and password:";
$text{'1011'} = "Account or Group Name:";
$text{'1012'} = "Password:";
$text{'1013'} = "Review Single Account or Group";

$text{'1020'} = "To add your site to the exchange,<BR>enter the name and password you wish to use:";
$text{'1021'} = "Account Name:";
$text{'1022'} = "Password:";
$text{'1023'} = "Create New Account";

$text{'1030'} = "If you've forgotten your password,<BR>enter your account, and your password will be e-mailed to you:";
$text{'1031'} = "Account Name:";
$text{'1032'} = "Get Password";

$text{'1040'} = "To view the status of all accounts and access the main administrative functions,<BR>input the administrative password:";
$text{'1041'} = "Password:";
$text{'1042'} = "Review Accounts";

$text{'1050'} = "The password for the";
$text{'1051'} = "account has been sent to its associated e-mail address!";

################
# MAIN DISPLAY #
################

@months = ('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

$text{'2000'} = "(Accounts in the";
$text{'2001'} = "Group)";
$text{'2002'} = "Master Overview:";

$text{'2005'} = "Current Status of the";
$text{'2006'} = "Account:";
$text{'2007'} = "(This account is awaiting administrative approval.)";

$text{'2010'} = "Account";
$text{'2011'} = "Start";
$text{'2012'} = "End";
$text{'2013'} = "Zone(s)";
$text{'2014'} = "Wt.";
$text{'2015'} = "Exposures";
$text{'2016'} = "Exp./Day";
$text{'2017'} = "Clicks";
$text{'2018'} = "%";
$text{'2019'} = "Ratio";
$text{'2020'} = "N/A";

$text{'2030'} = "Your Banner's<BR>Exposures";
$text{'2031'} = "Clicks On<BR>Your Banner";

$text{'2040'} = "Banners Shown<BR>on Your Site";
$text{'2041'} = "Clicks From<BR>Your Site";

$text{'2050'} = "Log Out";

$text{'2100'} = "EXPIRED!";
$text{'2101'} = "(non-expiring!)";
$text{'2102'} = "(date unknown)";

$text{'2110'} = "clicks";
$text{'2111'} = "exposures";

$text{'2120'} = "Your account currently has no assigned banner.";
$text{'2121'} = "To date, you have displayed";
$text{'2122'} = "banners on your site";
$text{'2123'} = ", and generated";
$text{'2124'} = "clicks from your site";
$text{'2125'} = ".";
$text{'2126'} = "You have earned";
$text{'2127'} = "exposures for your own banner on other sites. (You earn";
$text{'2128'} = "1 exposure for each";
$text{'2129'} = "displays";
$text{'2130'} = ", and";
$text{'2131'} = "exposures for each click";
$text{'2132'} = ".)";
$text{'2133'} = "Your account has been assigned an additional";
$text{'2134'} = "&quot;bonus&quot; exposures, as well.";
$text{'2135'} = "The HTML code below should be placed on your site where you want banners to appear.";

$text{'2150'} = "If you want banners to appear on more than one page (or more than once on a single page), simply use a unique &quot;page=&quot; number for each banner call. This will ensure that new banners are loaded (and that new displays are credited to you) on each of your pages. (Note that there are two &quot;page=&quot; designations in the call, and that they must match!)";

######################
# SECONDARY DISPLAYS #
######################

$text{'2200'} = "Edit Account";
$text{'2201'} = "Delete Account";
$text{'2202'} = "View Zone Stats";
$text{'2203'} = "View Daily Stats";
$text{'2204'} = "View Monthly Stats";
$text{'2205'} = "View IP Address Log";
$text{'2206'} = "View Overall Stats";

$text{'2210'} = "Stats by Zone for the";
$text{'2211'} = "Account";
$text{'2212'} = "Zone";
$text{'2213'} = "Member";
$text{'2214'} = "Displays";
$text{'2215'} = "Clicks";

$text{'2220'} = "Daily Stats for the";
$text{'2221'} = "Account";
$text{'2222'} = "(Last Five Weeks Only)";
$text{'2223'} = "(Full List)";
$text{'2224'} = "Date";
$text{'2225'} = "View Daily Stats for Last Five Weeks Only";
$text{'2226'} = "View Full Daily Stats List";

$text{'2230'} = "Monthly Stats for the";
$text{'2231'} = "Account";
$text{'2232'} = "Month";

$text{'2240'} = "IP Address Log for the";
$text{'2241'} = "Account";
$text{'2242'} = "The following log file lists the IP addresses of those individuals who have seen or clicked on this account's banner since yesterday morning. Each line displays the time of the exposure (E) or click-thru (C), and the IP address of the responsible party.";
$text{'2243'} = "Total exposures logged";
$text{'2244'} = "Total clicks logged";
$text{'2245'} = "Total IP addresses logged";
$text{'2246'} = "Average log entries per IP address";

###############
# EDIT SCREEN #
###############

$text{'5000'} = "Info for the";
$text{'5001'} = "Account";

$text{'5100'} = "Name";
$text{'5101'} = "E-Mail";
$text{'5102'} = "Site URL";
$text{'5103'} = "Banner URL(s)";
$text{'5104'} = "Zone(s)";

$text{'5200'} = "(You may leave this blank; once your account has been created, you'll have the option of uploading a banner from your computer. Just return to the edit screen to do so.)";
$text{'5201'} = "(Once your account has been created, you'll need to upload a banner from your computer. Just return to the edit screen to do so.)";

$text{'5300'} = "(Select above, the zones -- or &quot;target categories&quot; -- in which this banner should be displayed.)";
$text{'5301'} = "(Select above, the category or categories of banners to be displayed on your own pages.)";

$text{'5400'} = "If you'd like to change your account password, simply enter it below.";
$text{'5401'} = "New Password";
$text{'5402'} = "Change Password";

$text{'5500'} = "If you'd like to upload a new banner for your account, you may do so from here. <EM>(Note that you must be using either Netscape Navigator version 2 or higher, or Microsoft Internet Explorer version 4 or higher.)";
$text{'5501'} = "File to Upload";
$text{'5502'} = "Upload Banner";

##################
# ERROR MESSAGES #
##################

$text{'9000'} = "Error!";

$text{'9010'} = "Access Denied!";
$text{'9011'} = "Sorry, but you're not allowed access to admin functions.";

$text{'9020'} = "No Password!";
$text{'9021'} = "You must enter a password!";

$text{'9022'} = "Invalid Password!";
$text{'9023'} = "The password you entered is incorrect!";

$text{'9024'} = "Password Mismatch!";
$text{'9025'} = "Your administrative password was not set, as the two entries were different!";
		
$text{'9030'} = "Name in Use!";
$text{'9031'} = "The account name you entered is already in use!";

$text{'9040'} = "No E-Mail Address!";
$text{'9041'} = "There is no e-mail address associated with the";
$text{'9042'} = "account!";

$text{'9050'} = "Invalid Account or Group Name!";
$text{'9051'} = "There is no account or group on the list with ";
$text{'9052'} = "as its name! (Note that all names <EM>are</EM> case sensitive!)";

$text{'9060'} = "Invalid Banner Format!";
$text{'9061'} = "Your banner must be in <STRONG>GIF</STRONG> (.gif) or <STRONG>JPEG</STRONG> (.jpg or .jpeg) format!";

$text{'9070'} = "Banner Too Large!";
$text{'9071'} = "Your banner is too large! Its file size must be no more than";
$text{'9072'} = "kilobytes. (The file you attempted to upload was";
$text{'9073'} = "kilobytes.)";

$text{'9080'} = "Unable to Open File!";
$text{'9081'} = "The script was unable to open a file to save your banner. (This most likely indicates a permissions error on the upload directory.)";

$text{'9100'} = "Incomplete Entry!";
$text{'9101'} = "You didn't provide all of the necessary information! You must at <EM>least</EM> include a password!";

$text{'9105'} = "Incomplete Form!";
$text{'9106'} = "You didn't provide all of the necessary information to allow creation of the";
$text{'9107'} = "group!";
		
$text{'9110'} = "Invalid Entry!";
$text{'9111'} = "You've indicated that this account is to earn exposures by showing other banners, but have also indicated that it is to expire based on date or click-thrus. These two designations are mutually incompatible! Display ratios may only be set for accounts which are defined as non-expiring, or for which a set number of &quot;bonus&quot; exposures has been defined.";

$text{'9120'} = "Mail System Error!";
$text{'9121'} = "The server encountered an error while trying to send out e-mail. please contact the webmaster.";

$text{'9130'} = "File Permission Error!";
$text{'9131'} = "The server encountered an error while trying to access the";
$text{'9132'} = "file! The most likely cause of the problem is a permissions error in your adverts directory (";
$text{'9133'} = "). Make sure that the directory exists and that it is set world-writable.";

$text{'9140'} = "No Daily Stats!";
$text{'9141'} = "Sorry, but it seems there is no daily log file available!";

$text{'9150'} = "The script encountered an error while trying to create or access the database file!";

1;
