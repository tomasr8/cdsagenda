<?
// $Id: sessinit.inc,v 1.1.2.2 2004/07/29 07:09:06 tbaron Exp $

// sessinit.inc --- authentication/authorization management
//
// Author(s): Tibor Simko <tibor.simko@cern.ch>+Thomas Baron <thomas.baron@cern.ch>
//
// sessinit.inc is part of CDS Agenda.
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

##
## description: initializes cdsagenda session management
##
## note: based on the "PHP4 MySQL Session Handler" code from Ying
##       Zhang <ying@zippydesign.com>.  His code was modified to
##       suit our needs.
##
## note: for good session management operation, you need to set up in
##       the 'php.ini' file the variables `session.gc_maxlifetime'
##       (e.g. 86400 to mean 1 day) and `session.cookie_lifetime' to
##       zero (session holds until user closes his browser).  In
##       adddition, the garbage collector should be called explicitely
##       via `admin/gc.shtml' if you choose `session.gc_probability'
##       to be zero in the `php.ini' file.

require_once("ldapfunctions.inc");
require_once("imapfunctions.inc");
require_once("$AGE/AgeDB.php");

$db = &AgeDB::getDB();

////////////////////////////////////////////////////
//                                                //
// PART 1: AUTHENTICATION METHODS                 //
//                                                //
////////////////////////////////////////////////////

$SESS_LIFE = get_cfg_var("session.gc_maxlifetime");

## sess_open():
function sess_open($save_path, $session_name) {
	global $db;
	$db = &AgeDB::getDB();
	return true;
}

## sess_close():
function sess_close() {
	return true;
}

## sess_read():
function sess_read($key) {
	global $db, $SESS_LIFE;

	$qry = "SELECT session_vars FROM auth_session WHERE session_key = '$key'";
	$qid = $db->query($qry);
	$value = '';
	if ($row = $qid->fetchRow()) {
		$value = $row["session_vars"];
	}

	return $value;
}

## sess_write():
function sess_write($key, $val) {
	global $db, $SESS_LIFE;

	$expiry = time() + $SESS_LIFE;
	$value = addslashes($val);

	$qry = "INSERT INTO auth_session VALUES ('$key', '$value', $expiry)";
	$qid = $db->query($qry);

    // TODO: This function must return true if the session was written
    if($qid === 1) {
        return true;
    }

	if (get_class($qid) == "db_error") {
		$qry = "UPDATE auth_session SET session_expiry = $expiry, session_vars = '$value' WHERE session_key = '$key'";
		$qid = $db->query($qry);
	}

	return $qid;
}

## sess_destroy():
function sess_destroy($key) {
	global $db;

	$qry = "DELETE FROM auth_session WHERE session_key = '$key'";
	$res = $db->query($qry);

	return $res;
}

###########################################################################
# Garbage Collector
#
# 1) Delete expired sessions
# 2)Delete guest users without session
#

function sess_gc($maxlifetime) {
	global $db;
	global $userid;

	# Destroy expired sessions
	$query = "DELETE FROM auth_session WHERE session_expiry < " . time();
	$result = $db->query($query);

	# Delete guest users without session.
	# The LEFT JOIN method cannot be applied here because there is no simple
	# way to compare the user id in table user with the one encoded in
	# session_vars in table session.
	#
	# backup of current userid
	$old_userid = $userid;

	$query = "SELECT session_vars FROM auth_session";
	$result = $db->query($query);
	$nRows = $result->numRows();
	if($nRows) {
	    $cur_svar=$result->fetchRow();
	    $useridStr = "$userid";
	    while($cur_svar = $result->fetchRow()) {
		session_decode($cur_svar[0]);
		$useridStr .= ",$userid";
	    }
	}

	# restoration of the previous userid
	$userid = $old_userid;

 	if($useridStr != "") {
 	    $query = "DELETE FROM user WHERE id NOT IN (".$useridStr.") AND email = ''";
 	    $result = $db->query($query);
 	    $nRows = $db->affectedRows();
 	}
 	else
 	    $nRows = 0;

	return $nb_sess_deleted;
}

session_set_save_handler(
	"sess_open",
	"sess_close",
	"sess_read",
	"sess_write",
	"sess_destroy",
	"sess_gc");

// getEmail():
function getEmail($userid) {
    global $db, $useLDAP;

    $userid_email = "guest";
    $query = "SELECT email FROM user WHERE id='$userid'";
    $res = $db->query($query);
    if ($row = $res->fetchRow()) {
	   if ($row[email]) {
          // If the user name is already an email address
          if (ereg("@",$row[email])) {
             $userid_email = $row[email];
          }
          // Try to guess the email address from the LDAP directory
          elseif ($useLDAP) {
            $userid_email = getLDAPemail($row[email]);
          }
       }
    }
    return($userid_email);
}

## getPW():
function getPW($userid) {
    global $db;
    $userid_pw = "";
    $query = "SELECT password FROM user WHERE id='$userid'";
    $res = $db->query($query);
    if ($row = $res->fetchRow()) {
        if ($row[password]) {
            $userid_pw = $row[password];
        }
    }
    return($userid_pw);
}

## getID():
function getID($username) {
    global $db;
    $userid = "";
    $query = "SELECT id
              FROM user
              WHERE email='$username'";
    $res = $db->query($query);
    if ($row = $res->fetchRow()) {
        if ($row[id]) {
            $userid = $row[id];
        }
    }
    return($userid);
}

## createGuestUser():
function createGuestUser() {
    global $db;
    $res = $db->query("SELECT MAX(id) as maxid from user");
    if ($row = $res->fetchRow()) {
    	$newid = $row["maxid"] + 1;
    }
    else {
	$newid = 1;
    }
    $res = $db->query("INSERT INTO user (id) VALUES ($newid)");
    if ($db->affectedRows()) { # to check whether the above insert failed or not
        $userid = $newid;
        $_SESSION['userid'] = $userid;
    } else {
        $userid = 0;
    }
    return $userid;
}

function login(&$userid,&$userid_email,$username,$password)
{
    global $db, $useIMAP;
    $error = "";
    if (!$username) {
        $error = "<p>No user name specified.";
    } else {
        $query = "SELECT id,password FROM user WHERE email='$username'";
        $res = $db->query($query);
        if ($row = $res->fetchRow()) {
            $duserid_id=$row[id];
            $duserid_pw=$row[password];
            if ($duserid_pw) { // pw needed
                if ($duserid_pw == "IMAP") {
                    if ($useIMAP) {
                       $error = IMAPLogin($username,$password);
                       if ($result == "") {
                           $userid=$duserid_id;
                       }
                    }
                    else {
                       $error = "Cannot login... Please contact the site administrator.";
                    }
                }
                elseif ($duserid_pw != $password) {
                    $error = "<p>Sorry, password mismatch.  Please try again.";
                } else {
                    $userid=$duserid_id;
                    $userid_email=getEmail($userid);
                    $_SESSION['userid'] = $userid;
                }
            } else { // pw not needed
                $userid=$duserid_id;
                $userid_email=getEmail($userid);
                $_SESSION['userid'] = $userid;
                $status = 0;
            }
        }
        else {
            // if the user does not exist, we try to register him
            $error = register($userid,$userid_email,$username,$password);
        }
    }
    return $error;
}

function logout(&$userid,&$userid_email)
{
    $userid = createGuestUser();
    $userid_email=getEmail($userid);
}

function register(&$userid,&$userid_email,$username,$password)
{
    global $db, $useIMAP;

    $error = "";
    if ($username) {
       // first test whether this address is already used
       $query = "SELECT id
                 FROM user
                 WHERE email='$username'";
       $res = $db->query($query);
       if ($res->numRows()) {
          $error = "Sorry, user <strong class=headline>$username</strong> already exists.  Please use the <span class=headline>login</span> button instead.";
       }
       elseif(checkEmail($username)==false) {
           if ($useIMAP) {
               // try to register an IMAP user
               $error = IMAPLogin($username,$password);
               if ($error == "") {
                   $query = "UPDATE user
                             SET email='$username',password='IMAP'
                             WHERE id='$userid'";
                   $db->query($query);
                   $userid_email=getEmail($userid);
               }
           }
           else {
               $error = "Sorry, email is no correct";
           }
       }
       else {
          // register a normal CDS Agenda user
          $query = "UPDATE user
                    SET email='$username',password='$password'
                    WHERE id='$userid'";
          $db->query($query);
          $userid_email=getEmail($userid);
       }
    }
    else {
       $error = "No user name specified.  Please try again.";
    }

    return $error;
}

function checkEmail($email)
{
    $pos = strpos($email,"@");
    $em = preg_grep("/^.+@.+\\..+$/",array($email));

    if($em[0] != "") {  // regular expression was good
        if(strpos($email,"@") != strrpos($email,"@")) {
            return false;
        }
        return true;
    }
    else {
        return false;
    }
}

////////////////////////////////////////////////////
//                                                //
// PART 2: AUTHORIZATION METHODS                  //
//                                                //
////////////////////////////////////////////////////

/////////////////////////////////////////
//       Main Engine                   //
/////////////////////////////////////////

// this function check if user $userid is a superuser
function isSuperuser($userid)
{
    global $superuser_email;

    $userid_email = getEmail($userid);
    if ($superuser_email != "" && eregi($superuser_email,$userid_email)) {
        return true;
    }
    return false;
}

// this function returns the authorization rule id
function getAuthID($authName)
{
    global $db;
    $sql = "SELECT id
            FROM authorization
            WHERE name='$authName'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return $row[id];
    }
    else {
        return false;
    }
}

// this function returns the item type on which the authorization rule
// is defined (may be category, agenda, session, talk, file)
function getAuthItemType($authName)
{
    global $db;
    $sql = "SELECT itemType
            FROM authorization
            WHERE name='$authName'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return $row[itemType];
    }
    else {
        return false;
    }
}

// this function checks if user $userid has authorization $rule over
// item $item
function authorize($userid,$rule,$item)
{
    global $db;
    // Check with superusers
    if (isSuperuser($userid)) {
        return true;
    }

    $type = getAuthItemType($rule);

    // Check with individual permissions
    $sql = "SELECT *
            FROM user_authorization_item
            LEFT JOIN authorization on id=authID
            WHERE name='$rule' and
                  userID='$userid' and
                  itemID='$item'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return true;
    }
    else {
        // Check with group permissions
        $groupids = getMyGroups($userid);
        if (count($groupids) != 0) {
            $grouplist = "(".createListFromArray($groupids).")";
            $sql = "SELECT *
                    FROM group_authorization_item
                    LEFT JOIN authorization on authorization.id=authID
                    LEFT JOIN groups on groups.id=group_authorization_item.groupID
                    WHERE authorization.name='$rule' and
                          itemID='$item' and
                          groups.id IN $grouplist";
            $res = $db->query($sql);
            if ($row = $res->fetchRow()) {
                return true;
            }
        }
        // else try recurse if the request concerns a category
        if ($type == "category") {
            $sql = "SELECT fid
                    FROM LEVEL
                    WHERE uid='$item'";
            $res = $db->query($sql);
            while ($row = $res->fetchRow()) {
                if (authorize($userid,$rule,$row[fid])) {
                    return true;
                }
            }
        }

        // No possible authorization was found - request is rejected
        return false;
    }
}

// this function grants authorization $rule to user $userid over item $item
function setUserAuthorization($userid,$rule,$item,$recursive=1)
{
    global $db;
    if (!$userid) {
        return false;
    }
    if (isSuperuser($userid)) {
        return true;
    }
    // First get authorization ID
    if ($ruleID = getAuthID($rule)) {
        // Then check whether the authorization already exists
        $sql = "SELECT *
            FROM user_authorization_item
            WHERE authID='$ruleID' and
                  userID='$userid' and
                  itemID='$item'";
        $res = $db->query($sql);
        if ($row = $res->fetchRow()) {
            // If it exists, just update the recursivity
            $sql = "UPDATE user_authorization_item,
                    SET recursive = '$recursive'
                    WHERE authID='$ruleID' and
                          userID='$userid' and
                          itemID='$item'";
            $res = $db->query($sql);
            return true;
        }
        else {
            $sql = "INSERT INTO user_authorization_item (userID, authID, itemID, recursive)
                    VALUES('$userid','$ruleID','$item','$recursive')";
            $res = $db->query($sql);
            return true;
        }
    }
    else {
        return false;
    }
}

// this function grants authorization $rule to group $groupid over item $item
function setGroupAuthorization($groupid,$rule,$item,$recursive=1)
{
    global $db;
    if (!$groupid) {
        return false;
    }
    // First get authorization ID
    if ($ruleID = getAuthID($rule)) {
        // Then check whether the authorization already exists
        $sql = "SELECT *
            FROM group_authorization_item
            WHERE authID='$ruleID' and
                  groupID='$groupid' and
                  itemID='$item'";
        $res = $db->query($sql);
        if ($row = $res->fetchRow()) {
            // If it exists, just update the recursivity
            $sql = "UPDATE group_authorization_item,
                    SET recursive = '$recursive'
                    WHERE authID='$ruleID' and
                          groupID='$groupid' and
                          itemID='$item'";
            $res = $db->query($sql);
            return true;
        }
        else {
            $sql = "INSERT INTO group_authorization_item (groupID, authID, itemID, recursive)
                    VALUES('$groupid','$ruleID','$item','$recursive')";
            $res = $db->query($sql);
            return true;
        }
    }
    else {
        return false;
    }
}

// this function unsets authorization $rule to user $userid over item $item
function unsetUserAuthorization($userid,$rule,$item)
{
    global $db;
    // First get authorization ID
    if ($ruleID = getAuthID($rule)) {
        $sql = "DELETE
                FROM user_authorization_item
                WHERE authID='$ruleID'
                     ".($item == "*"?"":" and itemID='$item'")."
                     ".($userid == "*"?"":" and userID='$userid'");

        $res = $db->query($sql);
        return true;
    }
    else {
        return false;
    }
}

// this function unsets authorization $rule to group $groupid over item $item
function unsetGroupAuthorization($groupid = "*",$rule = "*",$item = "*")
{
    global $db;

    // First get authorization ID
    if ($rule == "*") {
        $sql = "DELETE
                FROM group_authorization_item
                WHERE ".($item == "*"?"":" itemID='$item'")."
                      ".($groupid == "*"?"":($item == "*"?"":" and")." groupID='$groupid'");
        $res = $db->query($sql);
    }
    elseif ($ruleID = getAuthID($rule)) {
        $sql = "DELETE
                FROM group_authorization_item
                WHERE authID='$ruleID'
                     ".($item == "*"?"":" and itemID='$item'")."
                     ".($groupid == "*"?"":" and groupID='$groupid'");

        $res = $db->query($sql);
        return true;
    }
    else {
        return false;
    }
}

// this function returns the list of users which have the $rule
// authorization over item $item
function listAuthorizedUsers($rule,$item)
{
    global $db;
    $userids = array();
    // First get authorization ID
    if ($ruleID = getAuthID($rule)) {
        $sql = "SELECT userID
                FROM user_authorization_item
                WHERE authID='$ruleID' and
                      itemID='$item'";
        $res = $db->query($sql);
        while ($row = $res->fetchRow()) {
            array_push($userids,$row[userID]);
        }
    }
    return $userids;
}

// this function returns the list of groups which have the $rule
// authorization over item $item
function listAuthorizedGroups($rule,$item)
{
    global $db;
    $groupids = array();
    // First get authorization ID
    if ($ruleID = getAuthID($rule)) {
        $sql = "SELECT groupID
                FROM group_authorization_item
                WHERE authID='$ruleID' and
                      itemID='$item'";
        $res = $db->query($sql);
        while ($row = $res->fetchRow()) {
            array_push($groupids,$row[groupID]);
        }
    }
    return $groupids;
}

/////////////////////////////////////////
//       category management           //
/////////////////////////////////////////

// this function check if user $userid can manage the $cid category
function canManageCategory($userid,$cid)
{
    return authorize($userid,"CAN MANAGE CATEGORY",$cid);
}

// this function unsets user $userid's authorization to manage category $cid
function unsetUserManageCategoryAuthorization($userid,$cid)
{
    return unsetUserAuthorization($userid,"CAN MANAGE CATEGORY",$cid);
}

// this function unsets group $groupid's authorization to manage category $cid
function unsetGroupManageCategoryAuthorization($groupid,$cid)
{
    return unsetGroupAuthorization($groupid,"CAN MANAGE CATEGORY",$cid);
}

// this function grants user $userid the right to manage category $cid
function setUserManageCategoryAuthorization($userid,$cid)
{
    return setUserAuthorization($userid,"CAN MANAGE CATEGORY",$cid,1);
}

// this function grants group $groupid the right to manage category $cid
function setGroupManageCategoryAuthorization($groupid,$cid)
{
    return setGroupAuthorization($groupid,"CAN MANAGE CATEGORY",$cid,1);
}

// this function returns the list of users who have the right to
// manage category $cid
function listUsersManageCategoryAuthorized($cid)
{
    return listAuthorizedUsers("CAN MANAGE CATEGORY",$cid);
}

// this function returns the list of groups who have the right to
// manage category $cid
function listGroupsManageCategoryAuthorized($cid)
{
    return listAuthorizedGroups("CAN MANAGE CATEGORY",$cid);
}

// this function returns the list of all the categories user $userid can manage
function listCategoriesUserCanManage($userid)
{
    global $db;
    $result = array();
    $ruleID = getAuthID("CAN MANAGE CATEGORY");
    $sql = "SELECT DISTINCT uid,title
            FROM LEVEL
            LEFT JOIN user_authorization_item on itemID=uid
            WHERE userID='$userid' and
                  authID='$ruleID'
            ORDER BY title";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($result,$row);
    }
    return $result;
}

// this function returns the list of all the categories group $groupid
// can manage
function listCategoriesGroupCanManage($groupid)
{
    global $db;
    $result = array();
    $ruleID = getAuthID("CAN MANAGE CATEGORY");
    $sql = "SELECT DISTINCT uid,title
            FROM LEVEL
            LEFT JOIN group_authorization_item on itemID=uid
            WHERE groupID='$groupid' and
                  authID='$ruleID'
            ORDER BY title";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($result,$row);
    }
    return $result;
}

/////////////////////////////////////////
//       agenda management             //
/////////////////////////////////////////

// this function checks if user $userid can modify agenda $ida
function canModifyAgenda($userid,$ida)
{
    global $db;
    $sql = "SELECT fid
            FROM AGENDA
            WHERE id='$ida'";
    $res = $db->query($sql);
    $row = $res->fetchRow();
    $fid = $row[fid];

    // try with a simple authorization
    if (authorize($userid,"CAN MODIFY AGENDA",$ida)){
        return true;
    }
    // else try with a global category authorization
    elseif (authorize($userid,"CAN MODIFY AGENDA IN CATEGORY",$fid)) {
        return true;
    }
    // else try with the category management authorization
    else {
        $sql = "SELECT fid
                FROM AGENDA
                WHERE id='$ida'";
        $res = $db->query($sql);
        $row = $res->fetchRow();
        $fid = $row[fid];
        return canManageCategory($userid,$fid);
    }
}

// this function grants user $userid the right to modify agenda $ida
function setUserModifyAgendaAuthorization($userid,$ida)
{
    return setUserAuthorization($userid,"CAN MODIFY AGENDA",$ida,1);
}

// this function grants user $userid the right to modify all agendas
// in category $cid
function setUserModifyAgendaInCategoryAuthorization($userid,$cid)
{
    return setUserAuthorization($userid,"CAN MODIFY AGENDA IN CATEGORY",$cid,1);
}

// this function grants group $groupid the right to modify agenda $ida
function setGroupModifyAgendaAuthorization($groupid,$ida)
{
    return setGroupAuthorization($groupid,"CAN MODIFY AGENDA",$ida,1);
}

// this function grants group $groupid the right to modify all agendas
// in category $cid
function setGroupModifyAgendaInCategoryAuthorization($groupid,$cid)
{
    return setGroupAuthorization($groupid,"CAN MODIFY AGENDA IN CATEGORY",$cid,1);
}

// this function unsets the right to modify agenda $ida for user $userid
function unsetUserModifyAgendaAuthorization($userid,$ida)
{
    return unsetUserAuthorization($userid,"CAN MODIFY AGENDA",$ida);
}

// this function unsets the right to modify all agendas in category
// $cid for user $userid
function unsetUserModifyAgendaInCategoryAuthorization($userid,$cid)
{
    return unsetUserAuthorization($userid,"CAN MODIFY AGENDA IN CATEGORY",$cid);
}

// this function unsets the right to modify agenda $ida for group $groupid
function unsetGroupModifyAgendaAuthorization($groupid,$ida)
{
    return unsetGroupAuthorization($groupid,"CAN MODIFY AGENDA",$ida);
}

// this function unsets the right to modify all agendas in category
// $cid for group $groupid
function unsetGroupModifyAgendaInCategoryAuthorization($groupid,$cid)
{
    return unsetGroupAuthorization($groupid,"CAN MODIFY AGENDA IN CATEGORY",$cid);
}

// this function returns the list of users who are authorized to
// modify agenda $ida
function listUsersModifyAgendaAuthorized($ida)
{
    return listAuthorizedUsers("CAN MODIFY AGENDA",$ida);
}

// this function returns the list of users who are authorized to
// modify all agendas in category $cid
function listUsersModifyAgendaInCategoryAuthorized($cid)
{
    return listAuthorizedUsers("CAN MODIFY AGENDA IN CATEGORY",$cid);
}

// this function returns the list of groups who are authorized to
// modify agenda $ida
function listGroupsModifyAgendaAuthorized($ida)
{
    return listAuthorizedGroups("CAN MODIFY AGENDA",$ida);
}

// this function returns the list of groups who are authorized to
// modify all agendas in category $cid
function listGroupsModifyAgendaInCategoryAuthorized($cid)
{
    return listAuthorizedGroups("CAN MODIFY AGENDA IN CATEGORY",$cid);
}

// this function unsets the right to modify agenda $ida for all users
function deleteAllAgendaAuthorizations($ida)
{
    unsetUserModifyAgendaAuthorization("*",$ida);
}

// this function returns the list of all the agendas user $userid can modify
function listAgendasUserCanModify($userid)
{
    global $db;
    $result = array();
    $ruleID = getAuthID("CAN MODIFY AGENDA");
    $sql = "SELECT DISTINCT id,fid,stdate,title
            FROM AGENDA
            LEFT JOIN user_authorization_item on itemID=id
            WHERE userID='$userid' and
                  authID='$ruleID'
            ORDER BY stdate";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($result,$row);
    }
    return $result;
}

// this function returns the list of all the agendas group $groupid can modify
function listAgendasGroupCanModify($groupid)
{
    global $db;
    $result = array();
    $ruleID = getAuthID("CAN MODIFY AGENDA");
    $sql = "SELECT DISTINCT id,fid,stdate,title
            FROM AGENDA
            LEFT JOIN group_authorization_item on itemID=id
            WHERE groupID='$groupid' and
                  authID='$ruleID'
            ORDER BY stdate";
    $res = $db->query($sql);

    while ($row = $res->fetchRow()) {
        array_push($result,$row);
    }
    return $result;
}


/////////////////////////////////////////
//    group management functions       //
/////////////////////////////////////////

// this function returns the list of all the groups user $userid
// belongs to
function getMyGroups($userid)
{
    global $useLDAP,$db;

    $result = array();

    // First get the "normal" (non-LDAP) groups
    $sql = "SELECT groupID
            FROM user_groups
            WHERE userID = '$userid'";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        array_push($result,$row[groupID]);
    }

    // Then add the LDAP groups
    if ($useLDAP) {
        getMyLDAPGroups($userid,$result);
    }

    return $result;
}


// this function returns the list of all the users belonging to group $groupid
function listGroupMembers($groupid)
{
    global $useLDAP,$db;

    $result = array();

    // check the group type
    $sql = "SELECT special,name
            FROM groups
            WHERE id='$groupid'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        // LDAP group
        if ($row[special] == "LDAP" && $useLDAP) {
            getLDAPGroupMembers($row[name],$result);
        }
        // "normal" group
        else {
            $sql = "SELECT email
                    FROM user_groups
                    LEFT JOIN user on user.id=userID
                    WHERE groupID='$groupid'";
            $res = $db->query($sql);
            while ($row = $res->fetchRow()) {
                array_push($result,$row[email]);
            }
        }
    }

    return $result;
}

// this function returns the name of group $groupid
function getGroupName($groupID)
{
    global $db;
    $sql = "SELECT name
            FROM groups
            where id='$groupID'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return $row[name];
    }
    else {
        return "";
    }
}

// this function returns the type of group $groupid
function getGroupType($groupID)
{
    global $db;
    $sql = "SELECT special
            FROM groups
            where id='$groupID'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        if ($row[special] == "LDAP") {
            return "LDAP";
        }
        else {
            return "local";
        }
    }
    else {
        return "local";
    }
}

// this function returns the list of all existing groups
function listAllGroups()
{
    global $db;
    $result = array();
    $sql = "SELECT id,name
            FROM groups";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        $result[$row[id]] = $row[name];
    }
    return $result;
}

// this function creates a new group with $name name
function addGroup($name,$description,$special)
{
    global $db;
    $sql = "SELECT id
            FROM groups
            WHERE name='$name'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        // the group already exists
        return false;
    }
    else {
        // create it
        $sql = "INSERT INTO groups (name,description,special)
                VALUES ('$name','$description','$special')";
        $res = $db->query($sql);
    }
    return true;
}

// this function deletes group $groupname, and all its possible
// associations (users, authorizations...)
function deleteGroup($groupname)
{
    global $db;
    // get group id
    $sql = "SELECT id
            FROM groups
            WHERE name='$groupname'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        $groupid = $row[id];

        // delete group
        $sql = "DELETE
                FROM groups
                WHERE id='$groupid'";
        $res = $db->query($sql);

        // delete user associations
        $sql = "DELETE
                FROM user_groups
                WHERE groupID='$groupid'";
        $res = $db->query($sql);

        // delete authorizations
        unsetGroupAuthorization($groupid);

        return true;
    }
    else {
        return false;
    }
}

// this function adds user $userid to group $groupid
// $groupid must be a local group (not LDAP)
function addUserToGroup($userid, $groupid)
{
    global $db;
    $sql = "SELECT special
            FROM groups
            WHERE id='$groupid'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        if ($row[special] != "LDAP") {
            // check if user $userid exists
            $sql = "SELECT id
                    FROM user
                    WHERE id='$userid'";
            $res = $db->query($sql);
            if ($row = $res->fetchRow()) {
                $sql = "INSERT INTO user_groups
                        VALUES('$userid','$groupid')";
                $res = $db->query($sql);
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
    else {
        return false;
    }
}

/////////////////////////////////////////
//     user management functions       //
/////////////////////////////////////////

// this function returns the list of all existing users
function listAllUsers()
{
    global $db;
    $result = array();
    $sql = "SELECT id
            FROM user
            WHERE email!=''";
    $res = $db->query($sql);
    while ($row = $res->fetchRow()) {
        $result[$row[id]] = getEmail($row[id]);
    }
    return $result;
}

// this function checks if user $username is registered
function isRegisteredUser($username)
{
    global $db;
    if ($username == "") {
        return false;
    }
    $sql = "SELECT id
            FROM user
            WHERE email='".mysql_escape_string($username)."'";
    $res = $db->query($sql);
    if ($row = $res->fetchRow()) {
        return true;
    }
    else {
        return false;
    }
}

// this function changes user $userid's password into a new one
function changePassword($userid,$password)
{
    global $db;
    $sql = "UPDATE user
            SET password='$password'
            WHERE id='$userid'";
    $res = $db->query($sql);
    if ($db->affectedRows() == 0) {
        return false;
    }
    else {
        return true;
    }
}

/////////////////////////////////////////
//       miscellaneous functions       //
/////////////////////////////////////////

// this function turns an array into a string containing the
// comma-separated list of values in the array
function createListFromArray($array)
{
    $result = "";
    while ($value = current($array)) {
        $result .= "'$value',";
        next($array);
    }
    $result = ereg_replace(",$","",$result);

    return $result;
}

////////////////////////////////////////////////////
//                                                //
// PART 3: SESSION STARTING                       //
//                                                //
////////////////////////////////////////////////////

session_start();
if (!session_is_registered("userid")) {
    $userid = createGuestUser();
}
?>
