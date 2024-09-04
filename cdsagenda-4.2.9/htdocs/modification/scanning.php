<?php
// $Id: scanning.php,v 1.1.1.1.4.6 2003/03/28 10:20:03 tbaron Exp $

// scanning.php --- request for scanning
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// scanning.php is part of CDS Agenda.
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

require_once("../config/config.php");
require_once( "../platform/template/template.inc" );
require_once('../AgeDB.php');

$db = &AgeDB::getDB();

// new template
$Template = new Template( $PathTemplate );
// Check if the scanning service if ACTIVE
if ( !$scanServiceACTIVE )
{
    // Template set-up
    $Template->set_file(array( "mainpage"=> "scanningOff.ihtml" ));

    $Template->set_var( "scanningOff_contactus", $support_email );
    $Template->pparse( "final-page", "mainpage" );
    exit;
}
// Template set-up
$Template->set_file(array( "mainpage"=> "scanning.ihtml" ));

$agendadir="/archive/electronic/other/agenda/$ida";

$Template->set_var("images","$IMAGES_WWW");

$Template->set_var("scanning_CATEG","$categ");
$Template->set_var("scanning_TYPE","$type");
$Template->set_var("scanning_BASE","$base");
$Template->set_var("scanning_IDA","$ida");
$Template->set_var("scanning_IDS","$ids");
$Template->set_var("scanning_IDT","$idt");
$Template->set_var("scanning_FROM","$from");
$Template->set_var("scanning_AN","$AN");
$Template->set_var("scanning_POSITION","$position");
$Template->set_var("scanning_STYLESHEET","$stylesheet");

$Template->set_var( "str_help", "" );
$Template->set_var( "str_help2", "" );
$Template->set_var( "str_help3", "" );
$Template->set_var( "str_help4", "" );
if ($from == "session")
{
    $query = "select ttitle,ida,ids,idt  from TALK where ida='$ida' and ids='$ids' and type=1 order by tday,stime";

    $arr = $db->query($query);
    if (DB::isError($arr)) {
        die ($arr->getMessage());
    }
    $numRows = $arr->numRows();
    $nbTalk=0;

    if ( $numRows != 0 )
        {
            $str_help="";
            for ( $i = 0; $numRows > $i; $i++ )
                {
                    $row = $arr->fetchRow();
                    $title=stripslashes( $row['ttitle'] );
                    $nbTalk++;

                    // cutted print
                    $str_help.="<TR><TD align=right class=headerselected><SMALL>$title</SMALL></TD><TD class=header><SMALL><INPUT type=checkbox name='name$nbTalk' value='" . $row['ida'] . $row['ids'] . $row['idt'] . ";" . $row['ttitle'] . "'></SMALL></TD><TD><SMALL><INPUT size=2 name=nb$nbTalk value=1> document(s)</SMALL></TD></TR>";

                    $query2 = "select ttitle,ida,ids,idt from SUBTALK where ida=' " . $row['ida'] . "' and ids='" . $row['ids'] . "' and fidt='" . $row['idt'] . "' order by ttitle";
                    
                    $arr2 = $db->query($query2);
                    if (DB::isError($arr2)) {
                        die ($arr2->getMessage());
                    }
                    $numRows2 = $arr2->numRows();
                    if ( $numRows2 != 0 )
                        {
                            $str_help2="";
                            for ( $i2 = 0; $numRows2 > $i2; $i2++ )
                                {
                                    $row2 = $arr2->fetchRow();
                                    $subtitle = stripslashes( $row2['ttitle'] );
                                    $nbTalk++;

                                    // cutted print
                                    $str_help2.="<TR><TD align=right class=headerselected><SMALL>$subtitle</SMALL></TD><TD class=header><SMALL><INPUT type=checkbox name='name$nbTalk' value='" . $row2['ttitle'] . "'></SMALL></TD><TD><SMALL><INPUT size=2 name=nb$nbTalk value=1> document(s)</SMALL></TD></TR>";
                                }
                        }
                }
        }
    $Template->set_var("str_help","$str_help");
    $Template->set_var("str_help2","$str_help2");
}

if ($from == "talk")
{
    $query = "select ttitle,ida,ids,idt from TALK where ida='$ida' and ids='$ids' and idt='$idt' and type=1";
    $arr = $db->query($query);
    if (DB::isError($arr)) {
        die ($arr->getMessage());
    }
    $numRows = $arr->numRows();

    $str_help3="";

    if ( $numRows != 0 )
        {
            $row = $arr->fetchRow();

            $nbTalk=1;
            $title=stripslashes( $row['ttitle'] );

            // cutted print
            $str_help3="<TR><TD align=right class=headerselected><SMALL>$title</SMALL></TD><TD class=header><SMALL><INPUT type=checkbox name='name$nbTalk' value='" . $row['ida'] . $row['ids'] . $row['idt'] . ";" . $row['ttitle'] . "' checked></SMALL></TD><TD><SMALL><INPUT size=2 name=nb$nbTalk value=1> document(s)</SMALL></TD></TR>";

            $query2 = "select ttitle,ida,ids,idt from SUBTALK where ida='$arr[1]' and ids='$arr[2]' and fidt='$arr[3]' order by ttitle";
            $arr2 = $db->query($query2);
            if (DB::isError($arr2)) {
                die ($arr2->getMessage());
            }
            $numRows2 = $arr2->numRows();

            if ( $numRows2 != 0 )
                {
                    $str_help4="";
                    for ( $i2 = 0; $numRows2 > $i2; $i2++ )
                        {
                            $row2 = $arr2->fetchRow();
                            $subtitle = stripslashes( $row2['ttitle'] );
                            $nbTalk++;

                            // cutted print
                            $str_help4.="<TR><TD align=right class=headerselected><SMALL>$subtitle</SMALL></TD><TD class=header><SMALL><INPUT type=checkbox name='name$nbTalk' value='" . $row['ida'] . $row['ids'] . $row['idt'] . ";" . $row['ttitle'] . "'></SMALL></TD><TD><SMALL><INPUT size=2 name=nb$nbTalk value=1> document(s)</SMALL></TD></TR>";
                        }
                    $Template->set_var("str_help4","$str_help4");
                }
        }
    else
        {
            $query = "select ttitle,ida,ids,idt from SUBTALK where ida='$ida' and ids='$ids' and idt='$idt'";

            $arr = $db->query($query);
            if (DB::isError($arr)) {
                die ($arr->getMessage());
            }
            $numRows = $arr->numRows();
            $row = $arr->fetchRow();
            $nbTalk=1;
            $title=        stripslashes( $row['ttitle'] );

            // cutted print
            $str_help3="<TR><TD align=right class=headerselected><SMALL>$title</SMALL></TD><TD class=header><SMALL><INPUT type=checkbox name='name$nbTalk' value='" . $row['ida'] . $row['ids'] . $row['idt'] . ";" . $row['ttitle'] . "' checked></SMALL></TD><TD><SMALL><INPUT size=2 name=nb$nbTalk value=1> document(s)</SMALL></TD></TR>";
        }
    $Template->set_var("str_help3","$str_help3");
}

// cutted echo

$Template->set_var("scanning_NBTALK","$nbTalk");

$str_help5="";

if(isset($HTTP_COOKIE_VARS["requester"]))
{
    $field = $HTTP_COOKIE_VARS["requester"];
    $str_help5.="document.forms[0].requester.value = '$field'\n";
}
if(isset($HTTP_COOKIE_VARS["email"]))
{
    $field = $HTTP_COOKIE_VARS["email"];
    $str_help5.="document.forms[0].email.value = '$field'\n";
}
if(isset($HTTP_COOKIE_VARS["tel"]))
{
    $field = $HTTP_COOKIE_VARS["tel"];
    $str_help5.="document.forms[0].tel.value = '$field'\n";
}
if(isset($HTTP_COOKIE_VARS["back"]))
{
    $field = $HTTP_COOKIE_VARS["back"];
    $str_help5.="document.forms[0].back.value = '$field'\n";
}
if(isset($HTTP_COOKIE_VARS["bcode"]))
{
    $field = $HTTP_COOKIE_VARS["bcode"];
    $str_help5.="document.forms[0].bcode.value = '$field'\n";
}
if(isset($HTTP_COOKIE_VARS["bholder"]))
{
    $field = $HTTP_COOKIE_VARS["bholder"];
    $str_help5.="document.forms[0].bholder.value = '$field'\n";
}

$Template->set_var("str_help5","$str_help5");

$Template->pparse( "final-page" , "mainpage" );

?>