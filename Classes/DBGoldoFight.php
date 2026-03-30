<?php
require_once('DBClass.php');

class GoldoFightDB extends DBClass
{
	var $classQuery;
	var $cnx;
	
	var $errno = '';
	var $error = '';
	
	// Connects to the database
	function __construct()
	{
		DBClass::__construct();
	}
	
	// Executes a database query
	function SelectLastInsertedAccount() 
	{
		$lastInsertedId = $this->lastInsertedID();
		$acc = Account::getAccount($this,$lastInsertedId);
		printf("%d - %s - %s - %s - %s - %s<br>",$acc->IdAccount,$acc->Nom,$acc->Prenom,$acc->Email,$acc->Pseudo,$acc->Password);
	}

	function SelectAllAccount() 
	{
		$accs = Account::getAllAccount($this);
		foreach ($accs as $acc)
		{
			printf("%d - %s - %s - %s - %s - %s<br>",
			$acc->IdAccount,
			$acc->Nom,
			$acc->Prenom,
			$acc->Email,
			$acc->Pseudo,
			$acc->Password);
		}
	}
	
	function isNullOrEmpty($str) {
		return (!isset($str) || trim($str) === '');
	}
}	


class Account
{
    public $IdAccount;
    public $Nom;
    public $Prenom;
    public $Email;
    public $Pseudo;
    public $Password;
    public $CreateDate;
    public $ActivationDate;
    public $Token;

    public function isActive()
	{
		return ($this->ActivationDate != null && $this->ActivationDate >= $this->CreateDate);
	}

    public static function getAccount($DB,$id)
    {
		$query = "SELECT IdAccount, Nom, Prenom, Email, Pseudo, Password ,CreateDate ,ActivationDate,Token FROM account WHERE IdAccount = $id";	
        if ($result = $DB->query($query))
		{
			return $result->fetch_object('Account');
		}
    }

    public static function getAccountByLogin ($DB,$login)
    {
		$IsMailExist = Account::IsEmailExists($DB, $login);
		if (!$IsMailExist) $IsPseudoExists = Account::IsPseudoExists($DB, $login);
		
		if (!$IsMailExist && !$IsPseudoExists) return null;
		
		$clause = ($IsMailExist) ? "Email = '$login'" : "Pseudo = '$login'";
		$query = "SELECT IdAccount, Nom, Prenom, Email, Pseudo, Password ,CreateDate ,ActivationDate ,Token FROM account WHERE $clause";	

        if ($result = $DB->query($query))
		{
			return $result->fetch_object('Account');
		}
    }
	
	public static function IsEmailExists($DB,$Email) 
	{
		$query = "SELECT IdAccount FROM account WHERE Email = '$Email'";

		$result = $DB->query($query);
		$obj = $result->fetch_object();
		
		return ($obj != null);
	}
	
	public static function IsPseudoExists($DB,$Pseudo) 
	{
		$query = "SELECT IdAccount FROM account WHERE Pseudo = '$Pseudo'";

		$result = $DB->query($query);
		$obj = $result->fetch_object();
		
		return ($obj != null);
	}
	
	
    public static function getAllAccount($DB) 
    {
        $return = array();
        if ($result = $DB->query("SELECT IdAccount, Nom, Prenom, Email, Pseudo, Password ,CreateDate ,ActivationDate ,Token FROM account")) {
            while ($obj = $result->fetch_object('Account')) { 
				array_push ($return,$obj);
            }
			$DB->freeResult($result);
            return $return;
        }
        return $return;
    }	

    public static function CheckPassword($password,$md5db)
    {
        $md5pass = strtoupper(md5($password));
		return ($md5pass == $md5db);
    }

    public static function SetSecretToken ($DB,$Secret,$IdAccount)
    {
		$Secret = $DB->escapeString($Secret);
		$query = "UPDATE account SET Token = '$Secret' WHERE IdAccount = $IdAccount";
		return $DB->query($query);
	}
	
	public static function IsSecretExists($DB,$Secret,$IdAccount) 
	{
		$query = "SELECT Token FROM account WHERE IdAccount = $IdAccount AND Token = '$Secret'";

		$result = $DB->query($query);
		$obj = $result->fetch_object();
		
		return ($obj != null);
	}
	
    public static function SetNewPassword ($DB,$Password,$IdAccount)
    {
		$Password = $DB->escapeString($Password);
		$Md5 = strtoupper(md5($Password));
		$query = "UPDATE account SET Password = '$Md5', Token = '' WHERE IdAccount = $IdAccount";
		return $DB->query($query);
	}

    public static function ActivateAccount ($DB,$IdAccount)
    {
		$query = "UPDATE account SET ActivationDate = CURRENT_TIMESTAMP, Token = '' WHERE IdAccount = $IdAccount";
		return $DB->query($query);
	}
}

/*
SELECT 
    act.Nom,
    acc.`Nom`,
    acc.`Prenom`,
    acc.`Pseudo`,
    sc.`Score`
FROM scores AS sc
INNER JOIN account acc ON acc.IdAccount = sc.IdAccount
INNER JOIN activities act ON act.IdActivity = sc.IdActivity
INNER JOIN
(
        SELECT
        MAX(Score) AS maxscore
        FROM scores  WHERE IdActivity = 1
) AS best ON best.maxscore = sc.Score

*/

class Scores
{
    public $IdScore;
    public $IdAccount;
    public $IdActivity;
    public $ActivityName;
    public $Score;
    public $CreateDate;
    public $Tour;
    public $Minutes;
    public $Nbclick;

    public static function getAllUserScore($DB,$IdAccount,$ScoreSortName,$ScoreSortType) 
    {
        $return = array();
		$query = Scores::getQuery($DB,$IdAccount,0,$ScoreSortName,$ScoreSortType);

        if ($result = $DB->query($query)) {
            while ($obj = $result->fetch_object('Scores')) { 
				array_push ($return,$obj);
            }
			$DB->freeResult($result);
            return $return;
        }
        return $return;
    }

    public static function getAllUserScoreByActivity($DB,$IdAccount,$IdActivity,$ScoreSortName,$ScoreSortType) 
    {
        $return = array();
		
		$query = Scores::getQuery($DB,$IdAccount,$IdActivity,$ScoreSortName,$ScoreSortType);

        if ($result = $DB->query($query)) {
            while ($obj = $result->fetch_object('Scores')) { 
				array_push ($return,$obj);
            }
			$DB->freeResult($result);
            return $return;
        }
        return $return;
    }

    public static function GetUserBetterScoreList ($DB,$IdAccount)
    {
		$query = "SELECT DISTINCT ac.Nom AS ActivityName, MAX(sc.Score) AS Score, sc.CreateDate, sc.Tour, sc.Nbclick, sc.Minutes FROM scores AS sc";
		$query .= " INNER JOIN activities AS ac ON ac.IdActivity = sc.IdActivity WHERE sc.IdAccount = $IdAccount";
		$query .= " GROUP BY ac.Nom ORDER BY sc.Score DESC";

		return Scores::getScoreListe ($DB,$query);
    }

    public static function GetUserAnalyticScoreList ($DB,$IdAccount)
    {
		$query = "SELECT DISTINCT ac.Nom AS ActivityName,";
		$query .= " COUNT(sc.Score) AS NbScore,";
		$query .= " MIN(sc.Score) AS MinScore,";
		$query .= " MAX(sc.Score) AS MaxScore,";
		$query .= " MIN(sc.Minutes) AS MinDuration,";
		$query .= " MAX(sc.Minutes) AS MaxDuration FROM scores AS sc";
		$query .= " INNER JOIN activities AS ac ON ac.IdActivity = sc.IdActivity";
		$query .= " WHERE sc.IdAccount = $IdAccount";
		$query .= " GROUP BY ac.Nom ORDER BY MaxScore DESC";

		return Scores::getScoreListe ($DB,$query);
    }

	public static function getScoreListe ($DB,$query)
	{
        $return = array();

		if ($result = $DB->query($query)) {
            while ($obj = $result->fetch_object('Scores')) { 
				array_push ($return,$obj);
            }
			$DB->freeResult($result);
            return $return;
        }
        return $return;
	}

    public static function saveScore($DB,$IdAccount,$IdActivity, $Score,$Nbclick,$Tour,$Minutes) 
    {
		$query = sprintf("INSERT INTO scores (IdAccount, IdActivity, Score, Nbclick, Tour, Minutes) VALUES (%d, %d, %d, %d, %d, %d)", $IdAccount, $IdActivity, $Score, $Nbclick, $Tour, $Minutes);
		$DB->query($query);
    }

    public static function getBestActivityScore ($DB,$IdActivity)
    {
		$query = "SELECT act.Nom AS ActivityName, acc.Nom, acc.Prenom, acc.Pseudo, sc.Score FROM scores AS sc";
		$query .= " INNER JOIN account acc ON acc.IdAccount = sc.IdAccount";
		$query .= " INNER JOIN activities act ON act.IdActivity = sc.IdActivity";
		$query .= " INNER JOIN (SELECT MAX(Score) AS maxscore FROM scores  WHERE IdActivity = $IdActivity) AS best ON best.maxscore = sc.Score";
		return Scores::getScoreListe ($DB,$query);
    }

    public static function getAllBestActivityScore ($DB)
    {
		// $query = "SELECT act.Nom AS ActivityName, acc.Nom, acc.Prenom, acc.Pseudo, sc.Score FROM scores AS sc";
		// $query .= " INNER JOIN account acc ON acc.IdAccount = sc.IdAccount";
		// $query .= " INNER JOIN activities act ON act.IdActivity = sc.IdActivity";
		// $query .= " GROUP BY acc.Prenom, acc.nom, act.Nom";
		// $query .= " ORDER BY MAX(sc.Score) DESC";
		
		
		$query = "SELECT act.Nom AS ActivityName, act.IdActivity , acc.Nom, acc.Prenom, acc.Pseudo, MAX(sc.Score) AS Score FROM scores AS sc";
		$query .= " INNER JOIN account acc ON acc.IdAccount = sc.IdAccount";
		$query .= " INNER JOIN activities act ON act.IdActivity = sc.IdActivity";
		$query .= " GROUP BY acc.Prenom, acc.nom, act.Nom";
		$query .= " ORDER BY act.IdActivity DESC, MAX(sc.Score) DESC";
		

		return Scores::getScoreListe ($DB,$query);
    }

	public static function FormatGameTime($gameTime)
	{
		
		$timeDiff = (int) ($gameTime / 1000);
		$seconds = floor($timeDiff % 60);

		// Extract integer minutes that don't form an hour using %
		$timeDiff = floor($timeDiff / 60);
		$minutes = $timeDiff % 60; //no need to floor possible incomplete minutes, because they've been handled as seconds

		// Extract integer hours that don't form a day using %
		$timeDiff = floor($timeDiff / 60);
		$hours = $timeDiff % 24; //no need to floor possible incomplete hours, because they've been handled as seconds
		
		$timeAsString =
		($hours < 10 ? "0".$hours : $hours."")
		.":".
		($minutes < 10 ? "0".$minutes : $minutes."")
		.":".
		($seconds < 10 ? "0".$seconds : $seconds."");
		return $timeAsString;
	}	

	// $ScoreSortName : 1-Score, 1-CeateDate, 1-Activity
	// $ScoreSortType : 1-ASC, 2 - DESC
	private static function getQuery($DB,$IdAccount,$IdActivity,$ScoreSortName,$ScoreSortType)
	{
		$query = "SELECT sc.IdScore, sc.IdAccount, sc.IdActivity, ac.Nom AS ActivityName, sc.Score, sc.CreateDate, sc.Tour, sc.Nbclick, sc.Minutes FROM scores AS sc";
		$query .= " INNER JOIN activities AS ac ON ac.IdActivity = sc.IdActivity WHERE sc.IdAccount = $IdAccount";
		if ($IdActivity > 0) $query .= " AND sc.IdActivity = $IdActivity";
		
		
		switch ($ScoreSortName)
		{
			case 1:$ScoreSortName = "sc.Score";break;
			case 2:$ScoreSortName = "sc.CreateDate";break;
			case 3:$ScoreSortName = "ac.Nom";break;
		}

		switch ($ScoreSortType)
		{
			case 1:$ScoreSortType = "ASC";break;
			case 2:$ScoreSortType = "DESC";break;
		}
		
		$query .= " ORDER BY $ScoreSortName $ScoreSortType";
		$query .= " LIMIT 100";

		// echo $query;
		return $query;
	}
}

// Draft - non visible des membres
// Pending - Futur activitée
// Active - Encours
// closed - Encours

class ActStatus {
    public static int $Draft = 1;
    public static int $Pending = 2;
    public static int $Active = 3;
    public static int $Closed = 4;
}

class Activity
{
	public $IdActivity;
	public $Nom;
	public $Name;			// Unique name
	public $Info;
	public $CreateDate;
	public $StartDate;
	public $EndDate;
	public $Statu;

    public static function getActivity($DB,$idActivity)
    {
		$query = "SELECT IdActivity, Nom, Name, Info, CreateDate, StartDate, EndDate FROM activities WHERE IdActivity = $idActivity";
        if ($result = $DB->query($query))
		{
			return $result->fetch_object('Activity');
		}
    }

    public static function getAllActivities($DB,$Active) 
    {
        $query = "SELECT IdActivity, Nom, Name, Info, CreateDate, StartDate, EndDate FROM activities";
		if ($Active == true)
		{
			$query .= " WHERE EndDate >= '".date("Y-m-d H:i:s")."'";
		}

		return Activity::getActivityList ($DB,$query);
    }

    public static function getActivityList ($DB,$query) 
    {
        $return = array();
		$now = date("Y-m-d H:i:s");
		if ($result = $DB->query($query)) {
            while ($obj = $result->fetch_object('Activity')) { 
				
				if ($obj->StartDate == null || $obj->EndDate == null)
				{
					$obj->Statu = ActStatus::$Draft;
				}
				else if ($now > $obj->EndDate)
				{
					$obj->Statu = ActStatus::$Closed;
				}
				else if ($now >= $obj->StartDate && $now <= $obj->EndDate)
				{
					$obj->Statu = ActStatus::$Active;
				}
				else if ($now < $obj->StartDate)
				{
					$obj->Statu = ActStatus::$Pending;
				}
				
				array_push ($return,$obj);
            }
			$DB->freeResult($result);
            return $return;
        }
        return $return;
    }
}

?>
