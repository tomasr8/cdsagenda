<?php
// $Id: scanningSend.php,v 1.1.1.1.4.2 2002/10/23 08:56:36 tbaron Exp $

// scanningSend.php --- 
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// scanningSend.php is part of CDS Agenda.
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

setcookie("requester","$requester",time()+31104000);
setcookie("email","$email",time()+31104000);
setcookie("tel","$tel",time()+31104000);
setcookie("back","$back",time()+31104000);
setcookie("bcode","$bcode",time()+31104000);
setcookie("bholder","$bholder",time()+31104000);
?>

<HTML>
<HEAD>

<?
echo "<TITLE>SCANNING REQUEST - END</TITLE>";
?>
<SCRIPT>

function tester()
{
  ok=true;
  if (document.SCAN.requester.value=="") { ok=false; }
  if (document.SCAN.tel.value=="") { ok=false; }
  if (document.SCAN.back.value=="") { ok=false; }
  if (document.SCAN.bcode.value=="") { ok=false; }
  if (document.SCAN.bholder.value=="") { ok=false; }
  if ( ok == false)
    {
      alert("Please fill in all the fields");
      return false;
    }
  else 
    {	
      return true;
    }
}
</SCRIPT>


</HEAD>
<BODY TEXT=black BGCOLOR="#FFFFFF" LINK="#009900" VLINK="#006600" ALINK="#FF0000">

<?    
if ($ida == "")
{
  print "No standalone acces please!";
  exit();
}

$agendadir="/archive/electronic/other/agenda/$ida";
$message="\n";
$message="${message}Requester: $requester\n";
$message="${message}Division: $base\n";
$message="${message}Tel No: $tel\n";
$message="${message}Original to go back: $back\n";
$message="${message}\n";
$message="${message}Budget Code: $bcode\n";
$message="${message}Budget Holder: $bholder\n";
$message="${message}\n";
$i=0;
$name="name0";
$num=0;
while ($i < $nbTalk+1)
{
  if (${$name} != "")
    {
      $info=split(";",${$name},2);
      $message="${message}Name of the file: $info[0]\n";

      $nb="nb$i";
      $num+=${$nb};

# write file name in log
      $found = `cat /pub/www/home/scanning/scanning.agenda.log | grep "|$info[0]|"`;
      if ($found == "")
	{
	  `echo "$requester|$email|$info[0]|${$nb}" >> /pub/www/home/scanning/scanning.agenda.log`;
	}

      $message="${message}Number of documents: ${$nb}\n\n";
    }
  $i++;
  $name="name${i}";
}
$message="${message}$num $scanningSendMsg1.\n\n";
if ( $MAILSERVERON )
     mail("${scan_email}","Agenda Scan Request ($info[0])","$message");
     echo "<H3>Please <B>print this page</B> and then attach it to your documents.<BR><BR><BR></H3>";

     echo "<TABLE cellpadding=0>";
     echo "<TR><TD><B>Requester:</B></TD><TD> $requester</TD></TR>";
     echo "<TR><TD><B>Original to go back to:</B></TD><TD> $back</TD></TR>";
     echo "</TABLE><BR><BR>";

     $name="name0";
     $i=0;

     echo "<TABLE border=1 bgcolor=mediumturquoise width=100%>";
     echo "<TR><TD bgcolor=white></TD><TD>Name(s) of Scanned file(s)</TD></TR>";
     $num=0;
     while ($i < $nbTalk+1)
{
  if (${$name} != "")
    {
      $num++;
      echo "<TR><TD rowspan=2><B>Talk $num</B></TD>";
      $info=split(";",${$name},2);
      $nb="nb$i";
      if (${$nb} > 1)
	{
	  echo "<TD rowspan=2><H4>$info[0]_1 to $info[0]_${$nb}</H4></TD>";
	}
      else
	{
	  echo "<TD rowspan=2><H4>$info[0]</H4></TD>";
	}
      echo "<TD>$info[1]</TD></TR>";
      echo "<TR><TD>${$nb} part(s)</TD></TR>";
    }
  $i++;
  $name="name${i}";
}
echo "</TABLE>";

echo "<BR><BR><BR><BR><HR>";

// from config_messages.inc
print $scanningSendMsg2;
?>
<BR>
<FORM>
<CENTER><INPUT type=submit value=CLOSE onClick="window.close();"></FORM></CENTER>

</BODY>
</HTML>