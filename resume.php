<?php
  //Pull in the POST for the selected profession.
	$res_type = trim($_POST['res_type']);

	//Call database
	include $_SERVER['DOCUMENT_ROOT'] . '/includes/db.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
	<meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<title>Resum&eacute</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="description" content="Resum&eacute for Joseph Rogina" />
    <meta name="keywords" content="joseph rogina, programmer, resum&eacute, portfolio, html5, CSS3, Bio, PHP, SQL" />
    <meta name="author" content="Joseph Rogina" />
    <link rel="shortcut icon" href="/favicon.ico"> 
	<link rel="stylesheet" type="text/css" href="/css/stylesheet.css">
</head>
<body>
<div id="rm-wrapper">
	<header class="rm-head">
		<h2>joseph rogina</h2>
		<h3>Resum&eacute Site</h3>
	</header>
	<div id="navigation">
	   <ul>
		  <li><a href="/resume/index.html">Back</a></li>
		  <li><a href="/index.html">Home</a></li>
		  <li><a href="/resume/<?php echo 'resume' . $res_type;?>.pdf">PDF</a></li>
		  <li><a href="/resume/login.php">Edit</a></li>
	   </ul>
	</div>
	<div class="rm-container">
		<div class="rm-content">
			<?php
			//Grab the resume header information.
			try
			{
				$sql = 'SELECT name, email, phone, address, city, state, zip FROM resumedata';
				$s = $pdo->query($sql);
			}
			catch (PDOException $e)
			{
				$error = 'Error getting resume header: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}
			$row = $s->fetch();
			$name = $row['name'];
			$email = $row['email'];
			$phone = $row['phone'];
			$address = $row['address'];
			$city = $row['city'];
			$state = $row['state'];
			$zip = $row['zip'];

			//Grab the career overview from the database for the selected profession.
			try
			{
				$sql = 'SELECT car_text FROM career WHERE car_id = :id';
				$s = $pdo->prepare($sql);
				$s->bindValue(':id', $res_type);
				$s->execute();
			}
			catch (PDOException $e)
			{
				$error = 'Error finding career overview: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}
			$row = $s->fetch();
			$career = $row['car_text'];

			//Grab the accomplishments from the database for the selected profession.
			try
			{
				$sql = 'SELECT accomplishment FROM accomplishments 
						WHERE car_id = :id';
				$s = $pdo->prepare($sql);
				$s->bindValue(':id', $res_type);
				$s->execute();
			}
			catch (PDOException $e)
			{
				$error = 'Error finding accomplishments: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}
			while ($row = $s->fetch()) {
				$accomplishments .= $row['accomplishment'] . " ";
			}

			//Grab the qualifications from the database for the selected profession.
			try
			{
				$sql = 'SELECT qualification FROM qualifications 
						WHERE car_id = :id';
				$qual_result = $pdo->prepare($sql);
				$qual_result->bindValue(':id', $res_type);
				$qual_result->execute();
			}
			catch (PDOException $e)
			{
				$error = 'Error finding qualifications: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}

			//Grab the skills from the database for the selected profession.
			try
			{
				$sql = 'SELECT skill, experience, years FROM skills 
						WHERE car_id = :id';
				$skill_result = $pdo->prepare($sql);
				$skill_result->bindValue(':id', $res_type);
				$skill_result->execute();
			}
			catch (PDOException $e)
			{
				$error = 'Error finding skills: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}

			//Grab the work experience from the database for the selected profession.
			try
			{
				$sql = 'SELECT company, city, state, position, duties, start_date, end_date FROM work 
						WHERE car_id = :id';
				$work_result = $pdo->prepare($sql);
				$work_result->bindValue(':id', $res_type);
				$work_result->execute();
			}
			catch (PDOException $e)
			{
				$error = 'Error finding work experience: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}

			//Grab the education and training from the database for the selected profession.
			try
			{
				$sql = 'SELECT school, city, state, degree, coursework, end_date FROM education';
				$ed_result = $pdo->query($sql);
			}
			catch (PDOException $e)
			{
				$error = 'Error finding education and training: ' . $e->getMessage();
				include '/includes/error.html.php';
				exit();
			}

			/*-------Start of output to screen-------*/
			echo '<div>';
			echo '<div class="rm-floatleft"><h2>' . $name . '</h2><h4>' . $city . ', ' . $state . '</h4></div>';
			echo '<div class="rm-floatright"><h2>' . $email . '</h2></div>';
			echo '</div><div style="clear:left;"></div>';
			echo '<hr />';
			/*-------Career overview section-------*/
			echo "<h3>Career Overview</h3>";
			echo "<p>" . $career . "</p>";
			/*-------Qualifications section-------*/
			echo "<h3>Qualifications</h3>";
			// Loop through the array of qualifications data, formatting it as 3 HTML columns.
			echo '<div><ul>';
			while($row = $qual_result->fetch()){
				echo '<li>' . $row['qualification'] . '</li>';
			}
			echo '</ul></div>';
			echo '<div class="clearleft"></div>';
			/*-------Technical Skills section-------*/
			if ($res_type != '2') {
				echo '<h3>Technical Skills</h3>';
				// Loop through the array of skill data, formatting it in an HTML table.
				echo '<table class="rm-table">';
				echo '<tr><th>Skills</th><th>Experience</th><th>Years</th></tr>';
				while($row = $skill_result->fetch()){
					// Display each qualification.
					echo "<tr><td>" . $row['skill'] . "</td><td>" . $row['experience'] . "</td><td>" . $row['years'] . "</td></tr>";
				}
				echo '</table>';
			}
			/*-------Accomplishments section-------*/
			echo "<h3>Accomplishments</h3>";
			echo "<p>" . $accomplishments . "</p>";
			/*-------Work Experience section-------*/
			echo "<h3>Work Experience</h3>";
			while($row = $work_result->fetch()){
				echo '<div class="dates">' . $row['start_date'] . '-' . $row['end_date'] . '</div>';
				echo '<div class="indent"><strong>' . $row['company'] . '</strong><br />';
				echo $row['city'] . ', ' . $row['state'] . '<br />';
				echo '<strong>' . $row['position'] . '</strong><br />';
				echo $row['duties'] . '</div>';
			}
			/*-------Education and Training section-------*/
			echo "<h3>Education and Training</h3>";
			while($row = $ed_result->fetch()){
				echo '<div class="dates">' . $row['end_date'] . '</div>';
				echo '<div class="indent"><strong>' . $row['school'] . '</strong><br />';
				echo $row['city'] . ', ' . $row['state'] . '<br />';
				echo $row['degree'] . '<br />';
				echo $row['coursework'] . '</div>';
			}
			?>
		</div><!-- /rm-content -->
	</div><!-- /rm-container -->
	<footer class="rm-foot">
		Copyrite &copy; 2013
	</footer>
</div>
</body>
</html>
