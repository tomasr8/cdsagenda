<?
include "../../config/config.php";
include "guideHeader.php";
?>

<STRONG class=headline>
<A HREF="index.php">top</A> 
  > create&nbsp;and&nbsp;modify 
  > <A HREF="modify.php">modify an agenda</A> 
  > <A HREF="agenda_toolbar.php">agenda toolbar</A> 
  > clone agenda</STRONG></TD></TR>

<TR>
<TD colspan=3>
  <SMALL>
  <BR>

This interface allows you to clone an existing agenda according to
certain parameters. There are three different functionalities:

<ul>
  <li><strong>Clone the agenda only once at the specified date</strong>
  <blockquote>
  This allows you to clone the agenda once at the given date. Clicking
  the calendar icon next to the date opens a window with a
  user-friendly calendar to set the date.
  </blockquote>

  <li><strong>Clone the agenda with a fixed interval</strong>
  <blockquote> This allows you to clone the agenda several times with
  a regular interval, like every day/week/month/year, every 2
  days/weeks/months/years, etc. You must select the date of the first
  clone, then either a last date or a number of clones. The last clone
  will be created on the last day satisfying the interval.  Clicking
  the calendar icon next to the date fields opens a window with a
  user-friendly calendar to set the date.</blockquote>

  <li><strong>Clone the agenda on given days</strong> <blockquote>
  This allows you to clone the agenda several times, specifying the
  day clones should be created, like every first Monday of each month,
  or every last Friday every two months. You must select the date of
  the first clone, then either a last date or a number of clones. The
  first clone will be created on the first day satisfying the
  condition after the starting date, the last clone on the last day
  satisfying the condition before the ending date.  Clicking
  the calendar icon next to the date fields opens a window with a
  user-friendly calendar to set the date.
  </blockquote> 

  </ul>

<p><strong>Note:</strong></p>
  <UL>
    <LI>All the agenda's composing sessions and talks will be copied.<BR>

    <LI>The existing alerts will also be copied.

    <LI>The related material (files) attached to the agenda will not be copied.<BR><BR>
  </SMALL>

  
  <SMALL><FONT color=green>See also:</FONT></SMALL>
  <BLOCKQUOTE><SMALL>
  <A HREF="find.php">find an agenda</A><BR>
  <A HREF="consult.php">consult an agenda</A><BR>
  <A HREF="modify.php">modify an agenda</A><BR>
  </SMALL></BLOCKQUOTE>
  </TD>
  </TR>

<!-- Closing tags opened in guide_header.php ->
</TABLE>
<BR>
<BR>
<HR>
</BODY>
</HTML>
