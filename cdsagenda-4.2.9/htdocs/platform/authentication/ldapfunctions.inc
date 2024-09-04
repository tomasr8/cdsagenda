<?php
// $Id: ldapfunctions.inc,v 1.1.2.2 2004/07/29 07:09:06 tbaron Exp $

// ldapfunctions.php --- manages all ldap connection functions
//
// Copyright (C) 2003  CDS Team <cds.support@cern.ch>
//     http://cds.cern.ch
//
// Author(s): Thomas Baron <thomas.baron@cern.ch>
//
// ldapfunctions.php is part of eAgenda.
//
// eAgenda is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// eAgenda is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with eAgenda; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// Commentary:
// Non-CERN user should consider modifying this script for local use.
// The architecture of LDAP servers can greatly differ from one imp to
// the other
//

function getLDAPemail($username) 
{
    $ds=ldap_connect("ldap.cern.ch");
    if ($ds) { 
        $r=ldap_bind($ds,"","");
        // Search surname entry
        $sr=ldap_search($ds,"ou=people, o=cern, c=ch", "uid=".$username);
        if (ldap_count_entries($ds,$sr)) {
            $info = ldap_get_entries($ds, $sr);
            $email = $info[0]['mail'][0];
        }
    }
    ldap_close($ds);
    return $email;
}

function getMyLDAPGroups($userid,&$result)
{
    global $db;
    $ds=ldap_connect("ldap.cern.ch");
    if ($ds) { 
        $r=ldap_bind($ds);
        // Search surname entry
        $sr=ldap_search($ds,"ou=people, o=cern, c=ch", "mail=".getEmail($userid));
        if (ldap_count_entries($ds,$sr)) {
            $info = ldap_get_entries($ds, $sr);
            if ($info[0]) {
                for ($i=0;$i<count($info[0]['ou']);$i++) {
                    $groupName = $info[0]['ou'][$i];
                    // check if the found group is meaningful for
                    // CDS Agenda
                    $sql = "SELECT id
                            FROM groups
                            WHERE name='$groupName'";
                    $res = $db->query($sql);
                    if ($row = $res->fetchRow()) {
                        array_push($result, $row[id]);
                    }
                }
            }
        }
    }
    ldap_close($ds);
}

function getLDAPGroupMembers($groupname,&$result)
{
    $ds=ldap_connect("ldap.cern.ch");
    if ($ds) { 
        $r=ldap_bind($ds);
        // build filter
        list($division,$group,$section) = explode(" ",$groupname);
        if ($group != "") { $groupfilter = "(group=$group)"; }
        if ($section != "") { $sectionfilter = "(ou=$groupname)"; }
        $filter = "(&(division=$division)$groupfilter$sectionfilter)";

        // Search surname entry
        $sr=ldap_search($ds,"ou=people, o=cern, c=ch", $filter);
        if (ldap_count_entries($ds,$sr)) {
            $info = ldap_get_entries($ds, $sr);
            for ($i=0;$i<count($info)-1;$i++) {
                if (isRegisteredUser($info[$i]['mail'][0])||isRegisteredUser($info[$i]['uid'][0])) {
                    $addtext = " <font color=green>(registered)</font>";
                }
                else {
                    $addtext = " <font color=red>(not registered)</font>";
                }
                array_push($result,$info[$i]['sn'][0].", ".$info[$i]['givenname'][0].$addtext);
            }
        }
    }
    ldap_close($ds);
}

?>
