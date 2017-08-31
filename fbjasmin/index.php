<?php
require_once 'FacebookAPI.php';
$FacebookAPIObject = new FacebookAPI();

$FacebookAPIObject->facebookLogin();
?>
<!DOCTYPE html>
<html class="no-js" lang="en" ng-app="JazzBook">
<!--<![endif]-->
<head>

	<meta charset="UTF-8"/>

	<title>Welcome To JazzBook</title>

	<meta name="description" content="Onepage Multipurpose Bootstrap HTML Template">

	<meta name="author" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/animate.css">
	<link rel="stylesheet" href="css/theme.css">

	<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>

	<script type="text/javascript" src="js/angular.min.js"></script>	
	<link href="src/css/HoldOn.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed|Raleway" rel="stylesheet">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
	integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

	<!--Javascript-->
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
	integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
	crossorigin="anonymous"></script>
	<script src="js/HoldOn.min.js"></script>
	<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

</head>
<body ng-controller="albumController">
	<!--wrapper start-->
	<div class="wrapper" id="wrapper">

		<!--header-->
		<header>
			<div class="banner row" id="banner">		
				<div class="parallax text-center" style="background-image: url(img/back_fb_pic.jpg);">
					<div class="parallax-pattern-overlay">
						<div class="container text-center" style="height:600px;padding-top:170px;">
							<a href="#"><img id="site-title" class=" wow fadeInDown" wow-data-delay="0.0s" wow-data-duration="0.9s" src="img/fb_icon.svg" alt="logo"/></a>
							<h2 class="intro wow zoomIn" wow-data-delay="0.4s" wow-data-duration="0.9s">Welcome to JazzBook</h2>
						</div>
					</div>
				</div>
			</div>	
			
			
				<div  class="navbar-wrapper stuckMenu">
					<div  class="container">
						<div class="navwrapper">
							<div class="navbar navbar-inverse navbar-static-top">
								<div class="container">
									<div class="navArea">
										<div class="navbar-collapse collapse">
											<ul class="nav navbar-nav">
												<li class="menuItem text-center" ><?php
													echo '<button  ng-click="loginauth()"  class="btn btn-primary" name="loginBtn">
													Login with Facebook
												</button>';
												?></li>

											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
		</header>
		
</div>

		

		
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
		integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
		crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
		integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
		crossorigin="anonymous"></script>
		<script type="text/javascript">
			angular.module("JazzBook", []).controller("albumController", function ($window, $scope, $http) {
				$scope.loginauth=function () {
					$window.location="<?php echo $FacebookAPIObject->getLoginUrl();?>";
				}
			});


		</script>
		
		<script src="js/jquery.js"></script>
		<script src="js/modernizr.js"></script>
		<script src="js/menustick.js"></script>
		<script src="js/parallax.js"></script>
		<script src="js/easing.js"></script>
		<script src="js/wow.js"></script>
		<script src="js/smoothscroll.js"></script>
		<script src="js/masonry.js"></script>
		<script src="js/imgloaded.js"></script>
		<script src="js/classie.js"></script>
		<script src="js/colorfinder.js"></script>
		<script src="js/contact.js"></script>
		<script src="js/common.js"></script>

		<?php include 'Footer.php' ?>

	</body>
	</html>