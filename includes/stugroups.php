<?php

function deletegroupset($grpsetid, $gpcourseid) {
	global $DBH;
	$grpsetid = intval($grpsetid);
        $gpcourseid = intval($gpcourseid);
	$query = "SELECT id FROM imas_stugroups WHERE groupsetid=$grpsetid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared
	//DB while ($row = mysql_fetch_row($result)) {
	while ($row = $stm->fetch(PDO::FETCH_NUM)) {
		deletegroup($row[0]);
	}
	$query = "DELETE FROM imas_stugroupset WHERE id=$grpsetid AND courseid=$gpcourseid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$query = "UPDATE imas_assessments SET isgroup=0,groupsetid=0 WHERE groupsetid=$grpsetid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$query = "UPDATE imas_forums SET groupsetid=0 WHERE groupsetid=$grpsetid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$query = "UPDATE imas_wikis SET groupsetid=0 WHERE groupsetid=$grpsetid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

}

function deletegroup($grpid,$delposts=true) {
	global $DBH;
	$grpid = Sanitize::onlyInt($grpid);
	removeallgroupmembers($grpid);

	if ($delposts) {
		//DB $query = "SELECT id FROM imas_forum_threads WHERE stugroupid=$grpid";
		//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
		$stm = $DBH->query("SELECT id FROM imas_forum_threads WHERE stugroupid=$grpid"); //sanitized above - no need for prepared
		$todel = array();
		//DB while ($row = mysql_fetch_row($result)) {
		while ($row = $stm->fetch(PDO::FETCH_NUM)) {
			$todel[] = $row[0];
		}
		if (count($todel)>0) {
			$dellist = implode(',',$todel);  //known to be safe INTs
			$query = "DELETE FROM imas_forum_threads WHERE id IN ($dellist)";
			//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
			$stm = $DBH->query($query); //sanitized above - no need for prepared
			$query = "DELETE FROM imas_forum_posts WHERE threadid IN ($dellist)";
			//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
			$stm = $DBH->query($query); //sanitized above - no need for prepared
		}
	} else {
		$query = "UPDATE imas_forum_threads SET stugroupid=0 WHERE stugroupid=$grpid";
		//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
		$stm = $DBH->query($query); //sanitized above - no need for prepared
	}
	$query = "DELETE FROM imas_stugroups WHERE id=$grpid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$query = "DELETE FROM imas_wiki_revisions WHERE stugroupid=$grpid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared
}

function removeallgroupmembers($grpid) {
	global $DBH;
	$grpid = intval($grpid);
	$query = "DELETE FROM imas_stugroupmembers WHERE stugroupid=$grpid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	//$query = "SELECT assessmentid,userid FROM imas_assessment_sessions WHERE agroupid=$grpid";
	//$result = mysql_query($query) or die("Query failed : " . mysql_error());

	//any assessment session using this group, set group to 0
	$query = "UPDATE imas_assessment_sessions SET agroupid=0 WHERE agroupid=$grpid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$now = time();

	if (isset($GLOBALS['CFG']['log'])) {
		$query = "INSERT INTO imas_log (time,log) VALUES ($now,'deleting members from $grpid')";
		//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
		$stm = $DBH->query($query); //sanitized above - no need for prepared
	}
}

function removegroupmember($grpid, $uid) {
	global $DBH;
	$grpid = intval($grpid);
	$uid = intval($uid);
	$query = "DELETE FROM imas_stugroupmembers WHERE stugroupid=$grpid AND userid=$uid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	//update any assessment sessions using this group
	$query = "UPDATE imas_assessment_sessions SET agroupid=0 WHERE agroupid=$grpid AND userid=$uid";
	//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
	$stm = $DBH->query($query); //sanitized above - no need for prepared

	$now = time();
	if (isset($GLOBALS['CFG']['log'])) {
		$query = "INSERT INTO imas_log (time,log) VALUES ($now,'deleting $uid from $grpid')";
		//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
		$stm = $DBH->query($query); //sanitized above - no need for prepared
	}
}

?>
