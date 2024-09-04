<?
include "../config/config.php";
require_once "$AGE/AgeDB.php";
require_once "$AGE/platform/system/archive.inc";
include "$AGE/overview/hierarchy.inc";

$db = &AgeDB::getDB();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 

<HTML>
<HEAD>
<?
        print "
<TITLE>CDS Agendas $VERSION - Stats</TITLE>
   <link rel=\"stylesheet\" href=\"${IMAGES_WWW}/cds_user_guide.css\">\n";
?>
</HEAD>
<BODY   BGCOLOR="#FFFFFF" 
        LINK="#009900" 
        VLINK="#006600" 
        ALINK="#FF0000"
        class="list">

<CENTER>
<TABLE border=0 cellpadding=0 cellspacing=0 width="75%">
  <TR>
<?
        print "
    <TD valign=top><A HREF=\"${AGE_WWW}\"><img border=0 alt=\"CDS Agendas\" src=\"${AGE_WWW}/images/dec.agendas.gif\"></A></TD>\n";
?>
    <TD>  </TD>
    <TD align=left>
        &nbsp;&nbsp;<strong class=headline><FONT size="+2">STATISTICS</FONT></strong>
<?
        print "
        <BR>&nbsp;&nbsp;<SMALL>(CDS Agenda version $VERSION)</SMALL>\n";
?>
        <BR>
    </TD>
  </TR>
<TR>
        <TD>&nbsp;</TD>
</TR>
<TR>
        <TD class=results colspan=3>
<?
print "
                <IMG SRC=\"${AGE_WWW}/images/okay.gif\" ALT=\"\" align=top>\n";

// If no fid specified, we start the display in top category
if ($fid == "") { 
    	$fid = 0; 
	$categ = "All categories";
}
else {
	$res = $db->query( "SELECT title from LEVEL where uid='$fid'" );
	$row = $res->fetchRow();
	$categ = $row['title'];
}

// create the set of levels in which the agendas may be searched
$levelset = array();
$levelset = getChildren($fid);
array_push($levelset,$fid);
$levelsettext = createTextFromArray($levelset);

	print "
		<STRONG class=headline>CDS Agendas Stats - $categ</STRONG></TD></TR><TR>\n";

	// variables definitions
	$nbAgendas = array();
	$nbSessions = array();
	$nbTalks = array();
	$thisyear = date ("Y",time());

	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(id) AS number from AGENDA where YEAR(cd)='$i' and fid IN $levelsettext");
        	$row = $res->fetchRow();
		array_push($nbAgendas,$row['number']);
	}

	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(ids) AS number from SESSION,AGENDA where YEAR(SESSION.cd)='$i' and fid IN $levelsettext and id=ida");
		print mysql_error();
       		$row = $res->fetchRow();
		array_push($nbSessions,$row['number']);
	}

	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(idt) AS number from TALK,AGENDA where YEAR(TALK.cd)='$i' and fid IN $levelsettext and id=ida");
        	$row = $res->fetchRow();
		array_push($nbTalks,$row['number']);
	}
	
	$numfiles = 0;
	$sizefiles = 0;
	$sql = "SELECT DISTINCT id
		FROM AGENDA
		WHERE fid IN $levelsettext";
	$res = $db->query($sql);
	while ($row = $res->fetchRow()) {
		$ida = $row['id'];
		$sql2 = "SELECT COUNT(FILE.id) as numfiles,
		       		SUM(FILE.size) as sizefiles
			 FROM FILE,
			      FILE_EVENT
			 WHERE 	FILE_EVENT.fileID=FILE.id and
			      	(eventID='$ida' or eventID LIKE '".$ida."s%')";
		$res2 = $db->query($sql2);
		if ($row2 = $res2->fetchRow()) {
			$numfiles += $row2['numfiles'];
			$sizefiles += $row2['sizefiles'];
		}
	}
	
?>

	  <TD colspan=3>
		<BR>
<?
	print "<UL><LI><small>Total number of agendas created till today: <b>" . array_sum($nbAgendas) . "</b></SMALL><BR><UL>";
	printtable($nbAgendas);
	print "<BR></UL></UL><UL><LI><small>Total number of sessions created till today: <b>" . array_sum($nbSessions) . "</b></SMALL><BR><UL>";
	printtable($nbSessions);
	print "<BR></UL></UL><UL><LI><small>Total number of talks created till today: <b>" . array_sum($nbTalks) . "</b></SMALL><BR><UL>";
	printtable($nbTalks);
	print "<BR></UL></UL><UL><LI><small>Number of resources (files): <b>$numfiles</b> Total size: <b>$sizefiles</b>o.</SMALL><BR><UL>";
	print "</UL></UL>";
?>
		
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>


<?
function printtable($array)
{
	global $startingyear,$thisyear;
	$maxtablewidth = 450;
	$values = $array;
	rsort($values);
	$maxvalue = $values[0];
	$widthfactor = $maxvalue/$maxtablewidth;

	print "<TABLE CELLPADDING=1 CELLSPACING=1 border=0>\n";
	print "<TR><TH><SMALL>Year</SMALL></TH><TH colspan=2><SMALL></SMALL></TH></TR>\n";
	for ($i=0;$i<=$thisyear-$startingyear;$i++)
	{
		$currentyear = $startingyear + $i;
		$nbitems = $array[$i];
		$width = (int)($nbitems/$widthfactor);
		print "<TR><TD align=right><small><B>$currentyear</B></small></TD><TD align=left><TABLE width=$width CELLPADDING=0 CELLSPACING=0 border=0><TR><TD bgcolor=red><small>&nbsp;</small></TD></TR></TABLE></TD><TD><small>$nbitems </small></TD></TR>\n";
	}
	print "</TR></TABLE>\n";

}
?>