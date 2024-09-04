<?php
// $Id: TimeTableEdit.php,v 1.1.1.1.4.5 2003/03/28 10:25:08 tbaron Exp $

// TimeTableEdit.php --- Modification area: modify session data
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// TimeTableEdit.php is part of CDS Agenda.
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

require_once '../AgeDB.php';
require_once "../config/config.php";
require_once "../platform/template/template.inc";
require_once "../platform/system/commonfunctions.inc";

// new template
$Template = new Template( $PathTemplate );
// Template set-up
$Template->set_file(array( "mainpage" => "TimeTableEdit.ihtml",
                           "JSMenuTools" => "JSMenuTools.ihtml",
                           "JSMenuToolsAdd" => "JSMenuToolsAdd.ihtml",
                           "error"=>"error.ihtml" ));	

if ($minH == null) {
	$minH = 7;
}
	
if ($maxH == null) {
	$maxH = 22;
}
	
if ($sizefactor == null) {
	$sizefactor = 1; 
}

$colorsession[1] = "#90c0f0";
$colorsession[2] = "#f0c090";
$colorsession[3] = red;
$colorsession[4] = cyan;
	
$db = &AgeDB::getDB();

if ($todo == "save") {
	if (isset($HTTP_POST_VARS)) {
		reset ($HTTP_POST_VARS);
		while (list ($key, $val) = each ($HTTP_POST_VARS)) {
			if (ereg("^newstart",$key)) {
				$idt = str_replace("newstart","",$key);
				$ids = ereg_replace("t.*","",$idt);
				$idt = str_replace("$ids","",$idt);
				$newstart = $val/$sizefactor + 60*$minH - 50/$sizefactor;
				$newhour = (int)($newstart/60);
				$newmin = $newstart - $newhour*60;
				$sql = "update TALK set stime='$newhour:$newmin:0' 
                        where ida='$ida' and ids='$ids' and idt='$idt'";
				$res = $db->query($sql);
				if (DB::isError($res)) {
					die ($res->getMessage());
				}
			}
		}
    }
}


$Template->set_var("images", $IMAGES_WWW);
$Template->set_var("timetableedit_minh", $minH);
$Template->set_var("timetableedit_maxh", $maxH);

$Template->parse( "timetableedit_jsmenutools", "JSMenuTools", true );

$Template->set_var("jsmenutoolsadd_sizefactor", $sizefactor);
$Template->parse( "timetableedit_jsmenutoolsadd", "JSMenuToolsAdd", true );

$nbTalks = 0;

if (!$thisday && !get_first_day($ida)) {
	outError("Sorry, you cannot edit the time table for this agenda... No talk is available yet...","01",&$Template);
	exit;
}

if (!$thisday) {
	$thisday = get_first_day($ida);
}

$Template->set_var("timetableedit_sizefactor", $sizefactor);
$Template->set_var("timetableedit_ida", $ida);
$Template->set_var("timetableedit_ids", $ids);
$Template->set_var("timetableedit_fid", $fid);
$Template->set_var("timetableedit_level", $level);

$db = &AgeDB::getDB();
$body = "";

$sql = "select HOUR(stime),MINUTE(stime)
        AS minutes,idt,ttitle,duration,ids 
        from TALK 
        where ida='$ida' and tday='$thisday'";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();

if ($numRows != 0) {
	for ( $i = 0; $numRows > $i; $i++ ) {
		$row = $res->fetchRow();
		$body .= "<INPUT type=hidden name=newstart" . 
			$row['ids'] . 
			$row['idt'] . 
			">\n";
	}
}

if ($sizefactor == 1) {
	$body .= "<A HREF=\"\" onClick=\"if (changed) { if (confirm('Changes have not been saved\\nAre you sure you want to zoom (changes will be lost)?')) {document.forms[0].sizefactor.value=2;document.forms[0].submit();return false;} else return false;} else {document.forms[0].sizefactor.value=2;document.forms[0].submit();return false;}\"><IMG BORDER=0 WIDTH=16 HEIGHT=16 SRC=\"$IMAGES_WWW/zoom.gif\" alt=\"zoom!\"></A>\n";
}
else {
	$body .= "<A HREF=\"\" onClick=\"if (changed) { if (confirm('Changes have not been saved\\nAre you sure you want to unzoom (changes will be lost)?')) {document.forms[0].sizefactor.value=1;document.forms[0].submit();return false;} else return false;} else {document.forms[0].sizefactor.value=1;document.forms[0].submit();return false;}\"><IMG BORDER=0 WIDTH=16 HEIGHT=16 SRC=\"$IMAGES_WWW/unzoom.gif\" alt=\"unzoom!\"></A>\n";
}
$body .= "
</TD>
</TR>
</TABLE>
<BR>
<font size=-2>\n";

$body .= getdays($ida);

$body .= "
</font>
<BR><BR>
<font size=-1><INPUT size=5 name=indic></font>
<BR><BR>
<font size=-1>
<INPUT type=\"radio\" name=dragtype onClick=\"DRAGtype=1;\" value=\"simple\" checked> simple<BR>
<INPUT type=\"radio\" name=dragtype onClick=\"DRAGtype=2;\" value=\"grouped\"> grouped
</font>
</FORM>\n";


$sql = "select TALK.ids,stitle 
        from TALK,
	         SESSION
        where TALK.ids=SESSION.ids and 
              TALK.ida=SESSION.ida and 
              TALK.ida='$ida' and 
              TALK.tday='$thisday' 
        group by TALK.ids 
        order by TALK.stime";
$res = $db->query($sql);
if (DB::isError($res)) {
    die ($res->getMessage());
}
$numRows = $res->numRows();
$numsession = 0;  

if ($numRows != 0) {
	for ( $i = 0; $numRows > $i; $i++ ) {
		$row = $res->fetchRow();
		$numsession ++;

		$body .= "
	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
		sessions[$numsession] = new Array();
		numtalks[$numsession] = 0; 
		numsession ++;
	</SCRIPT>\n";

		$ids = $row['ids'];
		for ($j=$minH;$j<=$maxH;$j++) {
			$body .= displayHourTable($j,$j-$minH+1,$numsession);
		}

		$sql = "select HOUR(stime) AS hours,
                       MINUTE(stime) AS minutes,
                       idt,ttitle,duration,ids 
                from TALK 
                where ida='$ida' and ids='$ids'";
		$res2 = $db->query($sql);
		if (DB::isError($res2)) {
			die ($res2->getMessage());
		}
		$numRows2 = $res2->numRows();

		if ($numRows2 != 0) {
			for ( $i2 = 0; $numRows2 > $i2; $i2++ ) {
				$row2 = $res2->fetchRow();
				$body .= displayTalk($row2['hours'],
							$row2['minutes'],
							$row2['idt'],
							$row2['ttitle'],
							$row2['duration'],
							$row2['ids'],
							$numsession);
			}
		}
	}
}

$body .= displayKey($numsession);

$Template->set_var("timetableedit_body", $body);

$jsfunctions = "
	function Initialize(idt)
	{\n";
for ($i = 1;$i <= $nbTalks; $i++) {
    $jsfunctions .= "
		if (idt == '$id[$i]')
		{
			setLeft(getObject(idt),$initialX[$i]);
			setTop(getObject(idt),$initialY[$i]);
		}\n";
}
$jsfunctions .= "
		changed = 0; 
	}\n";

$jsfunctions .= "
	function InitializeAll()
	{\n";
for ($i = 1;$i <= $nbTalks; $i++) {
    $jsfunctions .= "
			Initialize('$id[$i]');\n";
}
$jsfunctions .= "
	}\n";

$jsfunctions .= "
	function LeftAlign()
	{\n";
for ($i = 1;$i <= $nbTalks; $i++) {
    $jsfunctions .= "
			setLeft(getObject('$id[$i]'),150);\n";
}
$jsfunctions .= "
	}\n";

$jsfunctions .= "
	function setYForm(idt,Yvalue)
	{";
for ($i = 1;$i <= $nbTalks; $i++) {
    $jsfunctions .= "
		if (idt == '$id[$i]')
		{
			document.forms[0].newstart$id[$i].value = Yvalue;
		}\n";
}
$jsfunctions .= "
	}

function setIndic(Yvalue) {	
   if (Yvalue%(5*$sizefactor) != 0) {
      Yvalue = Yvalue - Yvalue%(5*$sizefactor);
   }
   newstart = Yvalue/$sizefactor + 60*minH - 50/$sizefactor ;
   newmin = newstart%60;
   newhour = (newstart - newmin)/60;
   if (newmin < 10) { newmin = \"0\" + newmin; }
   time = newhour + \":\" + newmin;
   document.forms[0].indic.value = time;
}\n";


$Template->set_var("timetableedit_jsfunctions", $jsfunctions);
$Template->pparse("final-page", "mainpage");






function displayHourTable($hour, $num, $numsession) {
	global $sizefactor,$IMAGES_WWW;

    $text = "";
	$top = 50 + ($num-1)*60*$sizefactor;
	$left = 150 + ($numsession-1)*170;
	$height = 60 * $sizefactor;
	if ($hour < 10) { 
		$hourtext = "0$hour"; 
	} 
	else { 
		$hourtext = $hour; 
	}
	if ($sizefactor == 1) { 
		$image = "smallhourtable.jpg"; 
	}
	else { 
		$image = "bighourtable.jpg"; 
	}
	$text .= "
	<DIV 	ID=\"hour$hour\"
		STYLE=\"POSITION: absolute; Z-INDEX: 0; VISIBILITY: show; left: $left; top: $top;\">
		<TABLE cellpadding=0 cellspacing=0 border=0>
		<TR>
			<TD valign=top><font size=-1><b>$hourtext:00&nbsp;&nbsp;</B></font></TD>
			<TD><IMG SRC=\"$IMAGES_WWW/$image\" WIDTH=120 HEIGHT=$height ALT=\"hour $hour\" ID=\"hourtable$num\"></TD>
		</TR>
		</TABLE>
	</DIV>\n";
    
    return $text;
}

function displayTalk($hour, 
					 $min, 
					 $idt, 
					 $ttitle, 
					 $duration, 
					 $ids, 
					 $numsession) {
	global $minH,
		$nbTalks,
		$id,
		$initialX,
		$initialY,
		$colorsession,
		$sizefactor,
		$IMAGES_WWW;
	$nbTalks++;
    $text = "";

	$duration = split(":",$duration,3);
	$mduration = $duration[0]*60 + $duration[1];
	$height = $mduration*$sizefactor - 2;
	if ($height < 3) {
		$height = 3;
	}

	$sttitle = substr($ttitle,0,16);

	$top = 50 + (($hour-$minH)*60 + $min)*$sizefactor;
	if ($top < 50) { 
		$top=50; 
	}
	$left = 200 + ($numsession-1)*170;
			
	$initialX[$nbTalks] = $left;
	$initialY[$nbTalks] = $top;
	$id[$nbTalks] = "$ids$idt";

	$text .= "

	<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
		document.forms[0].newstart$ids$idt.value = $top;
	</SCRIPT>

	<DIV 	ID=\"$ids$idt\"
		STYLE=\"POSITION: absolute; Z-INDEX: 0; VISIBILITY: show; left: $left; top: $top; width: 200;\"
		onClick=\"event.cancelBubble = true;\"
		onmousedown=\"DRAG_begindragTTE(event,'$ids$idt');\">
		<TABLE border=0 cellpadding=0 cellspacing=0>
		<TR>
			<TD valign=top>
				<TABLE bgcolor=black cellpadding=0 cellspacing=0 border=0 width=100%>
				<TR>
					<TD valign=top>
					<TABLE bgcolor=$colorsession[$numsession] cellpadding=0 cellspacing=0 border=0 width=100%>
					<TR>
						<TD><IMG SRC=\"$IMAGES_WWW/backgroundtalk.gif\" WIDTH=50 HEIGHT=$height ALT=\"$ttitle\" ID=\"hourtable$num\" border=1></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				</TABLE>
			</TD>
			<TD><font size=-2>&nbsp;$sttitle ($mduration')</font></TD>
		</TR>
		</TABLE>
	</DIV>
	
<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">
	numtalks[$numsession] ++;
	sessions[$numsession][numtalks[$numsession]] = getObject('$ids$idt');

if (NS4) {
	document.layers['$ids$idt'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
	document.layers['$ids$idt'].onmousedown=DRAG_begindragTTE;
	document.layers['$ids$idt'].onmouseup=DRAG_enddragTTE;
}

</SCRIPT>\n";

    return $text;
}


function get_first_day($ida) {
	$db = &AgeDB::getDB();
	$sql = "select tday from TALK where ida='$ida' order by tday LIMIT 1";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	if ($numRows != 0) {
		$row = $res->fetchRow();
		return $row['tday'];
	}
	else {
		return 0;
	}
}

function getdays($ida) {
	global $thisday;

    $text = "";

	$db = &AgeDB::getDB();
	$sql = "select tday from TALK where ida='$ida' 
            group by tday order by tday";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	$text .= "<SELECT name=thisday onChange=\"if (changed) { if (confirm('Changes have not been saved\\nAre you sure you want to quit this day (changes will be lost)?')) {document.forms[0].submit();}} else {document.forms[0].submit();}\" >\n";

	if ($numRows != 0) {
		for ($i = 0; $numRows > $i; $i++) {
			$row = $res->fetchRow();
			if ($row['tday'] == $thisday) {
				$text .= "<OPTION selected>" . $row['tday'] . "\n";
			}
			else {
				$text .= "<OPTION>" . $row['tday'] . "\n";
			}
		}
	}

	$text .= "</SELECT>\n";

    return $text;
}

function displayKey($numsession) {
	global $ida,$thisday,$colorsession,$IMAGES_WWW;

    $text = "";
	$left = 200 + $numsession*170;
	$top = 200;

	$text .= "
	<DIV ID=key STYLE=\"POSITION: absolute; Z-INDEX: 0; VISIBILITY: show; left: $left; top: $top;\">
		<TABLE border=0 cellpadding=0 cellspacing=0>";

	$db = &AgeDB::getDB();
	$sql = "
select 
	stitle 
from 
	TALK
	left join SESSION on TALK.ids=SESSION.ids and TALK.ida=SESSION.ida
where 	
	TALK.ida='$ida' and TALK.tday='$thisday' 
group by 
	TALK.ids 
order by 
	TALK.stime";
	$res = $db->query($sql);
	if (DB::isError($res)) {
		die ($res->getMessage());
	}
	$numRows = $res->numRows();

	$num = 0;
		
	if ($numRows != 0) {
		for ($i = 0; $numRows > $i; $i++) {
			$row = $res->fetchRow();
			$num++;
			$text .= "
		<TR>
			<TD valign=top>
				<TABLE bgcolor=black cellpadding=0 cellspacing=0 border=0 width=100%>
				<TR>
					<TD valign=top>
					<TABLE bgcolor=$colorsession[$num] cellpadding=0 cellspacing=0 border=0 width=100%>
					<TR>
						<TD><IMG SRC=\"$IMAGES_WWW/backgroundtalk.gif\" WIDTH=50 HEIGHT=15 ALT=\"$row[0]\" ID=\"hourtable$num\" border=1></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				</TABLE>
			</TD>
			<TD><font size=-1>&nbsp;" . $row['stitle'] . "</font></TD>
		</TR><TR><TD>&nbsp;</TD></TR>\n";
		}
	}
	$text .= "</TABLE></DIV>\n";

    return $text;
}

?>