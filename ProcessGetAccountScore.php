<?php
	require_once('./Classes/DBGoldoFight.php');
	session_start();

	if (!isset($_SESSION['IdAccount'])) return;

	$IdAccount = $_SESSION['IdAccount'];

	$DB = new GoldoFightDB();
	
	$IdActivity = 0;
	if (isset($_POST['IdActivity']))
	{
		$IdActivity = $_POST['IdActivity'];
	}
	
	
	// $ScoreSortName : 1-Score, 1-CeateDate, 1-Activity
	// $ScoreSortType : 1-ASC, 2 - DESC
	$ScoreSortName = 1;
	$ScoreSortType = 2;

	if (isset($_POST['ScoreSortName']))
	{
		$ScoreSortName = $_POST['ScoreSortName'];
	}

	if (isset($_POST['ScoreSortType']))
	{
		$ScoreSortType = $_POST['ScoreSortType'];
	}

	
	
	if ($IdActivity == 0) $Rows = Scores::getAllUserScore($DB,$IdAccount,$ScoreSortName,$ScoreSortType);
	else $Rows = Scores::getAllUserScoreByActivity($DB,$IdAccount,$IdActivity,$ScoreSortName,$ScoreSortType);
	
	$DB->close();
?>



    <div class="tableFixHead">
      <table>
        <thead>
          <tr>
            <th>Score</th>
            <th>Date</th>
            <th>Activité</th>
            <!-- <th>Tour</th> -->
            <th>Dur&#233;e</th>
            <th>Click</th>
          </tr>
        </thead>
        <tbody>
		<?php
			foreach ($Rows as $Row)
			{
				
				echo "<tr><td>$Row->Score</td>";
				echo "<td>$Row->CreateDate</td>";
				echo "<td>$Row->ActivityName</td>";
				// echo "<td>$Row->Tour</td>";
				echo "<td>".Scores::FormatGameTime($Row->Minutes)."</td>";
				echo "<td>$Row->Nbclick</td><tr>";
				
				
			}
		?>

        </tbody>
      </table>
    </div>





