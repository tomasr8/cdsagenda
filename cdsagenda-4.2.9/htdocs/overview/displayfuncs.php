<?php
// $Id: displayfuncs.php,v 1.1.1.1.4.12 2004/07/29 10:06:09 tbaron Exp $

// displayfuncs.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// displayfuncs.php is part of CDS Agenda.
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


///////////////////////////////////////////////////////////////////////
// function: displayAgendaAM
// description: this function returns a string containing the
//              description of all events taking place on a given day
//              in a set of categories (morning only)
// $params:
//    - $thisday: considered day
//    - $printable: if true, the html links are not created
//    - $display: 1=>display only agenda headers
//                2=>display only agenda/session headers
//                3=>display all headers
//    -$levelsettext: string representing a comma separated list
//                    of categories in which the events are searched
// $output:
//    - string
///////////////////////////////////////////////////////////////////////

function displayAgendaAM($thisday,$printable,$display,$levelsettext)
{
    global $IMAGES_WWW, $db;
    global $stylesheet,
        $format,
        $achairman,
        $aroom,
        $confidentiality,
        $sroom,
        $num;

    $resulttext = "";
    $num = 0;

    $requestAgenda = "
SELECT id,visibility,fid
from AGENDA
where
	fid IN $levelsettext and 
	fid != '3l52' and
	CONCAT(stdate,' ',stime) < '$thisday 13:00:00' and 
	CONCAT(endate,' ',etime) >= '$thisday 00:00:00'
order by stime
";
    $resAgenda = $db->query($requestAgenda);
    if (DB::isError($resAgenda)) {
        die ($resAgenda->getMessage());
    }
    $numRowsAgenda = $resAgenda->numRows();
    $resulttext .= "<TD valign=top bgcolor=gray>";
    $resulttext .= "<TABLE width=\"100%\" border=0 cellspacing=\"0\" cellpadding=\"2\">";

    while ( $row = $resAgenda->fetchRow() ) {
        $ida = $row['id'];
        $visibility = $row['visibility'];
        $mycateg = $row['fid'];

        $visibility = min($visibility,getCategVisibility($mycateg));

        if (isVisible($ida,$visibility,$mycateg)) {
            $num++;
            $displayed = TRUE;
            $resulttext .= displayAgendaHeader($ida,$printable,$thisday,"AM");
            
            $requestsession = "
SELECT 	
	ids
FROM	SESSION
WHERE	ida='$ida' and
	CONCAT(SESSION.speriod1,' ',SESSION.stime)<'$thisday 13:00:00' and 
	(CONCAT(SESSION.eperiod1,' ',SESSION.etime)>='$thisday 00:00:00' or etime IS NULL) and
	stitle!='dummy session'
order by stime";
            $res_sess = $db->query($requestsession);
            if (DB::isError($res_sess)) {
                die ($res_sess->getMessage());
            }
            $numRows_sess = $res_sess->numRows();
            
            for ( $j = 0; $numRows_sess > $j; $j++ ) {
                $row_sess = $res_sess->fetchRow();
                $ids = $row_sess['ids'];
                $resulttext .= displaySessionHeader($ida,$ids,$printable,$thisday);
                
                $requesttalk = "
SELECT DISTINCT
	idt
from
	TALK
where
	TALK.tday = '$thisday' and
	TALK.ida = '$ida' and
	TALK.ids = '$ids' and
	TALK.stime < '13:00:00' and
	TALK.ttitle != ''
order by
	TALK.ids,
	TALK.stime";

                
                $res_talk = $db->query($requesttalk);
                if (DB::isError($res_talk)) {
                    die ($res_talk->getMessage());
                }
                $numRows_talk = $res_talk->numRows();
            
                $numtalks = 0;
                for ( $k = 0; $numRows_talk > $k; $k++ ) {
                    $row_talk = $res_talk->fetchRow();
                    $idt = $row_talk['idt'];
                    $num++;
                    $numtalks++;
                    $lettertalk = chr(64+$numtalks);
                    if ($confidentiality != "password") {
                        $resulttext .= displayTalkHeader($ida,$ids,$idt,$printable,$format,$sroom,$display,$num,$lettertalk);
                    }
                } // end for ( $k = 0; $numRows_talk > $k; $k++ )
            } // end for ( $j = 0; $numRows_sess > $j; $j++ )
        
            if ($displayed) {
                $resulttext .= "<TR bgcolor=\"gray\"><TD colspan=\"2\"><IMG SRC=\"$IMAGES_WWW/transparent_height.gif\" ALT=\"\" HSPACE=0 VSPACE=0></TD></TR>\n";
            } // end if ($displayed)
        } // end if (isVisible(..))
    } // end while ( fetchRow() )
    if ($num == 0) {
        $resulttext .= "<TR><TD valign=top class=empty align=center><B class=emptytext>NO EVENTS</B></TD></TR>";
    } // end if ($num == 0)

    $resulttext .= "</TABLE>";
    $resulttext .= "</TD>";
    
    return $resulttext;
}





///////////////////////////////////////////////////////////////////////
// function: displayAgendaPM
// description: this function returns a string containing the
//              description of all events taking place on a given day
//              in a set of categories (afternoon only)
// $params:
//    - $thisday: considered day
//    - $printable: if true, the html links are not created
//    - $display: 1=>display only agenda headers
//                2=>display only agenda/session headers
//                3=>display all headers
//    -$levelsettext: string representing a comma separated list
//                    of categories in which the events are searched
// $output:
//    - string
///////////////////////////////////////////////////////////////////////

function displayAgendaPM($thisday,$printable,$display,$levelsettext) {
    global $IMAGES_WWW,$db;
    global $stylesheet,
        $format,
        $achairman,
        $aroom,
        $confidentiality,
        $sroom,
        $stime,
        $num;

    $num = 0;
    $resulttext = "";

    $requestagenda = "
SELECT id,visibility,fid
from AGENDA
where
	fid IN $levelsettext and 
	fid != '3l52' and
	CONCAT(stdate,' ',stime) <= '$thisday 23:59:59' and 
	CONCAT(endate,' ',etime) > '$thisday 13:00:00'
order by stime
";
    $resagenda = $db->query($requestagenda);

    if (DB::isError($resagenda)) {
        die ($resagenda->getMessage());
    }
    $numRowsAgenda = $resagenda->numRows();
    $resulttext .= "<TD valign=top bgcolor=gray>";
    $resulttext .= "<TABLE width=\"100%\" border=0 cellspacing=\"0\" cellpadding=\"2\">";

    
    while ($rowAgenda = $resagenda->fetchRow()) {
        $ida = $rowAgenda['id'];
        $visibility = $rowAgenda['visibility'];
        $mycateg = $rowAgenda['fid'];
        
        $visibility = min($visibility,getCategVisibility($mycateg));

        if (isVisible($ida,$visibility,$mycateg)) {
            $text = displayAgendaHeader($ida,$printable,$thisday,"PM");

            $textagenda = "";
            
            $requestsession = "
SELECT  ids,
        etime + 0 as time
FROM	SESSION
WHERE	ida='$ida' and
	CONCAT(SESSION.speriod1,' ',SESSION.stime)<='$thisday 23:59:59' and 
	CONCAT(SESSION.eperiod1,' ',SESSION.etime)>='$thisday 00:00:00' and
	stitle!='dummy session'
order by stime";
            $resSession = $db->query($requestsession);
            if (DB::isError($resSession)) {
                die ($resSession->getMessage());
            }
            $numRowsSession = $resSession->numRows();
        
            for ( $j = 0; $numRowsSession > $j; $j++ ) {
                $rowSession = $resSession->fetchRow();
                $ids = $rowSession['ids'];
                $etime = $rowSession['time'];
                $textsession = displaySessionHeader($ida,$ids,$printable,$thisday);
                
                $requesttalk = "
SELECT DISTINCT
	idt
from
	TALK
where
	TALK.tday = '$thisday' and
	TALK.ida = '$ida' and
	TALK.ids = '$ids' and
	TALK.stime >= '13:00:00' and
	TALK.ttitle != ''
order by
	TALK.ids,
	TALK.stime";
                $restalk = $db->query($requesttalk);
            
                if (DB::isError($restalk)) {
                    die ($restalk->getMessage());
                }
                $numRowsTalk = $restalk->numRows();
            
                $numtalksfound = $numRowsTalk;
                
                if ($confidentiality != "password" && 
                    ($numtalksfound != 0 || $etime >= 140000)) {
                    $textagenda .= $textsession;
                }
                
                $numtalks=0;
                for ( $k = 0; $numRowsTalk > $k; $k++ ) {
                    $rowTalk = $restalk->fetchRow();
                    $idt = $rowTalk['idt'];
                    $num++;
                    $numtalks++;
                    $lettertalk = chr(64+$numtalks);
                    if ($confidentiality != "password") {
                        $textagenda .= displayTalkHeader($ida,$ids,$idt,$printable,$format,$sroom,$display,$num,$lettertalk);
                    } // end if ($confidentiality != "password")
                } // end for ( $k = 0; $numRowsTalk > $k; $k++ )
            } // end for($j = 0; $numRowsSession > $j; $j++ )
            
            //Display AGENDA header
            if ($text != "") {
                $num++;
                $resulttext .= $text;
                $resulttext .= "$textagenda";
                $resulttext .= "<TR bgcolor=\"gray\"><TD colspan=\"2\"><IMG SRC=\"$IMAGES_WWW/transparent_height.gif\" ALT=\"\" HSPACE=0 VSPACE=0></TD></TR>\n";
            } // end if ($text != "")
        } // end if (isVisible(..))
    } // end while ($rowAgenda = $resagenda->fetchRow())
        
    // If no events were found
    if ($num == 0) {
        $resulttext .= "<TR><TD valign=top class=empty align=center><B class=emptytext>NO EVENTS</B></TD></TR>";
    }
    
    $resulttext .= "</TABLE>";
    $resulttext .= "</TD>";

    return $resulttext;
}




///////////////////////////////////////////////////////////////////////
// function: displayAgendaHeader
// description: this function returns a string containing the
//              description (header) of a given agenda
// $params:
//    - $thisday: considered day
//    - $ida: id of the agenda
//    - $printable: if true, the html links are not created
// $output:
//    - string
///////////////////////////////////////////////////////////////////////

function displayAgendaHeader($ida,$printable,$thisday,$timeofday)
{
    global $IMAGES_WWW,$db;
    global $stylesheet,
        $format,
        $achairman,
        $aroom,
        $confidentiality,
        $thislevel,
        $nbDisplayedIcons,
        $alocation,
        $mapServerACTIVE,
        $mapServerAddress;

    $requestagenda = "
SELECT
	title,
	id,
	confidentiality,
	fid,
	chairman,
	fid,
	stylesheet,
	format,
    location,
    room
from
	AGENDA
where
	id='$ida'";
    $resagenda = $db->query($requestagenda);
    if (DB::isError($resagenda)) {
        die ($resagenda->getMessage());
    }
    $numRowsAgenda = $resagenda->numRows();

    $rowAgenda = $resagenda->fetchRow();

    $fid = $rowAgenda['fid'];
    $format = $rowAgenda['format'];
    $stylesheet = $rowAgenda['stylesheet'];
    $confidentiality = $rowAgenda['confidentiality'];
    if ($confidentiality != "password") {
        // check with the global settings
        $sql = "SELECT accessPassword
                FROM LEVEL
                WHERE uid='$fid'";
        $res = $db->query($sql);
        $row = $res->fetchRow();
        if ($row['accessPassword']) {
            $confidentiality = "password";
        }
    }

    $achairman = $rowAgenda['chairman'];
	$alocation = $rowAgenda['location'];
	if ($alocation != "")
		$alocationtext = " <font size=\"-2\" color=\"lightblue\"> (<font color=lightblue>$alocation</font>)</font>";
	else
		$alocationtext = "";
    if ($achairman != "") {
        $achairmantext = "<font color=lightgreen size=-2> ($achairman)</font>";
    }
    else {
        $achairmantext = "";
    }
    $astime = Get_Stime($ida,$thisday,$timeofday);
    $astime = ereg_replace(":00$","",$astime);
    if ($astime != "" && $astime != "00:00") {
        $astimetext = ( $format != "olist" ? "<font size=\"-2\" color=\"white\">$astime&nbsp;-&nbsp;&nbsp;</font>" : "&nbsp;");
    }
    else {
        $astimetext = "";
    }
 
    $aroom = $rowAgenda['room'];
    if ($aroom != "" && $aroom != "--") {
        $bno = ereg_replace("(^-)*-.*","",$aroom);
        if ($bno == "") {
            $bno = "$aroom";
        }
        $bno = str_replace(" ","+",$bno);
        $aroomtext = ereg_replace("-*$","",$aroom);
        if ($aroomtext != "0") {
            if (!$printable) {
                $aroomtext = "<font size=\"-2\" color=\"lightblue\"> (".($mapServerACTIVE?"<A HREF=\"$mapServerAddress$bno\">":"")."<font color=lightblue>$aroom</font>".($mapServerACTIVE?"</A>":"").")</font>";
            }
            else {
                $aroomtext = "<font size=\"-2\" color=\"lightblue\"> (<font color=lightblue>$aroom</font>)</font>";
            }
        }
        else { 
            $aroomtext = ""; 
        }
    }
    else {
        $aroomtext = "";
    }
    // create the upper branch of the hierarchical tree for the current agenda
    $upperbranch = array();
    $upperbranch = getFatherTree($rowAgenda['fid']);
    $imagelevel = getIconLevel($upperbranch,$thislevel);
    $imagename = getIcon($imagelevel);
    if (!defined($nbDisplayedIcons[$imagename])) {
        $nbDisplayedIcons[$imagename] = 0;
    }
    $nbDisplayedIcons[$imagename] ++;

    if ($imagename == "") {
        $image = "";
        $colspan="2";
    }
    else {
        if (!$printable) {
            $image = "<TD valign=top><A HREF=\"\" onclick=\"document.forms[0].fid.value='$imagelevel';document.forms[0].submit();return false;\"><IMG SRC=\"$IMAGES_WWW/levelicons/$imagename.gif\" ALT=\"$array[1]\" border=0></A></TD>"; 
        }
        else {
            $image = "<TD valign=top><IMG SRC=\"$IMAGES_WWW/levelicons/$imagename.gif\" ALT=\"$array[1]\" border=0></TD>";
        }
        $colspan="1";
    }

    if ($confidentiality == "password") {
        if (!$printable) {
            $text = "<TR class=headerselected>$image<TD colspan=$colspan align=center>$astimetext<A HREF=\"../fullAgenda.php?ida=$ida#$thisday\"><font color=white><SMALL><B>".$rowAgenda['title']."</B></SMALL></font></A>$achairmantext$alocationtext$aroomtext<IMG SRC=\"$IMAGES_WWW/private.gif\" ALT=\"private\" border=0></TD></TR>";
        }
        else {
            $text = "<TR bgcolor=#000060>$image<TD colspan=$colspan align=center>$astimetext<font color=white><SMALL><B>".$rowAgenda['title']."</B></SMALL></font>$achairmantext$alocationtext$aroomtext<IMG SRC=\"$IMAGES_WWW/private.gif\" ALT=\"private\" border=0></TD></TR>";
        }
    }
    else {
        if (!$printable) {
            $text = "<TR class=headerselected>$image<TD colspan=$colspan align=center>$astimetext<A HREF=\"../fullAgenda.php?ida=$ida#$thisday\"><font color=white><SMALL><B>".$rowAgenda['title']."</B></SMALL></font></A>$achairmantext$alocationtext$aroomtext</TD></TR>";
        }
        else {
            $text = "<TR bgcolor=#000060>$image<TD colspan=$colspan align=center>$astimetext<font color=white><SMALL><B>".$rowAgenda['title']."</B></SMALL></font>$achairmantext$alocationtext$aroomtext</TD></TR>";
        }
    }

    return $text;
}





///////////////////////////////////////////////////////////////////////
// function: displaySessionHeader
// description: this function returns a string containing the
//              description (header) of a given session
// $params:
//    - $thisday: considered day
//    - $ida: id of the agenda
//    - $ids: id of the session
//    - $printable: if true, the html links are not created
// $output:
//    - string
///////////////////////////////////////////////////////////////////////

function displaySessionHeader($ida,$ids,$printable,$thisday)
{
    global $sroom,
        $confidentiality,
        $stime,
        $stylesheet,
        $display,
        $alocation,
        $slocation,
        $mapServerACTIVE,
        $mapServerAddress,
        $db;

    $requestsession = "
SELECT 	stitle,
	room,
	ids,
	schairman,
	stime,
	speriod1,
    slocation
FROM	SESSION
WHERE	ida='$ida' and
	ids = '$ids'
order by stime";
    $ressession = $db->query($requestsession);
    if (DB::isError($ressession)) {
        die ($ressession->getMessage());
    }
    $numRows = $ressession->numRows();
    
    $rowSession = $ressession->fetchRow();
    $sroom = $rowSession['room'];
	$slocation = $rowSession['slocation'];
	if ($slocation != "" && $slocation != "$alocation")
		$slocationtext = "<font size=\"-2\"> ($slocation)</font>";
	else
		$slocationtext = "";
    $stime = ereg_replace(":00$","",$rowSession['stime']);
    $sday = $rowSession['speriod1'];
    if ($stime == "00:00" || $thisday != $sday) {
        $stime = "&nbsp;";
    }
    $stime = ($format != "olist" ? $stime : "&nbsp;");
    if ($rowSession['schairman'] != "" && $rowSession['schairman'] != "$achairman") {
        $schairman = "<font size=-2> (<font color=green>".$rowSession['schairman']."</font>)</font>";
    }
    else {
        $schairman = "";
    }

    if ($rowSession['room'] != "" && $rowSession['room'] != $aroom) {
        $bno = ereg_replace("(^-)*-.*","",$rowSession['room']);
        if ($bno == "") {
            $bno = $rowSession['room'];
        }
        $bno = str_replace(" ","+",$bno);
        $rowSession['room'] = ereg_replace("-*$","",$rowSession['room']);
        if ($rowSession['room'] != "0") {
            if (!$printable) {
                $sesroom = "<font size=\"-2\"> (".($mapServerACTIVE?"<A HREF=\"$mapServerAddress$bno\">":"").$rowSession['room'].($mapServerACTIVE?"</A>":"").")</font>";
            }
            else {
                $sesroom = "<font size=\"-2\"> (".$rowSession['room'].")</font>";
            }
        }
        else {
            $sesroom = "";
        }
    }
    else {
        $sesroom = "";
    }

    if ($confidentiality != "password" 
        && $display != 1 
        && $rowSession['stitle'] != "" 
        && $stylesheet != "nosession" 
        && $stylesheet != "seminars") {
        if (!$printable) {
            $text = "<TR><TD valign=top bgcolor=\"#b0e0ff\"><font size=\"-2\"><B>$stime</B></FONT></TD><TD align=left class=header><A HREF=\"../fullAgenda.php?ida=$ida#$ids\"><font size=\"-2\" color=black><B>".$rowSession['stitle']."</B></font></A>$schairman$slocationtext$sesroom</TD></TR>";
        }
        else {
            $text = "<TR><TD valign=top bgcolor=\"#b0e0ff\"><font size=\"-2\"><B>$stime</B></FONT></TD><TD align=left bgcolor=#90c0f0><font size=\"-2\" color=black><B>".$rowSession['stitle']."</B></font>$schairman$slocationtext$sesroom</TD></TR>";
        }
    }
    
    return $text;
}



///////////////////////////////////////////////////////////////////////
// function: displayTalkHeader
// description: this function returns a string containing the
//              description (header) of a given talk
// $params:
//    - $thisday: considered day
//    - $ida: id of the agenda
//    - $ids: id of the session
//    - $idt: id of the talk
//    - $printable: if true, the html links are not created
//    - $format: can be "olist" (ordered list) or anything else
//    - $sroom: session room
//    - $display: 1=>display only agenda headers
//                2=>display only agenda/session headers
//                3=>display all headers
//    - $num: talk number
//    - $lettertalk: letter associated with the talk (if display is
//                   "olist")
// $output:
//    - string
///////////////////////////////////////////////////////////////////////

function displayTalkHeader($ida,$ids,$idt,$printable,$format,$sroom,$display,$num,$lettertalk)
{
    global $stylesheet,
        $slocation,
        $alocation,
        $mapServerACTIVE,
        $mapServerAddress,
        $db;

    $requesttalk = "
SELECT DISTINCT
	type,
	ttitle,
	tspeaker,
	stime,
	room,
	ida,
	category,
	ids,
    location
from
	TALK
where
	TALK.ida = '$ida' and
	TALK.ids = '$ids' and
	TALK.idt = '$idt'";
    $restalk = $db->query($requesttalk);
    if (DB::isError($restalk)) {
        die ($restalk->getMessage());
    }
    $numRowsTalk = $restalk->numRows();
    $rowTalk = $restalk->fetchRow();

	$tlocation = $rowTalk['location'];
	if ($tlocation != "" && 
        (($tlocation != $slocation && $stylesheet != "nosession") ||
         ($tlocation != $alocation && $stylesheet == "nosession")  ))
		$tlocationtext = " ($tlocation)";
	else
		$tlocationtext = "";

    $stime = ereg_replace(":00$","",$rowTalk['stime']);
    if ($stime == "00:00") {
        $stime = "&nbsp;";
    }

    $stime = ($format != "olist" ? $stime : "$lettertalk");
    if ($rowTalk['type'] == 1) {
        if ( $rowTalk['tspeaker'] != "" ) {
            $rowTalk['tspeaker'] = "(<font color=green size=-2>".$rowTalk['tspeaker']."</font>)";
        }
        $rowTalk['ttitle'] = stripslashes($rowTalk['ttitle']);
        $rowTalk['category'] = ereg_replace("[\n\r]+","",$rowTalk['category']);
        if ($rowTalk['category'] != "" && $rowTalk['category'] != " ") {
            $category = "<font size=\"-2\"><B>".$rowTalk['category']."</B></font><BR>\n";
        }
        else {
            $category = "";
        }
			
        if ($rowTalk['room'] != "" && $rowTalk['room'] != $sroom) {
            $bno = ereg_replace("(^-)*-.*","",$rowTalk['room']);
            if ($bno == "") {
                $bno = $rowTalk['room'];
            }
            $bno = str_replace(" ","+",$bno);
            $rowTalk['room'] = ereg_replace("-*$","",$rowTalk['room']);
            if ($rowTalk['room'] != "0") {
                if (!$printable) {
                    $room = " (".($mapServerACTIVE?"<A HREF=\"$mapServerAddress$bno\">":"").$rowTalk['room'].($mapServerACTIVE?"</A>":"").")";
                }
                else {
                    $room = " (".$rowTalk['room'].")";
                }
            }
            else {
                $room = "";
            }
        } // end if ($rowTalk['room'] != "" && $rowTalk['room'] != $sroom)
        else {
            $room = "";
            if ($rowTalk['room'] == "$sroom") {
                $room = "";
            }
        } // end else if ($rowTalk['room'] != "" && $rowTalk['room'] != $sroom)

        if ($display == 3) {
            if ((int)($num/2) == ($num/2)) {
                $text = "<TR><TD valign=top bgcolor=\"#E2E2E2\" align=middle><font size=\"-2\">$stime</FONT></TD><TD bgcolor=\"#D2D2D2\">$category<font size=\"-2\">".$rowTalk['ttitle'] ." ". $rowTalk['tspeaker'] ."$tlocationtext$room</font></TD></TR>";
            }
            else {
                $text = "<TR><TD valign=top bgcolor=\"#D0D0D0\" align=middle><font size=\"-2\">$stime</FONT></TD><TD bgcolor=\"silver\">$category<font size=\"-2\">". $rowTalk['ttitle'] ." ". $rowTalk['tspeaker'] ."$tlocationtext$room</font></TD></TR>";
            }
        } // end if ($display == 3)
    } // end if ($rowTalk['type'] == 1)
    else {
        if ($display == 3) {
            $text = "<TR><TD valign=top BGCOLOR=\"#ffdcdc\"><font size=\"-2\">$stime</FONT></TD><TD align=center BGCOLOR=\"#FFcccc\"><font size=\"-2\">---".$rowTalk['ttitle']."---</font></TD></TR>";
        }
    } // end else if ($rowTalk['type'] == 1)
    
    return $text;
}

?>