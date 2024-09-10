<?php

// modifymenus.php --- menu creation functions
//
// Copyright (C) 2002  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// modifymenus.php is part of CDS Agenda.
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

function CreateModifyMenus()
{
    global $canAddSubEvent,$stylesheet,$sessionid,$IMAGES_WWW,$canEditAgenda,$canEditTimeTable,$canDeleteAgenda,$canCloneAgenda,$canManageFiles,$agendaAlarmFlag,$ida,$scanServiceActive,$roomBookingSessionActive,$authentication,$userid;

    $modifyMenuText = "";

    /////////////////////////////////////////////////////
    // Agenda Toolbar
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("ToolAgenda",
                                  "Agenda&nbsp;Toolbar",
                                  "../guide/english/agenda_toolbar.php");
    if ( $canAddSubEvent ) {
        if (preg_match("/nosession/",$stylesheet)) {
            $modifyMenuText .= "
		addMenuItem(\"toolagendatext1\",\"add&nbsp;new&nbsp;talk\",\"document.forms[0].ids.value='$sessionid';javascript:openAddTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolAgenda\",document);";
        }
        else {
            $modifyMenuText .= "
		addMenuItem(\"toolagendatext1\",\"add&nbsp;new&nbsp;session\",\"javascript:openAddSession();javascript:closeAll();return false;\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolAgenda\",document);";
        }
    }
    else {
        if (preg_match("/nosession/",$stylesheet)) {
            $modifyMenuText .= "
		addMenuItem(\"toolagendatext1\",\"add&nbsp;new&nbsp;talk\",\"\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolAgenda\",document);";
        }
        else {
            $modifyMenuText .= "
		addMenuItem(\"toolagendatext1\",\"add&nbsp;new&nbsp;session\",\"\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolAgenda\",document);";
        }
    }
    if ( $canEditAgenda ) {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext2\",\"edit&nbsp;agenda&nbsp;data\",\"javascript:openModAgenda();javascript:closeAll();return false;\",\"$IMAGES_WWW/edit.gif\",\"\",\"ToolAgenda\",document);";
    }
    else {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext2\",\"edit&nbsp;agenda&nbsp;data\",\"\",\"$IMAGES_WWW/edit.gif\",\"\",\"ToolAgenda\",document);";
    }
    if ( $canEditTimeTable ) {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext10\",\"edit&nbsp;time&nbsp;table...\",\"javascript:openTimeTableEdit();javascript:closeAll();return false;\",\"$IMAGES_WWW/timetable.gif\",\"\",\"ToolAgenda\",document);";
    }
    else {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext10\",\"edit&nbsp;time&nbsp;table...\",\"\",\"$IMAGES_WWW/timetable.gif\",\"\",\"ToolAgenda\",document);";
    }
    if ( $canDeleteAgenda ) {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext3\",\"delete&nbsp;agenda\",\"javascript:delet();javascript:closeAll();return false;\",\"$IMAGES_WWW/delete.gif\",\"\",\"ToolAgenda\",document);";
    }
    else {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext3\",\"delete&nbsp;agenda\",\"\",\"$IMAGES_WWW/delete.gif\",\"\",\"ToolAgenda\",document);";
    }
    if ( $canCloneAgenda ) {
        $modifyMenuText .= "
	    	addMenuItem(\"toolagendatext4\",\"clone&nbsp;agenda\",\"javascript:cloneAgenda();javascript:closeAll();return false;\",\"$IMAGES_WWW/clone.gif\",\"\",\"ToolAgenda\",document);";
    }
    else {
        $modifyMenuText .= "
	    	addMenuItem(\"toolagendatext4\",\"clone&nbsp;agenda\",\"\",\"$IMAGES_WWW/clone.gif\",\"\",\"ToolAgenda\",document);";
    }
    if ( $canManageFiles ) {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext5\",\"attach&nbsp;files\",\"javascript:popUp('FileAgenda',true,event);return false;\",\"$IMAGES_WWW/files.gif\",\"FileAgenda\",\"ToolAgenda\",document);";
    }
    else {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext5\",\"attach&nbsp;files\",\"\",\"$IMAGES_WWW/files.gif\",\"FileAgenda\",\"ToolAgenda\",document);";
    }
    if ( $agendaAlarmFlag ) {
        $modifyMenuText .= "
		addMenuItem(\"toolagendatext6\",\"set&nbsp;up&nbsp;an&nbsp;alarm\",\"javascript:AlarmSetUp();javascript:closeAll();return false;\",\"$IMAGES_WWW/alert.gif\",\"\",\"ToolAgenda\",document);";
    }
    if (!preg_match("/nosession/",$stylesheet)) {
        $modifyMenuText .= "
        addMenuItem(\"toolagendatext7\",\"compute&nbsp;sessions&nbsp;starting/ending&nbsp;time\",\"javascript:computeSessionTime();javascript:closeAll();return false;\",\"$IMAGES_WWW/time.gif\",\"\",\"ToolAgenda\",document);";
    }
    if (preg_match("/nosession/",$stylesheet)) {
        $modifyMenuText .= "
                addMenuItem(\"toolagendatext8\",\"agenda&nbsp;timing\",\"document.forms[0].ids.value='$sessionid';javascript:popUp('FileSession',false,event);setTimeScale(-1);popUp('Timing',true,event);return false;\",\"$IMAGES_WWW/time.gif\",\"Timing\",\"ToolAgenda\",document);";
    }
    if ($authentication) {
        $modifyMenuText .= "
                addMenuItem(\"toolagendatext9\",\"change&nbsp;authorizations\",\"javascript:changeAuthorization();javascript:closeAll();return false;\",\"$IMAGES_WWW/authorize.gif\",\"\",\"ToolAgenda\",document);\n";
    }
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // File Agenda Toolbar
    /////////////////////////////////////////////////////
	$modifyMenuText .= openMenu("FileAgenda",
                                  "Attach&nbsp;a&nbsp;File&nbsp;to&nbsp;an&nbsp;Agenda",
                                  "");
    $modifyMenuText .= "
		addMenuItem(\"fileagendatext1\",\"list&nbsp;of&nbsp;files...\",\"javascript:openFile();javascript:popUp('FileAgenda',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileAgenda\",document);
		addMenuItem(\"fileagendatext2\",\"file&nbsp;upload...\",\"javascript:openUpload();javascript:popUp('FileAgenda',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileAgenda\",document);
		addMenuItem(\"fileagendatext3\",\"file&nbsp;linkage...\",\"javascript:openLink();javascript:popUp('FileAgenda',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileAgenda\",document);\n";

	$modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Lecture Toolbar
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("MenuLecture",
                                  "Lecture&nbsp;Toolbar",
                                  "");
	$modifyMenuText .= "
	    addMenuItem(\"menulecturetext2\",\"edit&nbsp;lecture&nbsp;data...\",\"javascript:popUp('FileTalk',false,event);openModLecture();javascript:closeAll();return false;\",\"$IMAGES_WWW/edit.gif\",\"\",\"MenuLecture\",document);
		addMenuItem(\"menulecturetext3\",\"delete&nbsp;lecture\",\"javascript:popUp('FileTalk',false,event);delet();javascript:closeAll();return false;\",\"$IMAGES_WWW/delete.gif\",\"\",\"MenuLecture\",document);
		addMenuItem(\"menulecturetext4\",\"attach&nbsp;files\",\"javascript:popUp('FileTalk',true,event);return false;\",\"$IMAGES_WWW/files.gif\",\"FileTalk\",\"MenuLecture\",document);
		addMenuItem(\"menulecturetext5\",\"write&nbsp;minutes\",\"openMinWrit();javascript:closeAll();return false;\",\"$IMAGES_WWW/minutes.gif\",\"\",\"MenuLecture\",document);\n";
    if ($authentication) {
        //$modifyMenuText .= "addMenuItem(\"menulecturetext8\",\"change&nbsp;authorizations\",\"javascript:changeAuthorization();javascript:closeAll();return false;\",\"$IMAGES_WWW/authorize.gif\",\"\",\"MenuLecture\",document);\n";
    }
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Session Toolbar
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("ToolSession",
                                  "Session&nbsp;Toolbar",
                                  "../guide/english/session_toolbar.php");
    $modifyMenuText .= "
		addMenuItem(\"toolsessiontext1\",\"add&nbsp;new&nbsp;talk...\",\"javascript:popUp('FileSession',false,event);openAddTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolSession\",document);
		addMenuItem(\"toolsessiontext2\",\"edit&nbsp;session&nbsp;data...\",\"javascript:popUp('FileSession',false,event);openModSession();javascript:closeAll();return false;\",\"$IMAGES_WWW/edit.gif\",\"\",\"ToolSession\",document);
		addMenuItem(\"toolsessiontext3\",\"delete&nbsp;agenda&nbsp;item\",\"javascript:popUp('FileSession',false,event);deleteSession();javascript:closeAll();return false;\",\"$IMAGES_WWW/delete.gif\",\"\",\"ToolSession\",document);";
    if ( $roomBookingSessionActive )
        $modifyMenuText .= "
		    addMenuItem(\"toolsessiontext4\",\"room&nbsp;booking...\",\"javascript:popUp('FileSession',false,event);openRoomBook();javascript:closeAll();return false;\",\"$IMAGES_WWW/book.gif\",\"\",\"ToolSession\",document);";
    $modifyMenuText .= "
		addMenuItem(\"toolsessiontext5\",\"files\",\"javascript:popUp('FileSession',true,event);return false;\",\"$IMAGES_WWW/files.gif\",\"FileSession\",\"ToolSession\",document);
		addMenuItem(\"toolsessiontext6\",\"write&nbsp;minutes\",\"javascript:popUp('FileSession',false,event);openMinWrit();javascript:closeAll();return false;\",\"$IMAGES_WWW/minutes.gif\",\"\",\"ToolSession\",document);
		addMenuItem(\"toolsessiontext7\",\"session&nbsp;timing\",\"javascript:popUp('FileSession',false,event);setTimeScale(-1);popUp('Timing',true,event);return false;\",\"$IMAGES_WWW/time.gif\",\"Timing\",\"ToolSession\",document);";
    if ($authentication) {
        //$modifyMenuText .= "		addMenuItem(\"toolsessiontext9\",\"change&nbsp;authorizations\",\"javascript:changeAuthorization();javascript:closeAll();return false;\",\"$IMAGES_WWW/authorize.gif\",\"\",\"ToolSession\",document);\n";
    }
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // FileSession
    /////////////////////////////////////////////////////
	$modifyMenuText .= openMenu("FileSession",
                                  "Attach&nbsp;a&nbsp;File&nbsp;to&nbsp;a&nbsp;Session",
                                  "");
    $modifyMenuText .= "
		addMenuItem(\"filesessiontext1\",\"List&nbsp;of&nbsp;Files...\",\"openFile();javascript:popUp('FileSession',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileSession\",document);\n";
    if ($scanServiceActive) {
        $modifyMenuText .= "
		addMenuItem(\"filesessiontext2\",\"Scanning&nbsp;request...\",\"openScanSession();javascript:popUp('FileSession',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileSession\",document);\n";
    }
    $modifyMenuText .= "
		addMenuItem(\"filesessiontext3\",\"File&nbsp;upload...\",\"openUpload();javascript:popUp('FileSession',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileSession\",document);
		addMenuItem(\"filesessiontext4\",\"File&nbsp;Linkage...\",\"openLink();javascript:popUp('FileSession',false,event);javascript:closeAll();return false;\",\"\",\"\",\"FileSession\",document);\n";
	$modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Session Timing
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("Timing",
                                  "session&nbsp;timing",
                                  "../guide/english/session_toolbar.php");
    $modifyMenuText .= "
        </SCRIPT>
		<FORM action=\"\">
		<TR>
		<TD class=header colspan=2>
			<small>Please select the default free time between 2 talks:<BR><SELECT name=time size=1 onChange=\"setTimeSessionScale(this.selectedIndex-1);\">
			<OPTION>0
			<OPTION>5
			<OPTION>10
			<OPTION>15
			<OPTION>20
			<OPTION>25
			<OPTION>30
			<OPTION>35
			<OPTION>40
			<OPTION>45
			<OPTION>50
			<OPTION>55
			<OPTION>60
			</SELECT> mn<BR></small>
		</TD>
	</tr>
    <script>\n";
    $modifyMenuText .= "
		addMenuItem(\"sessiontimingtext1\",\"Compute&nbsp;talks&nbsp;duration\",\"javascript:popUp('FileSession',false,event);computeDuration();return false;\",\"$IMAGES_WWW/time.gif\",\"\",\"Timing\",document);
		addMenuItem(\"sessiontimingtext2\",\"Compute&nbsp;talks&nbsp;starting&nbsp;time\",\"javascript:popUp('FileSession',false,event);computeTime();return false;\",\"$IMAGES_WWW/time.gif\",\"\",\"Timing\",document);
    </script>
	</FORM>
    <SCRIPT>\n";
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Talk Toolbar
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("ToolTalk",
                                  "Talk&nbsp;Toolbar",
                                  "../guide/english/talk_toolbar.php");
    $modifyMenuText .= "
        addMenuItem(\"tooltalktext1\",\"add&nbsp;sub&nbsp;point...\",\"javascript:popUp('FileSession',false,event);openAddSubTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/add.gif\",\"\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext2\",\"edit&nbsp;talk&nbsp;data...\",\"javascript:popUp('FileTalk',false,event);openModTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/edit.gif\",\"\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext3\",\"delete&nbsp;agenda&nbsp;item\",\"javascript:popUp('FileTalk',false,event);deleteTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/delete.gif\",\"\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext4\",\"attach&nbsp;files\",\"javascript:popUp('FileTalk',true,event);return false;\",\"$IMAGES_WWW/files.gif\",\"FileTalk\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext5\",\"write&nbsp;minutes\",\"openMinWrit();javascript:closeAll();return false;\",\"$IMAGES_WWW/minutes.gif\",\"\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext6\",\"advance/delay&nbsp;talk\",\"javascript:popUp('TimeTalk',true,event);return false;\",\"$IMAGES_WWW/time.gif\",\"TimeTalk\",\"ToolTalk\",document);
        addMenuItem(\"tooltalktext7\",\"broadcast&nbsp;URL\",\"javascript:openBroadcastTalk();return false;\",\"$IMAGES_WWW/camera.gif\",\"\",\"ToolTalk\",document);";
    $db = &AgeDB::getDB();
    $sql = "select * from SESSION where ida='$ida'";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numsessions = $res->numRows();
    if ($numsessions > 1) {
        $modifyMenuText .=  "
		addMenuItem(\"tooltalktext9\",\"move&nbsp;talk\",\"javascript:popUp('MoveTalk',true,event);return false;\",\"$IMAGES_WWW/movetalk.gif\",\"MoveTalk\",\"ToolTalk\",document);\n";
    }
    if ($authentication) {
        //$modifyMenuText .= "		addMenuItem(\"tooltalktext8\",\"change&nbsp;authorizations\",\"javascript:changeAuthorization();javascript:closeAll();return false;\",\"$IMAGES_WWW/authorize.gif\",\"\",\"ToolTalk\",document);\n";
    }
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Move Talk
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("MoveTalk",
                                  "Move&nbsp;Talk",
                                  "../guide/english/talk_toolbar.php");
    $db = &AgeDB::getDB();
    $sql = "select stitle,ids from SESSION where ida='$ida' order by speriod1,stime";
    $res = $db->query($sql);
    if (DB::isError($res)) {
        die ($res->getMessage());
    }
    $numRows = $res->numRows();
    $numsessions = 1;
    if ($numRows != 0 ) {
        for ( $i = 0 ; $i < $numRows; $i++ ) {
            $row = $res->fetchRow();
            $modifyMenuText .= "addMenuItem(\"movetalktext$numsessions\",\"to&nbsp;session&nbsp;#$numsessions&nbsp;(<b>'" . preg_replace("/[\n\r\"]+/","",$row['stitle']) . "'</b>)\",\"javascript:MoveTalkTo('" . $row['ids'] . "');return false;\",\"$IMAGES_WWW/movetalk.gif\",\"\",\"MoveTalk\",document);\n";
            $numsessions++;
        }
    }
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Time Talk
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("TimeTalk",
                                        "advance/delay&nbsp;talk",
                                        "");
    $modifyMenuText .= "
		</SCRIPT>
        <FORM action=\"\">
		<TR>
		<TD class=header>
			<small><SELECT name=time size=1 onChange=\"setTimeScale(this.selectedIndex);\">
			<OPTION>5
			<OPTION>10
			<OPTION>15
			<OPTION>20
			<OPTION>25
			<OPTION>30
			<OPTION>35
			<OPTION>40
			<OPTION>45
			<OPTION>50
			<OPTION>55
			<OPTION>60
			</SELECT><BR>time scale (in min.)</small>
		</TD>
		<TD class=header align=right>
			<I><small>advance</small></I><A HREF=\"\" onClick=\"advanceTalk();return false;\"><IMG border=0 SRC=\"../images/upt.gif\" hspace=5 alt=\"advance this talk\" ALIGN=middle></A><BR>
			<I><small>delay</small></I><A HREF=\"\" onClick=\"delayTalk();return false;\"><IMG border=0 SRC=\"../images/downt.gif\" hspace=5 alt=\"delay this talk\" ALIGN=middle></A><BR>
			&nbsp;
		</TD>
		<TD class=header align=left>
			<A HREF=\"\" onClick=\"advanceAllTalk();return false;\"><IMG border=0 SRC=\"../images/upt_red.gif\" hspace=5 alt=\"advance all the talks\" ALIGN=middle></A><I><small>advance&nbsp;all</small></I><BR>
			<A HREF=\"\" onClick=\"delayAllTalk();return false;\"><IMG border=0 SRC=\"../images/downt_red.gif\" hspace=5 alt=\"delay all the talks\" ALIGN=middle></A><I><small>delay&nbsp;all</small></I><BR>
			&nbsp;
		</TD>
	       	</TR>
		</FORM>
        <SCRIPT>\n";
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // File Talk
    /////////////////////////////////////////////////////
	$modifyMenuText .= openMenu("FileTalk",
                                  "Attach&nbsp;a&nbsp;File&nbsp;to&nbsp;a&nbsp;Talk",
                                  "");
	$modifyMenuText .= "
	    addMenuItem(\"filetalktext1\",\"List&nbsp;of&nbsp;Files...\",\"openFile();javascript:closeAll();return false;\",\"\",\"\",\"FileTalk\",document);\n";
    if ($scanServiceActive) {
        $modifyMenuText .= "
		addMenuItem(\"filetalktext2\",\"Scanning&nbsp;request...\",\"openScanTalk();javascript:closeAll();return false;\",\"\",\"\",\"FileTalk\",document);\n";
    }
    $modifyMenuText .= "
		addMenuItem(\"filetalktext3\",\"File&nbsp;upload...\",\"openUpload();javascript:closeAll();return false;\",\"\",\"\",\"FileTalk\",document);
		addMenuItem(\"filetalktext5\",\"File&nbsp;Linkage...\",\"openLink();javascript:closeAll();return false;\",\"\",\"\",\"FileTalk\",document);
		addMenuItem(\"filetalktext6\",\"Give&nbsp;Report&nbsp;Number&nbsp;\",\"javascript:popUp('RepNumber',true,event);return false;\",\"\",\"RepNumber\",\"FileTalk\",document);\n";
    $modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // SubTalk Toolbar
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("ToolSubPoint",
                                  "Sub&nbsp;Talk&nbsp;Toolbar",
                                  "");
    $modifyMenuText .= "
		addMenuItem(\"toolsubpointtext1\",\"edit&nbsp;sub&nbsp;point&nbsp;data...\",\"javascript:popUp('FileTalk',false,event);openModSubTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/edit.gif\",\"\",\"ToolSubPoint\",document);
		addMenuItem(\"toolsubpointtext2\",\"delete&nbsp;agenda&nbsp;item\",\"javascript:popUp('FileTalk',false,event);deleteSubTalk();javascript:closeAll();return false;\",\"$IMAGES_WWW/delete.gif\",\"\",\"ToolSubPoint\",document);
		addMenuItem(\"toolsubpointtext3\",\"files\",\"javascript:popUp('FileTalk',true,event);return false;\",\"$IMAGES_WWW/files.gif\",\"FileTalk\",\"ToolSubPoint\",document);
		addMenuItem(\"toolsubpointtext4\",\"write&nbsp;minutes\",\"openMinWrit();javascript:closeAll();return false;\",\"$IMAGES_WWW/minutes.gif\",\"\",\"ToolSubPoint\",document);
		addMenuItem(\"toolsubpointtext5\",\"move&nbsp;up\",\"MoveSubTalkUp();javascript:closeAll();return false;\",\"$IMAGES_WWW/upicon.gif\",\"\",\"ToolSubPoint\",document);
		addMenuItem(\"toolsubpointtext6\",\"move&nbsp;down\",\"MoveSubTalkDown();javascript:closeAll();return false;\",\"$IMAGES_WWW/downicon.gif\",\"\",\"ToolSubPoint\",document);";
	$modifyMenuText .= closeMenu();

    /////////////////////////////////////////////////////
    // Give Report Number
    /////////////////////////////////////////////////////
    $modifyMenuText .= openMenu("RepNumber",
                                "&nbsp;give&nbsp;report&nbsp;number",
                                "");
    $modifyMenuText .= "
		addMenuItem(\"repnumber1\",\"EDMS&nbsp;Number...\",\"\",\"\",\"\",\"RepNumber\",document);
		addMenuItem(\"repnumber2\",\"CDS&nbsp;Number...\",\"\",\"\",\"\",\"RepNumber\",document);\n";
	$modifyMenuText .= closeMenu();


    /////////////////////////////////////////////////////
    // Special JS for Netscape 4 event handling
    /////////////////////////////////////////////////////
    $modifyMenuText .= "
<SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"Javascript\">
if (NS4) {
        document.layers['ToolAgenda'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['ToolAgenda'].onmousedown=DRAG_begindrag;
        document.layers['ToolAgenda'].onmouseup=DRAG_enddrag;
        document.layers['FileAgenda'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['FileAgenda'].onmousedown=DRAG_begindrag;
        document.layers['FileAgenda'].onmouseup=DRAG_enddrag;
        document.layers['ToolSession'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['ToolSession'].onmousedown=DRAG_begindrag;
        document.layers['ToolSession'].onmouseup=DRAG_enddrag;
        document.layers['Timing'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['Timing'].onmousedown=DRAG_begindrag;
        document.layers['Timing'].onmouseup=DRAG_enddrag;
        document.layers['ToolTalk'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['ToolTalk'].onmousedown=DRAG_begindrag;
        document.layers['ToolTalk'].onmouseup=DRAG_enddrag;
        document.layers['FileTalk'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['FileTalk'].onmousedown=DRAG_begindrag;
        document.layers['FileTalk'].onmouseup=DRAG_enddrag;
        document.layers['FileSession'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['FileSession'].onmousedown=DRAG_begindrag;
        document.layers['FileSession'].onmouseup=DRAG_enddrag;
        document.layers['RepNumber'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['RepNumber'].onmousedown=DRAG_begindrag;
        document.layers['RepNumber'].onmouseup=DRAG_enddrag;
        document.layers['TimeTalk'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['TimeTalk'].onmousedown=DRAG_begindrag;
        document.layers['TimeTalk'].onmouseup=DRAG_enddrag;
        document.layers['ToolSubPoint'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['ToolSubPoint'].onmousedown=DRAG_begindrag;
        document.layers['ToolSubPoint'].onmouseup=DRAG_enddrag;
        document.layers['MenuLecture'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['MenuLecture'].onmousedown=DRAG_begindrag;
        document.layers['MenuLecture'].onmouseup=DRAG_enddrag;
        document.layers['MoveTalk'].captureEvents(Event.MOUSEUP|Event.MOUSEDOWN);
        document.layers['MoveTalk'].onmousedown=DRAG_begindrag;
        document.layers['MoveTalk'].onmouseup=DRAG_enddrag;
}
</SCRIPT>";

    return $modifyMenuText;
}

function openMenu($menuName,$menuText,$helpurl)
{
    global $IMAGES_WWW;

    $createMenuRetStr = "
<DIV ID=\"$menuName\"
	 CLASS=\"menudiv\"
	 onClick=\"event.cancelBubble = true;\">
<TABLE BORDER=1 CELLPADDING=1 CELLSPACING=0>
<TR>
	<TD class=\"menuheader\">
		<TABLE width=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
		<TD class=\"menuheader\" align=left ID=\"" . $menuName . "header\"
			onMouseDown=\"DRAG_begindrag(event,'" . $menuName . "')\"
            onMouseUp=\"DRAG_enddrag();\">
			<B>
			&nbsp;$menuText&nbsp;
			</B>
	    </TD>";

    if ( $helpurl != "" ) {
        $createMenuRetStr .= "
		<TD class=headerselected>
			<A HREF=\"$helpurl\" target=\"top\"><IMG SRC=\"$IMAGES_WWW/smallhelp.gif\" alt=\"help\" align=right valign=middle border=0></A>
		</TD>";
    }

    $createMenuRetStr .= "
		<TD class=headerselected align=right>
			<A HREF=\"\" onClick=\"closeSubItems('$menuName');javascript:popUp('$menuName',false,event);return false;\"><IMG SRC=\"$IMAGES_WWW/sclose.gif\" hspace=2 vspace=2 ALT=\"Close this window\" BORDER=0 align=right></A>
		</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<TR class=header>
	<TD>
	<TABLE width=\"100%\" border=0 cellpadding=0 cellspacing=0>
     <SCRIPT TYPE=\"text/javascript\" LANGUAGE=\"JavaScript1.2\">";

    return $createMenuRetStr;
}

function closeMenu()
{
  $closeMenuRetStr = "
	</SCRIPT>
	</TABLE>
	</TD>
</TR>
</TABLE>
</DIV>";
  return $closeMenuRetStr;
}

?>
