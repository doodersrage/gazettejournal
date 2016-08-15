#!/usr/bin/perl
#

#Make sure that the above line is at the very first line, not
#several lines down, and that it is completely flush with 
#the left margin, or your script will not work.

use CGI;
$query = new CGI;

#You need to modify this script at all parts of Step B

#Depending on where Perl is on your server, the above location
#may not be correct.  To find where Perl is, telnet to your
#server and at the prompt type: which perl  This will tell you
#the correct path to Perl on your server.  Or, contact your
#server administrator

#Script Description
  #Unique script ID: 23a3-3acc
  #Created on:       6/13/2001
  #Last edited on:   8/21/2001
  #Edited by KBH on: 10/16/2001
  #Script class:     A

#You can edit this script on my server any time up to 365 days
#after it was first created.  The values that you used to create
#this script will be loaded and you can change the configurations
#as needed.  Any edits you make to this script offline should be
#marked with comments so that you can easily find them and transfer
#them to any upgraded scripts you create

#See http://flattext.com

#STEP A================================
#SCRIPT USAGE
#This is FLATTEXT 6.0, released November 1, 2000

#COPYRIGHT NOTICE
#This script is (c) 2000 by Thomas Zimmerman
#and must not be sold or distributed in any form.
#I protect these rights vigorously.  If you have
#questions about the domain of this copyright, please
#contact me.  You must keep this copyright notice
#in order to use this program, but you are free to
#remove the printed credits at Step L7.

#I am more than happy to help you if you experience
#any difficulties with this script.  Many of the most
#common problems are addressed in the help
#files at:

#http://flattext.com/help/

#A1. The following lines get and process data passed 
#through the URL, do not modify
$stringpassed=$ENV{'QUERY_STRING'};

#A2. Replace all plusses with spaces for data passed via URL
$stringpassed=~s/\+/ /g;

#STEP B================================
#You MUST modify each of the variables in this this section

#B1. REQUIRED: The location of data file on your server.  This must
#be the PATH to your data file, not the URL of your data file!  There
#is extensive treatment of this in the Help Pages, under Data File
#Errors: http://flattext.com/help/
  $data="/export/home/ggazett/ggazett/public_html/ddb/deaths.txt";

#B2. REQUIRED: The URL of this file in your cgi-bin directory.  You must
#provide the full URL, beginning with http
  $thisurl="http://gazettejournal.net/cgi-bin/ddb/ddb.cgi";

#B3. OPTIONAL: You can format the opening and closing HTML
#of your results page in a separate file that can be written in
#regular HTML and saved on your server.  If your script can't
#find this file and open it, the default result screen is displayed
#instead.  For ease of configuration, place it in the same directory
#as your data file.  Note: this file must have three plusses +++
#where you want your search results inserted.  See help file
#under Template Problems: http://flattext.com/help/
  $openinghtml="/export/home/ggazett/ggazett/public_html/ddb/ddb_template2.shtml";

#STEP C================================

#C1. Maximum number of matching records to display per page
  $maximumpage=50;

#C2. Maximum total number of records to display per search,
#for stylistic reasons, should be multiple of above number
  $maximum=10000;

#C3. Minimum number of characters user must search for
 $minimumcharacters=0;

#STEP D================================
#You should not need to modify this section at all

#D1. Check to see if opening html file is on server
if (-e "$openinghtml"){

#D2. If so, open it and write opening and closing text to different strings
#to be used throughout the script
$problem="Can't open template file.  Make sure you are referencing the file and not just a directory.";
open(OPENING, "$openinghtml") || &security;
@wholefile=<OPENING>;
close(OPENING);
$fulltemplate=join("\n",@wholefile);
($templatestart,$templateend)=split(/\+\+\+/,$fulltemplate);}
else{

#D3. If template file not found, use this for now
$templatestart="<HTML><HEAD><TITLE>$templatetitle</TITLE></HEAD><BODY>";
$templateend="</BODY></HTML>";}
  $delimiter="\t";

#D4. Get words entered by user
  $words=$query->param('words');

#D5. Checks to see if anything attached to URL
if (length($stringpassed)>1){
($words,$sf1)=split(/&&/,$stringpassed);}

#STEP E================================
#E2. Remove quotation marks from words entered by user
$words=~s/\"//g;

#E3. Remove word AND from search words since all
#searches are and searches
$words=~s/ and / /g;
$words=~s/ AND / /g;

#E4 Check to make sure that user has searched for the number of characters
#you specified at C3.
if (length($words)<$minimumcharacters && !$actiontotake){
$problem="Unable to execute your search.  You need to search for at least 2 characters.  Please press back on your browser to continue.";
&security;}
#E5. Get field user wants to search
if (!$sf1){
  $sf1=$query->param('sf1');}

#E6. Get number of records already displayed
  $startitem=$query->param('startitem');

#E7. Figure the last record to display on this page
  $enditem=$startitem+$maximumpage;

#STEP F================================
#Do not modify this section

#F1. Get length of words entered by user
$checklength=length($words);

#F2. Checks to see if length is shorter than your minimum
if ($checklength<$minimum && !$actiontotake){
$problem="You must enter at least $minimum characters.  Please press BACK on your browser and fix this problem.";
&security;}

#F3. Lowercase search words to make matches easier
$words2=$words;
#F3a.  Support for Eurpoean characters.  Uncomment and replace with your
#character set in brackets for all non-English Characters.  See help files.
#$words=~tr/[ÈÉÊËéêëè]/e/;
#$words=~tr/[ÀÁÂÃÄÅÆàáâãäåæ]/a/;
#$words=~tr/[çÇ]/c/;
#$words=~tr/[ìíîïÍÎÏÌ]/i/;
#$words=~tr/[ÒÓÔÕÖòóôõöô]/o/;
#$words=~tr/[ÙÚÛÜùúûü]/u/;
$words=lc($words);

#F4. Split words entered by user into seven variables
($one1, $two2, $three3, $four4, $five5, $six6, $seven7)=split(/ /, $words);


#STEP G================================
#Do not modify this section

#G1. Open datafile and write contents to an array, if can't open report the problem at the security subroutine
$problem="You do not have a file to search on my server.  Please ADD test records before trying to search your test data file.";
open (FILE, "$data") || &security;
@all=<FILE>;
close (FILE);

#G2.  The line below is required, do not modify
print "Content-type: text/html\n\n";

#G3. Display HTML Header
print "$templatestart\n";

#G4. Show words user searched for
print "<FONT size=2>You Searched For: <FONT color=\"#800000\">$words2</FONT></FONT><P>\n";

#STEP H================================
#H1. Read each line of the data file, compare with search words

foreach $line (@all){
$line=~s/\n//g;
$loopsaround++;

$checkleng=length($line);
if ($checkleng<2){next};

$linetemp1=lc($line);

#H1a.  Support for Eurpoean characters.  Uncomment and replace with your
#character set in brackets for all non-English Characters.  See help files.
#$linetemp1=~tr/[ÈÉÊËéêëè]/e/;
#$linetemp1=~tr/[ÀÁÂÃÄÅÆàáâãäåæ]/a/;
#$linetemp1=~tr/[çÇ]/c/;
#$linetemp1=~tr/[ìíîïÍÎÏÌ]/i/;
#$linetemp1=~tr/[ÒÓÔÕÖòóôõöô]/o/;
#$linetemp1=~tr/[ÙÚÛÜùúûü]/u/;

($Name,$Location,$Age,$Source,$Date,$skipthisfield)=split (/$delimiter/,$linetemp1);

#H2. This section specifies the fields to sort results by
#See help databases for patches to allow various kinds of sorts
$line="$Name$Date$delimiter$loopsaround$delimiter$line";

#H3. This line removes stray leading spaces before sorting your results
$line=~s/^ +//;

#H4. If the variable sf1 is deleted from search page, then
#search all of the fields in the database
if ($sf1 eq ""){
$wholestring=" $Name $Location $Age $Source $Date";
if ($wholestring  =~/\b$one1/ && $wholestring  =~/\b$two2/ && $wholestring  =~/\b$three3/ && $wholestring  =~/\b$four4/ && $wholestring  =~/\b$five5/ && $wholestring=~/\b$six6/ && $wholestring  =~/\b$seven7/){
push (@keepers,$line);}}

#H7. If the variable sf1 is named Name, then do this search
if ($sf1 eq "Name" && $Name =~/\b$one1/ && $Name  =~/\b$two2/ && $Name  =~/\b$three3/ && $Name  =~/\b$four4/ && $Name  =~/\b$five5/ && $Name  =~/\b$six6/ && $Name  =~/\b$seven7/){
push (@keepers,$line);}

#H7. If the variable sf1 is named Location, then do this search
if ($sf1 eq "Location" && $Location =~/\b$one1/ && $Location  =~/\b$two2/ && $Location  =~/\b$three3/ && $Location  =~/\b$four4/ && $Location  =~/\b$five5/ && $Location  =~/\b$six6/ && $Location  =~/\b$seven7/){
push (@keepers,$line);}

#H7. If the variable sf1 is named Age, then do this search
if ($sf1 eq "Age" && $Age =~/\b$one1/ && $Age  =~/\b$two2/ && $Age  =~/\b$three3/ && $Age  =~/\b$four4/ && $Age  =~/\b$five5/ && $Age  =~/\b$six6/ && $Age  =~/\b$seven7/){
push (@keepers,$line);}

#H7. If the variable sf1 is named Source, then do this search
if ($sf1 eq "Source" && $Source =~/\b$one1/ && $Source  =~/\b$two2/ && $Source  =~/\b$three3/ && $Source  =~/\b$four4/ && $Source  =~/\b$five5/ && $Source  =~/\b$six6/ && $Source  =~/\b$seven7/){
push (@keepers,$line);}

#H7. If the variable sf1 is named Date, then do this search
if ($sf1 eq "Date" && $Date =~/\b$one1/ && $Date  =~/\b$two2/ && $Date  =~/\b$three3/ && $Date  =~/\b$four4/ && $Date  =~/\b$five5/ && $Date  =~/\b$six6/ && $Date  =~/\b$seven7/){
push (@keepers,$line);}

#H8. If the variable sf1 is named All Below, then do this
if ($sf1 eq "All Fields"){
$wholestring= "$Date $Source $Age $Location $Name ";
if ($wholestring  =~/\b$one1/ && $wholestring  =~/\b$two2/ && $wholestring  =~/\b$three3/ && $wholestring  =~/\b$four4/ && $wholestring  =~/\b$five5/ && $wholestring=~/\b$six6/ && $wholestring  =~/\b$seven7/){
push (@keepers,$line);}}

}
#STEP J================================
#J1. Sort matches stored in array. 
@keepers=sort(@keepers);

#J2. Get and display number of matches found
$length1=@keepers;

#J3. If the number of matches is less than enditem, adjust
if ($length1<$enditem){
$enditem=$length1;
$displaystat="Y";}

#J4. The first field about to display
$disstart=$startitem+1;

#J5. Show user total number of matches found
if ($length1){
print "$length1 Matches Found (displaying $disstart to $enditem)<P>\n";
} else {
print "Your search found zero records, please try again.<P>\n";}

#STEP K================================
#K1. Do some HTML formatting before showing results
#K2. Open table for results
print "<table border=0 cellpadding=2 cellspacing=0><tr valign=top>\n";
#K4. Keep track of results processed on this page
foreach $line (@keepers){

#K5. Delete stray hard returns
$line=~s/\n//g;

#K6. Keep track of records displayed

$countline1++;

#K7. Decide whether or not this record goes on this page
if ($countline1>$startitem && $countline1<=$enditem){

#K8. Open each line of sorted array for displaying

($sortfield,$loopsaround,$Name,$Location,$Age,$Source,$Date,$skipthisfield)=split (/$delimiter/,$line);

#K15. Assign Table Colors
$darkcolor="#EEEEEE";
$lightcolor="#FFFFFF";
#K16. Check for alternate colors
$test=$colorcount%2;
if ($test==0){
$rowcolor=$darkcolor;}
else{
$rowcolor=$lightcolor;}
$colorcount++;
print "<tr valign=top bgcolor=$rowcolor>\n";

#K15. Formatting for field Name.  If you add any HTML, make sure you
#put a backslash in front of all quote marks inside print statements
if (!$Name){
$Name="&nbsp;";
print "<td>$Name</td>\n";}
else{
print "<td>$Name</td>\n";}

#K15. Formatting for field Location.  If you add any HTML, make sure you
#put a backslash in front of all quote marks inside print statements
if (!$Location){
$Location="&nbsp;";
print "<td>$Location</td>\n";}
else{
print "<td>$Location</td>\n";}

#K15. Formatting for field Age.  If you add any HTML, make sure you
#put a backslash in front of all quote marks inside print statements
if (!$Age){
$Age="&nbsp;";
print "<td>$Age</td>\n";}
else{
print "<td>$Age</td>\n";}

#K15. Formatting for field Source.  If you add any HTML, make sure you
#put a backslash in front of all quote marks inside print statements
if (!$Source){
$Source="&nbsp;";
print "<td>$Source</td>\n";}
else{
print "<td>$Source</td>\n";}

#K15. Formatting for field Date.  If you add any HTML, make sure you
#put a backslash in front of all quote marks inside print statements
if (!$Date){
$Date="&nbsp;";
print "<td>$Date</td>\n";}
else{
print "<td>$Date</td>\n";}
print "</tr>\n";
#K18. Check password before showing edit and delete snippets

#STEP L================================
#L1. If total displayed equals maximum you set, then exit
if ($countline1 == $maximum && $maximum){
$problem2="Your search was terminated at $maximum records, please be more specific in your search";
last;}

#L2. If script just got to last match then exit program
if ($length1 == $countline1){
last;}

#L3. If script is at the end of a page then show NEXT button
if ($countline1 == $enditem && $displaystat ne "Y"  && $maximum>$countline1){
$stopit="Y";
last;
}

}}

print "</tr></table>\n";

#L4. Display NEXT MATCHES button
if ($stopit eq "Y"){
print "<form method=POST action=\"$thisurl\">\n";

#L5. Pass hidden variables so script will know how to display next page
print "<input type=hidden name=\"words\" value=\"$words\"> \n";
print "<input type=hidden name=\"sf1\" value=\"$sf1\"> \n";
print "<input type=hidden name=\"checkpassword\" value=\"$checkpassword\"> \n";
print "<input type=hidden name=\"startitem\" value=\"$enditem\"> \n";
print "<input type=submit value=\"Get Next Matches\"></form>\n";
}

#L6. Show problems
print "<P>$problem2\n";

#L7. Show credits.  If registered, you can delete entire Step L7
#print "<P><CENTER><FONT size=\"-1\">Search Results by <A #href=\"http://www.flattext.com\">FLATTEXT</A></FONT></CENTER>\n";

#L8. If opening.htm was found, show its closing html codes
srand();
$checkval=int(rand(30));
if ($checkval==3){
print "<img src=\"http://flattext.com/b.gif?23a3-3acc\">\n";}
print "$templateend\n";
exit;

#STEP M================================
sub security{
#M1. This is the subroutine that reports all problems
print "Content-type: text/html\n\n";

print "$templatestart\n";
print "<CENTER><FONT size=+2>Data Error</FONT></CENTER><P>\n";
print "<FONT size=\"+1\">Please correct the following error:<blockquote>$problem</blockquote></FONT>\n";
print "$templateend\n";
exit;
}

