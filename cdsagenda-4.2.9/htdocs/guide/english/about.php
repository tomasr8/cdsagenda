<?
include "../../config/config.php";
include "guideHeader.php";

print "
		<STRONG class=headline>About CDS Agendas $VERSION</STRONG></TD></TR><TR>\n";
?>

<TD colspan=3>
<BR>
<FONT color=red>Please note:</FONT><BR>
<BLOCKQUOTE>
<UL>
<LI><SMALL><B>This tool can be used on all platforms (Sun, Mac and PC), if it is run on Netscape Navigator version 4 or later. The compatibility with MS Internet Exporer has also been tested with success.</B></SMALL>
</UL>
</BLOCKQUOTE>
<FONT color=red>Description:</FONT><BR>
<BLOCKQUOTE><SMALL>
<UL>
<LI>The CDS Agenda tool can help you plan your meetings and conferences, and associate for a long-term storage (in CDS) all possible attachments (minutes, slides,...).<BR>
The tool is both used to display schedules of meetings, and to create and modify new agendas.<BR><BR>

<LI>Differences between v4.1 and v4.2:<BR><BR>
<UL>
<LI>What's new:
<UL>
	<LI>Complete authentication module
	<LI>Groups definition (follow the CERN Organisational structure)
	<LI>Authorization scheme for modification and access to the agendas
	<LI>Authorization scheme for category management
	<LI>Visibility level, access and modification passwords can now be
	  globally defined at the level of the category.
	<LI>Modification of an agenda is done in a new window
	<LI>Possibility to order subpoints of a talk
	<LI>Possibility to order subcategories
</UL>
<LI>What's corrected:
<UL>
	<LI>The lecture titles were not visible in the modification area. This 
	  point has been corrected
	<LI>Corrected bug with file and directory protection mode
	<LI>When a talk is moved from one session to another, its date is changed accordingly. Its attached files are also correctly transfered.
</UL>
</UL>
<br>
<LI>Differences between v4.0 and v4.1:<BR><BR>
<UL>
<LI>It is now possible to protect individual files with passwords.
<LI>A visibility parameter in the agenda data allows you to tell the system at which level of the overview your meeting should appear.
</UL>
<br>
<LI>Differences between v4.0 and v3.0.3:<BR><BR>
<UL>
<LI>The file management has been rethought: files stored in a protected agenda are now also protected.
<LI>Possibility of creating multiple recurrent meeting in one go ("clone" interface)
<LI>Tool for synchronising with personal schedulers (Outlook...) uses the vCalendar standard format.
</UL>
<br>
<LI>Differences between v3.0.3 and v3.0.beta:<BR><BR>

<UL>
<LI>The modification interface is built on the same stylesheets used for the display interface which ensures a true WYSIWYG interface.
<UL>
<LI>It also includes a dummy session which is visible in the modification interface but invisible through the display one. The organiser can then prepare his meeting without users seing it.
<LI>Support to a possible authorization scheme at all level of the tool.
<LI>New way of modifying a schedule ("time table edit").<BR><BR>
</UL>
<LI>More stylesheets have been added (lecture, event)
     <UL>
<LI>The existing ones have been improved...
<LI>...and simplified using templates in stylesheets.<BR><BR>
</UL>
<LI>New possibility of creating single or multi sessions meetings. Single session meeting creation is of course simplified.<BR><BR>
<LI>The top and side menus have been replaced with a homogeneous top toolbar with pop-up menus. (layers)<BR><BR>
<LI>The user can now create 3 types of items (meeting with full schedule, simple event and lecture)<BR><BR>
<LI>The tool now supports Opera browser. (tested on Opera 5.0 linux <U>identified as MSIE 5.0</U>)<BR><BR>
<LI>The user can now focus on one day of the meeting/conference.<BR><BR>
<LI>He can also choose to display or hide the talks (useful in case of very large conferences, to visualize only the main sessions).<BR><BR>

</UL>

<LI>Difference between v2 and v3.0 beta:<BR><BR>
			
<UL>
<LI>The information is extracted from the MySQL database, then converted to XML. The XML output is then passed through a transformation tool (using XSLt) to create the HTML output. The advantage of this technique is that you can very easily create new output formats (XSL sheets) and associate them to your agenda. Among others, an improved version of the "Compact" format has been created (called "compact #2"), as well as a standard display with inline minutes.<BR><BR>
<LI>As a consequencs of this process, the creation of the page may be slightly slower than in version 2. But this should be improved as soon as a faster XSL processor appears (we currently use xtxsl).<BR><BR>
<LI>The creation of the PostScript and PDF from the original agenda have also been improved. You can now create coloured PS or PDF, in A5, A4 or A3 (posters).<BR><BR>
<LI>Navigating through the hierarchical tree is made easier (see "Go To" menu).<BR><BR>
</UL>

</UL>
</BLOCKQUOTE>
<SMALL>
<BR>

<FONT color=green>See also:</FONT></SMALL>
<BLOCKQUOTE><SMALL>
<A HREF="index.php">user guide</A><BR>
</SMALL></BLOCKQUOTE>
</TD>
</TR>
</TABLE>
<BR><BR>
<HR>
</BODY>
</HTML>
