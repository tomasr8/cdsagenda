<?
include "../../config/config.php";
require_once '../../AgeDB.php';
include "guideHeader.php";

$db = &AgeDB::getDB();

function printtable($array)
{
	global $startingyear,$thisyear;

	print "<TABLE CELLPADDING=1 CELLSPACING=1 border=0>\n";
	print "<TR><TH><SMALL>Year</SMALL></TH><TH colspan=2><SMALL></SMALL></TH></TR>\n";
	for ($i=1;$i<=$thisyear-$startingyear+1;$i++)
	{
		$currentyear = $startingyear -1 + $i;
		$nbitems = $array[$i];
		$width = (int)($nbitems/30);
		print "<TR><TD align=right><small><B>$currentyear</B></small></TD><TD align=left><TABLE width=$width CELLPADDING=0 CELLSPACING=0 border=0><TR><TD bgcolor=red><small>&nbsp;</small></TD></TR></TABLE></TD><TD><small>$nbitems </small></TD></TR>\n";
	}
	print "</TR></TABLE>\n";

}

	print "
		<STRONG class=headline>CDS Agendas Stats - Database</STRONG></TD></TR><TR>\n";

	// variables definitions
	$nbAgendas = array();
	$nbSessions = array();
	$nbTalks = array();
	$startingyear = 1999;
	$thisyear = date ("Y",time());

	$res = $db->query("select COUNT(*) AS number from AGENDA");
    $row = $res->fetchRow();
	array_push($nbAgendas,$row['number']);
	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(*) AS number from AGENDA where YEAR(cd)='$i'");
        $row = $res->fetchRow();
		array_push($nbAgendas,$row['number']);
	}

	$res = $db->query( "select COUNT(*) AS number from SESSION");
    $row = $res->fetchRow();
	array_push($nbSessions,$row['number']);
	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(*) AS number from SESSION where YEAR(cd)='$i'");
        $row = $res->fetchRow();
		array_push($nbSessions,$row['number']);
	}

	$res = $db->query( "select COUNT(*) AS number from TALK");
        $row = $res->fetchRow();
	array_push($nbTalks,$row['number']);
	for ($i=$startingyear;$i<=$thisyear;$i++)
	{
		$res = $db->query( "select COUNT(*) AS number from TALK where YEAR(cd)='$i'");
        $row = $res->fetchRow();
		array_push($nbTalks,$row['number']);
	}
	
?>

	  <TD colspan=3>
		<BR>
<?
	print "<UL><LI><small>Total number of agendas created till today: <b>$nbAgendas[0]</b></SMALL><BR><UL>";
	printtable($nbAgendas);
	print "<BR></UL></UL><UL><LI><small>Total number of sessions created till today: <b>$nbSessions[0]</b></SMALL><BR><UL>";
	printtable($nbSessions);
	print "<BR></UL></UL><UL><LI><small>Total number of talks created till today: <b>$nbTalks[0]</b></SMALL><BR><UL>";
	printtable($nbTalks);
	print "</UL></UL>";
?>
		
	  </TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
