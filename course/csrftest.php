<?php
//IMathAS:  Manage student groups
//(c) 2010 David Lippman

/*** master php includes *******/
require("../init.php");
require("../includes/htmlutil.php");
require("../includes/stugroups.php");
require_once("../includes/filehandler.php");

/*** pre-html data manipulation, including function code *******/

if (isset($_GET['rengrpset'])) {
		$renameGrpSet = sanitize::onlyInt($_GET['rengrpset']);            
		//renaming groupset
		if (isset($_GET['grpsetname'])) {
			//if name is set
			//DB $query = "UPDATE imas_stugroupset SET name='{$_POST['grpsetname']}' WHERE id='{$_GET['rengrpset']}'";
			//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
			$stm = $DBH->prepare("UPDATE imas_stugroupset SET name=:name WHERE id=:id");
			$stm->execute(array(':name'=>Sanitize::stripHtmlTags($_GET['grpsetname']), ':id'=>$renameGrpSet)); //formerly ':id'=>$_GET['rengrpset']
			header('Location: ' . $GLOBALS['basesiteurl'] . "/course/managestugrps.php?cid=$cid" . "&r=" . Sanitize::randomQueryStringParam());
                        echo "Name changed...";
			exit();
		} else {
			//DB $query = "SELECT name FROM imas_stugroupset WHERE id='{$_GET['rengrpset']}'";
			//DB $result = mysql_query($query) or die("Query failed : " . mysql_error());
			//DB $page_grpsetname = mysql_result($result,0,0);
			$stm = $DBH->prepare("SELECT name FROM imas_stugroupset WHERE id=:id AND courseid=:courseid");
			$stm->execute(array(':id'=>$renameGrpSet, ':courseid'=>$cid));
			$page_grpsetname = $stm->fetchColumn(0);
        }
}

$servername = "localhost";
$username = "homestead";
$password = "secret";
$dbname = "imathas_db";

// See what the groupsets and their IDs are
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, name FROM imas_stugroupset";
$result = $conn->query($sql);        

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"] . "<br>";
    }
} else {
    echo "0 results" . "<br>";
}
$conn->close();

echo "covfefe";

require("../footer.php");

?>