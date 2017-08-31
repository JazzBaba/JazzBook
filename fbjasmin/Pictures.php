<?php
include "FacebookAPI.php";

$FacebookAPIObject = new FacebookAPI();

if (isset($_GET['albumid'])) {
	if (isset($_SESSION['facebook_access_token'])) {
		$FacebookAPIObject->FacebookObject->setDefaultAccessToken($_SESSION['facebook_access_token']);
		$Profile = $FacebookAPIObject->getUserInfo();
		if(isset($_GET['prev']))
		{
			$TempUserAlbumImages = $FacebookAPIObject->FacebookObject->get("/" . $_GET['albumid'] . "/photos?fields=source,name,id&before=".$_GET['prev'])->getGraphEdge();	
		}
		else if(isset($_GET['next']))
		{
			$TempUserAlbumImages = $FacebookAPIObject->FacebookObject->get("/" . $_GET['albumid'] . "/photos?fields=source,name,id&after=".$_GET['next'])->getGraphEdge();	
		}
		else{
			$TempUserAlbumImages = $FacebookAPIObject->FacebookObject->get("/" . $_GET['albumid'] . "/photos?fields=source,name,id")->getGraphEdge(); //$FacebookAPIObject->getUserAlbumImages($_GET['albumid']);
			
		}
			$UserAlbumImages=$TempUserAlbumImages->asArray();
		
		$tempNext=$FacebookAPIObject->nextAlbum($TempUserAlbumImages);
		$tempPrv=$FacebookAPIObject->previousAlbum($TempUserAlbumImages);
		$nextURL=$TempUserAlbumImages->getmetaData()['paging']['cursors']['after'];
		$prevURl=$TempUserAlbumImages->getmetaData()['paging']['cursors']['before'];
		// echo "<pre>";
		// print_r($tempNext->getmetaData()['paging']['cursors']['after']);
		// echo "</pre>";
		// exit;
		$UserAlbumNameResponse = $FacebookAPIObject->FacebookObject->get("/" . $_GET['albumid']."?fields=name");
		$UserAlbumName = $UserAlbumNameResponse->getGraphNode()->asArray();

	} else {
		header("location:http://localhost/fbjasmin/");
	}
} else {
	header("location:./");
}
?>
<!DOCTYPE html>

<html class="no-js" lang="en">
<!--<![endif]-->
<head>

	<meta charset="UTF-8"/>

	<title><?php echo $Profile['name'].' '.$UserAlbumName['name']; ?></title>

	<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/animate.css">
	<link rel="stylesheet" href="css/theme.css">

	<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>
	<style type="text/css">
		a.album_title {
			text-decoration: none;
			color: #3b5998;
			font-size: larger;
			font-family: 'Roboto Condensed', sans-serif;
		}

		a.album_title:hover {
			text-decoration: underline;
		}
	</style>

</head>
<body>
<!-- onload="slideShow()" -->
	<header id="wrapper">
		<div class="banner row" id="banner">		
			<?php echo '<div class="parallax text-center" style="background-image: url('.$Profile["cover"]["source"].'
			);">';?>
			<div class="parallax-pattern-overlay">
				<div class="container text-center" style="height:600px;padding-top:170px;">
					<a href="#"><img id="site-title" style="border-radius: 50%" class=" wow fadeInDown" wow-data-delay="0.0s" wow-data-duration="0.9s" src="<?php echo $Profile['picture']['url'];?>" alt="logo"/></a>
					<h2 class="intro wow zoomIn" wow-data-delay="0.4s" wow-data-duration="0.9s">Welcome  <?php echo $Profile['name'];?></h2>
				</div>
			</div>
		</div>
		<div class="navbar-wrapper stuckMenu">
			<div class="container">
				<div class="navwrapper">
					<div class="navbar navbar-inverse navbar-static-top">
						<div class="container">
							<div class="navArea">
								<div class="navbar-collapse collapse">
									<ul class="nav navbar-nav">
										<li class="menuItem"><a href="Album.php">Albums</a></li>
										<li class="menuItem"><a href="logout.php">Logout</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</header>	


	<!--gallery-->
	<section class="gallery" id="gallery">
		<div class="container">
			<div class="heading text-center">
				<img class="dividerline" src="img/sep.png" alt="">
				<h2><?php echo $UserAlbumName['name'].' Images'; ?></h2>
				<img class="dividerline" src="img/sep.png" alt="">
			</div>
			
			<div id="grid-gallery" class="grid-gallery">

				<section class="grid-wrap">
					<ul class="grid">
						<li class="grid-sizer"></li>
						<!-- for Masonry column width -->				
						<?php $i=0;
						foreach ($UserAlbumImages as $UserAlbumImage) {
							echo	'<li id="'.$i.'"><figure>';
							if (isset($UserAlbumImage['name'])){
								echo'<img  src="' . $UserAlbumImage["source"] . '" alt=""/>';
								echo '<figcaption><p>'.$UserAlbumImage['name'].'</p></figcaption>';
							}
							else{
								echo'<img  src="' . $UserAlbumImage["source"] . '" alt=""/>';
								echo '<figcaption><p></p></figcaption>';
							}
							echo '</li></figure>' ;$i++;} ?>
						</ul>
					</section><!-- // end small images -->
					<div class="container-fluid row">
						<div  class="col-sm-6">
						<?php if(isset($tempPrv)) { 
						echo '<a href="Pictures.php?albumid='.$_GET["albumid"].'&prev='.$prevURl.'" class="btn btn-primary">Prev</a>';}?>
						</div>
						<div class="col-sm-6 text-right">
						<?php if(isset($tempNext)) {	 ?>
							<a href="Pictures.php?albumid=<?php echo $_GET["albumid"]?>&next=<?php echo $nextURL ?>" class="btn btn-primary">Next</a>
							<?php } ?>
						</div>
					</div>

					<section class="slideshow">
						<ul>
							<?php  foreach ($UserAlbumImages as $UserAlbumImage) {
								echo '<li>'; ?>
								<figure>
									<?php
									if (isset($UserAlbumImage['name']))	{
										echo '<img src="' . $UserAlbumImage["source"] . '" alt=""/>';
										echo '<figcaption><p>'.$UserAlbumImage['name'].'</p></figcaption>';
									}
									else{
										echo '<img src="' . $UserAlbumImage["source"] . '" alt=""/>';
										echo '<figcaption><p></p></figcaption>';
									}?>
								</figure>
							</li>
							<?php }?>
						</ul>
						<nav>
							<span class="icon nav-prev"></span>
							<span id="next" class="icon nav-next"></span>
							<span id="close" class="icon nav-close" onclick="cancelled=true;"></span>
						</nav>
						<div class="info-keys icon">Navigate with arrow keys</div>
					</section><!-- // end slideshow -->
					
				</div><!-- // grid-gallery -->
			</div>
		</section>
		<?php include 'Footer.php' ?>

		<script type="text/javascript">
		var cancelled = false;
			//var first=count;
			function sleep(ms) {
				return new Promise(resolve => setTimeout(resolve, ms));
			}

			async function slideShow() {
				var count = <?php echo $i ?>;
				document.getElementById("0").click();
					//setInterval(slideShow, 3000);
					

					for (var i = 0; i < count ; i++) {
						if (cancelled) {
							return;
						} 

						console.log(i);
						await sleep(2000);
						document.getElementById('next').click();

					}
				}


			</script>

			<script src="js/jquery.js"></script>
			<script src="js/modernizr.js"></script>
			<script src="js/bootstrap.js"></script>
			<script src="js/menustick.js"></script>
			<script src="js/parallax.js"></script>
			<script src="js/easing.js"></script>
			<script src="js/wow.js"></script>
			<script src="js/smoothscroll.js"></script>
			<script src="js/masonry.js"></script>
			<script src="js/imgloaded.js"></script>
			<script src="js/classie.js"></script>
			<script src="js/colorfinder.js"></script>
			<script src="js/gridscroll.js"></script>
			<script src="js/contact.js"></script>
			<script src="js/common.js"></script>
			<script src="js/jquery.lazyload.js"></script>
			<script type="text/javascript">
				$(function() {
					$("img.lazy").lazyload();
				});
			</script>


		</body>
		</html>