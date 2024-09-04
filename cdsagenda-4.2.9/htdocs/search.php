<?php
// $Id: search.php,v 1.1.1.1.4.10 2004/07/29 10:04:51 tbaron Exp $

// search.php --- search interface
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// search.php is part of CDS Agenda.
//
// CDS Agenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// CDS Agenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with ; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
//
// 
//

require_once 'AgeDB.php';
require_once 'config/config.php';
require_once 'platform/template/template.inc';

$db = &AgeDB::getDB();

$Template = new Template($PathTemplate);
$Template->set_file(array("mainpage"  => "search.ihtml",
                          "JSMenuTools" => "JSMenuTools.ihtml",
						  "AGEfooter" => "AGEfooter_template.inc"));	

$Template->set_var("search_supportEmail", $support_email);
$Template->set_var("search_runningAT", $runningAT);
$Template->set_var("images", $IMAGES_WWW );
$Template->parse( "search_jsmenutools", "JSMenuTools", true );

// Prepare menu creation
$topbarStr .= 
"<table border=0 cellspacing=1 cellpadding=0 width=\"100%\">
<tr>";
include 'menus/topbar.php';
$menuArray = array("MainMenu",
				   "ToolMenu",
				   "UserMenu",
				   "HelpMenu");
$topbarStr .= CreateMenuBar( $menuArray );
$topbarStr .= " </tr> </table>\n\n";
$Template->set_var("search_topmenubar", $topbarStr);

$core = "
<H1 class=\"headline\">Search:</H1>

<FORM action=\"search.php\">
<CENTER>
<TABLE cellspacing=1 cellpadding=1 border=0 bgcolor=black>
<TR>
        <TD>
<TABLE bgcolor=white width=\"100%\" cellpadding=5 cellspacing=1 border=0>";

$core .= "<TR class=headerselected><TD colspan=3 align=center><font size=-1 color=white><B>WHERE?</B></font></TD></TR><TR><TD colspan=3 valign=top><font size=-1>\n";

$db = &AgeDB::getDB();
if ( $newSearchMode ) {
	$core .= "
                         <SELECT name=\"base\">
                                 <OPTION value=\"all\"> all";

	// New search mode, with all the categories but by title every
	// title should appear only once but the search will be done
	// inside all the category of each level with the specified title
	
	$sql = " select title from LEVEL GROUP BY title ";
	$resCat = $db->query($sql);

	if (DB::isError($resCat)) {
		die ($resCat->getMessage());
	}
	$numRows = $resCat->numRows();
	if ( $numRows != 0 ) {
		for ( $indCat = 0; $indCat < $numRows; $indCat++ ) {
			$row = $resCat->fetchRow();
			$title = $row['title'];

			if ($base == $title)
				$core .= "   <OPTION value=\"" . $title 
					. "\" selected> " . $title 
					. "\n";
			else
				$core .= "   <OPTION value=\"" . $title 
					. "\"> " . $title 
					. "\n";
		}
	}
	$core .= "
                         </SELECT>
                 </TD>
         </TR>";
	$core .= "</TD></TR>\n";
}
else {
	// Classic search mode (Implemented by CERN)

	// If search is accessed for the first time, we setup all id[x]
	// variables from the fid var

	if ( $fid != "" ) {
		// First retrieve all level titles in $id[#]
		$sql = "select level,uid from LEVEL where uid='$fid'";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
		$row = $res->fetchRow();
		$level = $row['level'];
		$uid   = $row['uid'];

		$id[$level] = "" . $uid;
		for ( $i=$level-1 ; $i>=1 ; $i-- ) {
			$sql = "select fid from LEVEL where uid='${id[$i+1]}'";
			$res2 = $db->query($sql);
			if (DB::isError($res2)) {
				die ($res2->getMessage());
			}
			$row = $res2->
				$id[$i] = "" . $row['fid'];
		}
	}

	$id[0] = "0";
	$i = 0;

	// Then we create all select boxes

	while (( $id[$i] != "" ) && ( $id[$i] != "all")) {
		$i++;

		$sql = "select uid,title from LEVEL where level=$i and fid='${id[$i-1]}' order by title";
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}
		$numRows = $res->numRows();
		if ( $numRows != 0 ) { 
			// One or more records retrieved
			$core .= "<SELECT onChange=\"document.forms[0].submit();\" name=\"id[$i]\">\n";
			$core .= "   <OPTION value=\"all\"> all categories\n";

			for ( $j = 0; $j < $numRows; $j++ ) { 
				$row = $res->fetchRow();
				$uid   = $row['uid'];
				$title = $row['title'];
				// for each record retrieved
				if ($id[$i] != $uid) {
					$core .= "   <OPTION value=\"" . $uid . "\"> " . $title . "\n";
				}
				else {
					$core .= "   <OPTION selected value=\"" . $uid . "\"> " . $title . "\n";
				}
			}
			$core .= "</SELECT>\n";
		}
		else
			{
				$id[$i] = "";
			}
		$core .= "<BR>\n";
	}
}

$core .= "</font></TD></TR><TR class=headerselected><TD colspan=3 align=center><font size=-1 color=white><B>WHAT?</B></font></TD></TR>\n";
$core .= "<TR>\n";
$core .= "<TD><font size=-1>\n";
$core .= "<select name=field>\n";
// Search by SMR if active
if ( $newSearchMode ) {
	// SMR field
	if ($field == "" || $field == "smr")
		$core .= "   <OPTION value=smr selected> smr (redirect to the smr finder script)\n";
	else
		$core .= "   <OPTION value=smr> smr (redirect to the smr finder script)\n";
}
// title field
if ($field == "title") {
	$core .= "   <OPTION value=title selected> title/comments\n";
}
else {
	$core .= "   <OPTION value=title> title/comments\n";
}

// speaker field
if ($field == "speaker") {
	$core .= "   <OPTION value=speaker selected> speaker/chairman\n";
}
else {
	$core .= "   <OPTION value=speaker> speaker/chairman\n";
}

// agenda id
if ($field == "agid") {
	$core .= "   <OPTION value=agid selected> agenda id\n";
}
else {
	$core .= "   <OPTION value=agid> agenda id\n";
}

$core .= "</SELECT></font></TD>\n";
$core .= "<TD><font size=-1><INPUT size=40 name=keywords value=\"$keywords\"></font></TD>
	<TD><font size=-1>
		<INPUT type=radio name=logop value=\"and\" checked>and<BR>
		<INPUT type=radio name=logop value=\"or\">or</font>
	</TD></TR>\n";


$core .= "<TR class=headerselected><TD colspan=3 align=center><font size=-1 color=white><B>WHEN?</B></font></TD></TR>\n";
$core .= "
<TR>
        <TD colspan=3><font size=-1>
                <INPUT type=radio name=period value=period checked>
                        <SELECT name=day onChange=document.forms[0].period[0].checked=1;>
                                <OPTION value=0> Any day
                                <OPTION value=1> 1st
                                <OPTION value=2> 2nd
                                <OPTION value=3> 3rd
                                <OPTION value=4> 4th
                                <OPTION value=5> 5th
                                <OPTION value=6> 6th
                                <OPTION value=7> 7th
                                <OPTION value=8> 8th
                                <OPTION value=9> 9th
                                <OPTION value=10> 10th
                                <OPTION value=11> 11th
                                <OPTION value=12> 12th
                                <OPTION value=13> 13th
                                <OPTION value=14> 14th
                                <OPTION value=15> 15th
                                <OPTION value=16> 16th
                                <OPTION value=17> 17th
                                <OPTION value=18> 18th
                                <OPTION value=19> 19th
                                <OPTION value=20> 20th
                                <OPTION value=21> 21st
                                <OPTION value=22> 22nd
                                <OPTION value=23> 23rd
                                <OPTION value=24> 24th
                                <OPTION value=25> 25th
                                <OPTION value=26> 26th
                                <OPTION value=27> 27th
                                <OPTION value=28> 28th
                                <OPTION value=29> 29th
                                <OPTION value=30> 30th
                                <OPTION value=31> 31st
                        </SELECT>of
                        <SELECT name=month onChange=document.forms[0].period[0].checked=1;>
                                <OPTION value=0> Any month
                                <OPTION value=1> January
                                <OPTION value=2> February
                                <OPTION value=3> March
                                <OPTION value=4> April
                                <OPTION value=5> May
                                <OPTION value=6> June
                                <OPTION value=7> July
                                <OPTION value=8> August
                                <OPTION value=9> September
                                <OPTION value=10> October
                                <OPTION value=11> November
                                <OPTION value=12> December
                        </SELECT>of
                        <SELECT name=year onChange=document.forms[0].period[0].checked=1;>
                                <OPTION value=0> Any year";
for ($i=0;$i<count($years);$i++) {
    $j = $i + 1;
    $core .= "                     <OPTION value=$j> $years[$i]\n";
}
$core .= "
                        </SELECT><BR>

                <INPUT type=radio name=period value=between> between:

                        <INPUT size=12 name=startbet onFocus=document.forms[0].period[1].checked=1;> and <INPUT size=12 name=endbet onFocus=document.forms[0].period[1].checked=1;><BR>(format yyyy-mm-dd)<BR>

                <INPUT type=radio name=period value=byweek>

                        <SELECT name=whichweek onFocus=document.forms[0].period[2].checked=1;>

                                <OPTION value=0> this week

                                <OPTION value=1> next week

                                <OPTION value=2> last week

                </SELECT>
	</font>
        </TD>

</TR>";

$keywords=stripslashes($keywords);

$core .= "<TR><TD align=center colspan=3><font size=-1><INPUT type=submit name=\"search\" value=\"Search\" onClick=\"if (document.forms[0].period[1].checked==true){ return checkbetween(); };\"></font></TD>\n";


//check the right logical operator button

if ($logop == "or") {
	$core .= "<SCRIPT>\n";
	$core .= " document.forms[0].logop[1].checked=true;\n";
	$core .= "</SCRIPT>\n";
}

$core .= "
</TR></TABLE>
</TD>
</TR>
</TABLE>
</CENTER>


<SCRIPT>

        function checkbetween()
        {
                return (datecheck(document.forms[0].startbet.value) && datecheck(document.forms[0].endbet.value));
        }

        function datecheck(txt)
        {
                var res=1;

                if (txt.length != 10){res=0;}
                if (txt.indexOf(\"-\") != 4){res=0;}
                if (txt.lastIndexOf(\"-\") != 7){res=0;}
                tmp=parseInt(txt.substring(0,4),10);
                if ((tmp < 1)||(isNaN(tmp))){res=0;}
                tmp=parseInt(txt.substring(5,7),10);
                if ((tmp > 12)||(tmp < 1)||(isNaN(tmp))){res=0;}
                tmp=parseInt(txt.substring(8,10),10);
                if ((tmp < 1)||(tmp > 31)||(isNaN(tmp))){res=0;}
                if (res == 0){
                        alert(\"Please enter a correct Date in the Search Period fields: Format: yyyy-mm-dd\");
                        return false;
                }
                return true;
        }
</SCRIPT>";


//////////////////////////////////////////////////////////////////////
//
// let's build the query!
//
//////////////////////////////////////////////////////////////////////

$i = 1;
while ( $id[$i] != "" ) { $i++; }
if ( $id[$i-1] == "all" )
{
    $id = "${id[$i-2]}";
    $j = $i-2;
}
else
{
    $id = "${id[$i-1]}";
    $j = $i-1;
}
if ($id == "all") { $id = "0"; }

$result = "";
$num = 0;
$core .= createSearchWord();

if ($search == "Search" && $keywords != "")
{
    searchCategory($id,"");
    
    if (isset($num) && $num != 0)
		{
			$core .= "
<H1 class=\"headline\">Results:</H1>\n";
			$core .= "$num record(s) found.<BR>";
			$core .= "<TABLE bgcolor=silver>\n";
            
			$core .= $result;

			$core .= "</TABLE>\n";
		}
    else if (isset($num) && $num == 0)
		{
			$core .= "0 record found\n";
		}
}

$core .= "</FORM>";

// Display core
$Template->set_var("search_core", $core);

// Create Menus
$menuText = CreateMenus($menuArray);
$Template->set_var("search_menus", $menuText);


// Create Footer
$Template->set_var("AGEfooter_shortURL", "");
$Template->set_var("AGEfooter_msg1", $AGE_FOOTER_MSG1);
$Template->set_var("AGEfooter_msg2", $AGE_FOOTER_MSG2);
$Template->parse("AGEfooter_template", "AGEfooter", true);

$Template->pparse("final-page", "mainpage");

// SEARCH FUNCTIONS

function createSearchWord() {
	global $period, $startbet, $endbet, $day, $month, $year, $years, 
		$search, $keywords, $logop, $field, $whichweek;
	global $searchWord;
	
    if ($keywords == "") {
        $keywords = "%";
    }

    $text = "";

	//////////////////////////////////////////////////////////////////////
	//
	// let's build the query!
	//
	//////////////////////////////////////////////////////////////////////

	//Search period
	$textperiod="";
	if ($period == "between") {
		$text .= "<SCRIPT>\n";
		$text .= " document.forms[0].period[1].checked=true;\n";
		$text .= " document.forms[0].startbet.value='$startbet';\n";
		$text .= " document.forms[0].endbet.value='$endbet';\n";
		$text .= "</SCRIPT>\n";
		
		$startdate=explode("-",$startbet);
		$enddate=explode("-",$endbet);
		$textperiod=" and TO_DAYS(AGENDA.stdate) >= TO_DAYS('$startbet') and TO_DAYS(AGENDA.stdate) <= TO_DAYS('$endbet')";
	}
	
	if ($period == "period") {
		$text .= "<SCRIPT>\n";
		$text .= " document.forms[0].period[0].checked=true;\n";
		$text .= " document.forms[0].day.selectedIndex=$day;\n";
		$text .= " document.forms[0].month.selectedIndex=$month;\n";
		$text .= " document.forms[0].year.selectedIndex=$year;\n";
		$text .= "</SCRIPT>\n";
		
		if ($day != 0) {
			$textperiod=" and ( DAYOFMONTH(AGENDA.stdate)=$day or DAYOFMONTH(SESSION.speriod1)=$day or DAYOFMONTH(TALK.tday)=$day )";
		}

		if ($month != 0) {
			$textperiod="$textperiod and ((MONTH(AGENDA.stdate) = $month) or (MONTH(SESSION.speriod1) = $month) or (MONTH(TALK.tday) = $month))";
		}

		if ($year != 0) {
			$textperiod="$textperiod and ((YEAR(AGENDA.stdate) = $year+($years[0]-1)) or (YEAR(SESSION.speriod1) = $year+($years[0]-1)) or (YEAR(TALK.tday) = $year))";
		}
	}	
	if ($period == "byweek") {
		$text .= "<SCRIPT>\n";
		$text .= " document.forms[0].period[2].checked=true;\n";
		$text .= " document.forms[0].whichweek.selectedIndex=$whichweek;\n";
		$text .= "</SCRIPT>\n";
				
		$now=time();
		$today=strftime("%Y-%m-%d",$now);
		$weekday=strftime("%w",$now);
		
		//Search this week
		if ($whichweek == 0) {
			$textperiod=" and (TO_DAYS(AGENDA.stdate) BETWEEN (TO_DAYS('$today') - $weekday) and (TO_DAYS('$today')+7-$weekday))";
		}

		if ($whichweek == 1) {
			$textperiod=" and (TO_DAYS(AGENDA.stdate) BETWEEN (TO_DAYS('$today')+7-$weekday) and (TO_DAYS('$today')+14-$weekday))";
		}

		if ($whichweek == 2) {
			$textperiod=" and (TO_DAYS(AGENDA.stdate) BETWEEN (TO_DAYS('$today')-7-$weekday) and (TO_DAYS('$today')-$weekday))";
		}
	}

	$keywords=addslashes($keywords);
	$keywords=addslashes($keywords);
	$keywords=addslashes($keywords);
	$words=explode(" ",$keywords);

	$searchWord="
select DISTINCT 
	AGENDA.title,
	AGENDA.stdate,
	AGENDA.endate,
	AGENDA.fid,
	AGENDA.id 
from 
	AGENDA
	left join TALK on AGENDA.id=TALK.ida
	left join SESSION on AGENDA.id=SESSION.ida
where 
	fid='<ID>'
	and (";

	$word = each($words);

	if ($field == "title") 
		{ $field1="ttitle"; $field2="title"; $field3="stitle"; }
	if ($field == "speaker") 
		{ $field1="tspeaker"; $field2="chairman"; $field3="schairman"; }
	if ($field == "agid") 
		{ $field1="ida"; $field2="id"; $field3="ida"; }

	$searchWord .= " (TALK.$field1 LIKE '%$word[1]%' or AGENDA.$field2 LIKE '%$word[1]%' or SESSION.$field3 LIKE '%$word[1]%'";

	//if field=title, we search also in the comments field.
	if ($field == "title") 
		{ $searchWord .= " or TALK.tcomment LIKE '%$word[1]%' or SESSION.scomment LIKE '%$word[1]%' or AGENDA.acomments LIKE '%$word[1]%')"; }
	else
		{ $searchWord .= ")"; }

	while ($word = each($words)) {
		$searchWord .= " $logop (TALK.$field1 LIKE '%$word[1]%' or AGENDA.$field2 LIKE '%$word[1]%'";		
		//if field=title, we search also in the comments field.
		if ($field == "title") 
			{ $searchWord .= " or TALK.tcomment LIKE '%$word[1]%' or AGENDA.acomments LIKE '%$word[1]%' or SESSION.scomment LIKE '%$word[1]%')"; }
		else 
			{ $searchWord .= ")"; }
	}
	$searchWord .= ") $textperiod";

    return $text;
}


function searchCategory($id,$text)
{
	global $result,$num,$searchWord;

	// search sub-categories
	$db  = &AgeDB::getDB();
	$sql = "select uid,title from LEVEL where fid='$id'";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}

	$numRows = $res->numRows();
	if ($numRows != 0) {
		for ($i=0; $i < $numRows; $i++) {
			$row = $res->fetchRow();
			$uid = $row['uid'];
			$title = $row['title'];
			searchCategory($uid,"$text > <A HREF=\"displayLevel.php?fid=" 
						   . $uid . "\">" . $title . "</A>");
		}
	}
	else {
		$request = str_replace("<ID>","$id",$searchWord);

		// find results
		$sql = $request . ' order by AGENDA.stdate desc';
		$res = $db->query($sql);
		if (DB::isError($res)) {
			die ($res->getMessage());
		}

		$numRows = $res->numRows();
		if ($numRows != 0) {
			$text = str_replace("<BR>"," ",$text);
			$result .= "</TABLE></CENTER>
<BR><font size=-1><B>$text</B> ($numRows result(s))</font><BR>&nbsp
<CENTER><TABLE bgcolor=lightgrey>\n";
		}	

		// store results
		$i=0;
		for ($j=0;$numRows > $j;$j++) {
			$row = $res->fetchRow();
			$title  = $row['title'];
			$id     = $row['id'];
			$fid    = $row['fid'];
			$stdate = $row['stdate'];
			$endate = $row['endate'];

			$num++;
			$title = ereg_replace("<BR>"," ",$title);
			$id = ereg_replace(" ","+",$id);
			$result .= "	<TR><TD align=center>$num.</TD><TD><FONT size=\"-1\" class=\"headline\"><A HREF=\"fullAgenda.php?ida=" . $id . "&amp;fid=" . $fid . "\">" . $title . "</A><BR>(" . $stdate . " / " . $endate . ")</FONT><BR></TD></TR>\n";
		}
	}
}
?>