<?php
	session_start();
?>
<!DOCTYPE HTML>
<html>

<head>
  <title>EPL 425 - Questions Game</title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="keywords" content="EPL425, PHP, Home Page, Questions Game">
  <meta name="author" content="Christiana Giapintzaki">
  <meta name="description" content="Homework 3 - EPL425">
  <link rel="shortcut icon" href="icon.png">
  <link rel="stylesheet" type="text/css" href="style.css" title="style" />
</head>

<body>
  <a name="top"></a>
  <div id="main">
  
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <h1><a href="index.php"><span class="logo_colour">QUESTIONS GAME</span></a></h1>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <li class="selected"><a href="index.php">Home</a></li>
          <li><a href="help.php">Help</a></li>
          <li><a href="scores.php">Score Board</a></li>          
        </ul>
      </div>
    </div>
    <?php
        if(file_exists('questions.xml')){
            $xml = simplexml_load_file('questions.xml');
        }
        else {
            die("Error with xml load file.");
        }
    ?>
    <div id="site_content">
      <div id="content">
		<form id="welcome" action="index.php" method="post">
                    <div
				<?php if (isset($_POST['start'])) echo 'style="display: none;"';
					elseif (isset($_POST['next'])) echo 'style="display: none;"';
					elseif (isset($_POST['finish'])) echo 'style="display: none;"';
					elseif (isset($_POST['yes'])) echo 'style="display: none;"';
					elseif (isset($_POST['no'])) echo 'style="display: block;"';
					elseif (isset($_POST['end'])) echo 'style="display: block;"';
				?>>
                                <br>
                                <h3 id="welcomeMsg">Welcome to the EPL 425 Questions Game!<br></h3>
				<input type="submit" name="start" value="Start">
                    </div>
		</form>
          
          <form id="quiz" action="index.php" method="post">
			<div class="question" 
				<?php 
                                    if (isset($_POST['start'])){ 
                                        echo 'style="display: block;"';
                                    }
                                    elseif (isset($_POST['next'])){ 
                                        echo 'style="display: block;"';
                                    }
                                    else{
                                        echo 'style="display: none;"';
                                    } 
				?>>
				<?php
					if(isset($_POST['start'])){
						session_destroy();
						session_start();
                                                
						$table = array();
                                                $_SESSION['table'] = $table;
                                                
                                                //fill visit tables with 0 
                                                $easyVisit = array_fill(0, 25, 0);
                                                $mediumVisit = array_fill(0, 25, 0);
                                                $difficultVisit = array_fill(0, 25, 0);
                                                
                                                //pick a random number for the first question
						$numQuestion = rand(0,24);      
                                                
                                                //make first question visited
						$mediumVisit[$numQuestion] = 1;
                                                
                                                //get first question and possible answers from xml file
						$question = $xml->medium->question[$numQuestion]->questionText;
						$answer1 = $xml->medium->question[$numQuestion]->answer[0];
						$answer2 = $xml->medium->question[$numQuestion]->answer[1];
                                                $answer3 = $xml->medium->question[$numQuestion]->answer[2];
                                                $answer4 = $xml->medium->question[$numQuestion]->answer[3];
                                                
                                                //get the correct answer 
						if($xml->medium->question[$numQuestion]->answer[0]["Correct"] == "true"){
                                                    $correct = 1;
                                                }
                                                elseif($xml->medium->question[$numQuestion]->answer[1]["Correct"] == "true"){
                                                    $correct = 2;
                                                }
                                                elseif($xml->medium->question[$numQuestion]->answer[2]["Correct"] == "true"){
                                                    $correct = 3;
                                                }
                                                else{
                                                    $correct = 4;
                                                }
                                               
                                                //save level and correct answer 
						$tempArr = array();
						$tempArr["level"] = "medium";
						$tempArr["correct"] = $correct;
                                                
                                                $_SESSION['table'][0] = $tempArr;
						$_SESSION['easyVisit'] = $easyVisit;
						$_SESSION['mediumVisit'] = $mediumVisit;
						$_SESSION['difficultVisit'] = $difficultVisit;
					}
					if(isset($_POST['next'])){
                                                //get answer of the player
						$answer = $_POST['answer'];
                                                
                                                //convert it to integer
						$answer = intval($answer);
                                                
                                                //fill the answer column of the table 
						$_SESSION['table'][sizeof($_SESSION['table'])-1]["answer"] = $answer;
                                                
                                                $row = $_SESSION['table'][sizeof($_SESSION['table'])-1];
                                                //check if answer is correct and choose level of next question
						if($row["correct"] === $row["answer"]){
                                                    //get higher level
							if($row["level"] === "easy"){
                                                            $newLevel = "medium";
                                                        }
                                                        elseif($row["level"] === "medium"){
                                                            $newLevel = "difficult";
                                                        }
                                                        else{
                                                            $newLevel = "difficult";
                                                        }
						}else{
                                                    //get lower level
							if($row["level"] === "easy" || $row["level"] === "medium"){
                                                            $newLevel = "easy";
                                                        }
                                                        else{
                                                            $newLevel = "medium";
                                                        }
						}
                                                
						$numQuestion = rand(0,24);
                                                
                                                switch ($newLevel) {
                                                    case "easy":
                                                        while($_SESSION['easyVisit'][$numQuestion] === 1){
								$numQuestion = rand(0,24);
							}
							$_SESSION['easyVisit'][$numQuestion] = 1;
                                                        break;
                                                    case "medium":
                                                        while($_SESSION['mediumVisit'][$numQuestion] === 1){
								$numQuestion = rand(0,24);
							}
							$_SESSION['mediumVisit'][$numQuestion] = 1;
                                                        break;
                                                    case "difficult":
                                                        while($_SESSION['difficultVisit'][$numQuestion] === 1){
								$numQuestion = rand(0,24);
							}
							$_SESSION['difficultVisit'][$numQuestion] = 1;
                                                        break;
                                                }
                                                
						$question = $xml->$newLevel->question[$numQuestion]->questionText;
						$answer1 = $xml->$newLevel->question[$numQuestion]->answer[0];
						$answer2 = $xml->$newLevel->question[$numQuestion]->answer[1];
                                                $answer3 = $xml->$newLevel->question[$numQuestion]->answer[2];
                                                $answer4 = $xml->$newLevel->question[$numQuestion]->answer[3];
                                                
                                                //get the correct answer 
						if($xml->$newLevel->question[$numQuestion]->answer[0]["Correct"] == "true"){
                                                    $correct = 1;
                                                }
                                                elseif($xml->$newLevel->question[$numQuestion]->answer[1]["Correct"] == "true"){
                                                    $correct = 2;
                                                }
                                                elseif($xml->$newLevel->question[$numQuestion]->answer[2]["Correct"] == "true"){
                                                    $correct = 3;
                                                }
                                                else{
                                                    $correct = 4;
                                                }
                                               
                                                //save level and correct answer 
						$tempArr = array();
						$tempArr["level"] = $newLevel;
						$tempArr["correct"] = $correct;
                                                
                                                $_SESSION['table'][sizeof($_SESSION['table'])] = $tempArr;
                                                
					}
					if(isset($_POST['finish'])){
						//get answer of the player
						$answer = $_POST['answer'];
                                                
                                                //convert it to integer
						$answer = intval($answer);
                                                
                                                //fill the answer column of the table 
						$_SESSION['table'][sizeof($_SESSION['table'])-1]["answer"] = $answer;
					}
				?>
                                <br><br>
                                <h4><strong>QUESTION: <?php echo htmlspecialchars($question); ?></strong></h4><br>
                                <input type="radio" name="answer" value="1" autocomplete="off"> <?php echo htmlspecialchars($answer1);?></input><br>
		  		<input type="radio" name="answer" value="2" autocomplete="off"> <?php echo htmlspecialchars($answer2);?></input><br>
		  		<input type="radio" name="answer" value="3" autocomplete="off"> <?php echo htmlspecialchars($answer3);?></input><br>
                                <input type="radio" name="answer" value="4" autocomplete="off"> <?php echo htmlspecialchars($answer4);?></input><br>
                                <br><br>
				<?php
                                        //calculate remaining questions
					$num = sizeof($_SESSION['table']);
					$remainingQ = 7 - $num;
					if ($num < 7) {?>
                                            <input type="submit" name="next" value="NEXT">
                                        <?php
                                        } else {
                                        ?>
                                            <input type="submit" name="finish" value="FINISH">
                                        <?php
                                        }
                                        ?>	
				<input type="submit" name="end" value="END">
                                <br><br>
				<div class="row">
					<div class="column">
						<p>Number of Question: <?php echo $num;?></p>
					</div>
					<div class="column">
						<p>Remaining Questions: <?php echo $remainingQ;?></p>
					</div>
   				</div>
			</div>
		</form>
        <form id="score" action="index.php" method="post">
			<div class="results" 
				<?php if (isset($_POST['finish'])) echo 'style="display: block"';
					else echo 'style="display: none;"'; 
				?>>
				<?php
                                        //give points for each correct answer based on its level
					$score = 0;
					for($i = 0; $i < sizeof($_SESSION['table']); $i++){
						if ($_SESSION['table'][$i]["correct"] === $_SESSION['table'][$i]["answer"]){
                                                    switch ($_SESSION['table'][$i]["level"]){
                                                        case "easy":
                                                            $score++;
                                                            break;
                                                        case "medium":
                                                            $score+=2;
                                                            break;
                                                        case "difficult":
                                                            $score+=3;
                                                            break;
                                                    }
						}
					}
				?>
				<table id="quizScores">
					<tr>
						<th>Question</th>
						<th>Level</th>
						<th>Answer</th>
						<th>Points</th>
					</tr>
					<?php
						for($i = 1; $i <= sizeof($_SESSION['table']); $i++){
					?>
						<tr>
							<th><?php echo "$i"; ?></th>
							<th><?php echo $_SESSION['table'][$i-1]["level"]; ?></th>
                                                        
							<th><?php 
								if ($_SESSION['table'][$i-1]["correct"] === $_SESSION['table'][$i-1]["answer"]){
                                                                    echo "Correct";
                                                                }
                                                                else{
                                                                    echo "Wrong";
                                                                }
							?></th>
							<th><?php 
                                                                //print points earned for each answer
								if ($_SESSION['table'][$i-1]["correct"] === $_SESSION['table'][$i-1]["answer"]){
                                                                    if($_SESSION['table'][$i-1]["level"] === "easy"){
                                                                        echo "1";
                                                                    }
                                                                    elseif($_SESSION['table'][$i-1]["level"] === "medium"){
                                                                        echo "2";
                                                                    }
                                                                    else{
                                                                        echo "3"; 
                                                                    }
								}else{
									echo "0"; 
								}
							?></th>
						</tr>
					<?php
						}
					?>
				</table>
				<p class="instructions2">Your overall score is <?php echo "$score"; ?></p>
				<p class="instructions2">Save score?</p>
				<input type="submit" name="yes" value="Yes">
				<input type="submit" name="no" value="No">
			</div>
		</form>
		<form id="enterName" action="index.php" method="post">
			<div class="name"
			<?php if (isset($_POST['yes'])) echo 'style="display: block;"';
				else echo 'style="display: none;"'; 
			?>>
                                <br><br>
                                <p class="instructions2">Enter your nickname:</p>
                                <input type="text" name="nickname" id="nickname" placeholder=" " maxlength="10" required><br><br>
				<input type="submit" name="save" value="Save">
				<?php
					if(isset($_POST['save'])){
                                        //calculate final score of player based on correct answers
					$finalScore = 0;
					for($i = 0; $i < sizeof($_SESSION['table']); $i++){
						if ($_SESSION['table'][$i]["correct"] === $_SESSION['table'][$i]["answer"]){
                                                    switch ($_SESSION['table'][$i]["level"]){
                                                        case "easy":
                                                            $finalScore++;
                                                            break;
                                                        case "medium":
                                                            $finalScore+=2;
                                                            break;
                                                        case "difficult":
                                                            $finalScore+=3;
                                                            break;
                                                    }
						}
					}
						$filename = "scores.txt";
						$open = file_get_contents($filename);
						$val = $_POST['nickname'];
						$open = $open . $val . " " . $finalScore . "\n"; 
						file_put_contents($filename, $open); 	
					}
				?>
			</div>
		</form>
      </div>
    </div>
    <div id="footer">
        <br><br><br><br>
        <a id="btt" href="#top">Back to Top</a>
    </div>
  </div>
</body>
</html>
