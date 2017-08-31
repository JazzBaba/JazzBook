<?php
require_once "FacebookAPI.php";
include 'GoogleAPI.php';
require_once 'lib/google-api-php-client/src/Google/Service/Drive.php';

$GoogleAPIObject = new GoogleAPI();
$FacebookAPIObject = new FacebookAPI();

if (isset($_GET['code'])) {
	$GoogleAPIObject->auth($_GET['code']);
}

if ($GoogleAPIObject->isAuth()) {
	$GoogleUserProfile = $GoogleAPIObject->getUserInfo();
} else {
	$AuthUrl = $GoogleAPIObject->GoogleClient->createAuthUrl();
}

if (isset($_SESSION['facebook_access_token'])) {
	$FacebookAPIObject->FacebookObject->setDefaultAccessToken($_SESSION['facebook_access_token']);
		$Profile = $FacebookAPIObject->getUserInfo();
		$UserAlbums = $FacebookAPIObject->getUserAlbums($Profile['id']);
		$AlbumJSON=json_encode($UserAlbums);
} else {
	header("location:index.php");
}

?>
<!DOCTYPE html>

<html  lang="en" >
<!--<![endif]-->
<head>

	<meta charset="UTF-8"/>

	<title><?php echo $Profile['name'];?> | Albums</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/animate.css">
	<link rel="stylesheet" href="css/theme.css">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link href="css/HoldOn.css" rel="stylesheet" type="text/css">
	<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Playball' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js"></script>
</head>
<body ng-app="JazzBook">
	<!--wrapper start-->
	<div class="wrapper" id="wrapper" ng-controller="albumController">

		<!--header-->
		<header>
			<div class="banner row" id="banner">		
				<?php echo '<div class="parallax text-center" style="background-image: url('.$Profile["cover"]["source"].'
				);">';?>
				<div class="parallax-pattern-overlay">
					<div class="container text-center" style="height:600px;padding-top:170px;">
						<a href="#"><img id="site-title" style="border-radius: 50%" class=" wow fadeInDown" wow-data-delay="0.0s" wow-data-duration="0.9s" src="<?php echo $Profile['picture']['url'];?>" alt="logo"/></a>
						<h2 class="intro wow zoomIn" wow-data-delay="0.4s" wow-data-duration="0.9s">Welcome  <?php echo $Profile['name'];?></h2>
					</div>
				</div>
				<?php echo '</div>' ?>
			</div>
				<div class="navbar-wrapper stuckMenu">
					<div class="container">
						<div class="navwrapper">
							<div class="navbar navbar-inverse navbar-static-top">
								<div class="container">
									<div class="navArea">
										<div class="navbar-collapse collapse">
											<ul class="nav">
												<li class="menuItem text-center"><a href="logout.php?session=facebook_access_token">Logout</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</header>
		<!--specialties-->

			<section class="specialties" id="specialties">
				<div class="container">

					<div class="btn-group">
						<?php if (!isset($_SESSION['token'])) {
							echo '<button ng-click="googleAuth(\''.$AuthUrl.'\')" class="btn btn-danger loginBtn text-nowrap loginBtn-google text-md-center text-lg-right text-sm-left" name="loginBtn">
							Login with Google
						</button>';
					} else {
						echo '<div class="dropdown">
						<button class="btn btn-danger dropdown-toggle"
						style="color: #3b5998;font-family: \'Roboto Condensed\', sans-serif;background-color: transparent"
						type="button"
						id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false">
						<img src="'.$GoogleUserProfile['picture'].'" class="img text-center" height="40px" width="40px" style="margin-right: 5px"/>
						' . $GoogleUserProfile['name'] . '
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<a class="dropdown-item text-center" href="logout.php?session=token"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
					</div>
				</div>';
			}
			?>
				
			</div>
			<div class="heading text-center">
				<img class="dividerline" src="img/sep.png" alt="">
				<h2>Your Albums</h2>
				<img class="dividerline" src="img/sep.png" alt="">
			</div>
			<div class="download_multiple" style="text-align: center;" >
				<button class="btn btn-primary" ng-click="downloadSelectedAlbum()" id="downloadmultiple" disabled>
					Download <span class="badge badge-info">{{AlbumSelected}}</span> album
					<span class="sr-only">unread messages</span>
				</button>
					<!-- </div>
					<div class="download_multiple" style="margin-left:43%;" > -->
						<?php echo '<button class="btn btn-primary" ng-click="downloadAllAlbum()">Download all <i class="fa fa-download" aria-hidden="true"></i>
					</button>'?>

					<?php if(isset($_SESSION['token'])){
						echo '<button class="btn btn-danger" ng-click="shareSelectedAlbum()" id="sharemultiple"
						ng-disabled="isShareAllow">
						Share <span class="badge badge-info">{{AlbumSelected}}</span> album to drive
						<span class="sr-only">unread messages</span>
					</button>';
				}else{  echo '<button class="btn btn-danger"
				disabled>
				Share <span class="badge badge-info">{{AlbumSelected}}</span> album to drive
				<span class="sr-only">unread messages</span>
			</button>';}?>

			<?php echo '<button class="btn btn-danger" ng-click="shareAllAlbum()"';
			if (!isset($_SESSION['token'])) {
				echo 'disabled';
			}
			echo '>Share all to drive
		</button>' ?>
	</div>

	<div class="row">

		<div class="restmenuwrap">
			<?php
			$i = 0;
			foreach ($UserAlbums as $UserAlbum) {
				?>
				<div class="col-md-4">
					<div class="restitem clearfix">
						<?php echo	'<a style="margin:auto;" href="http://localhost/fbjasmin/Pictures.php?albumid=' . $UserAlbum['id'] . '" class="album_title">
						<div class="rm-thumb" style="background-image: url('. $UserAlbum['picture']['url'].');"></div></a>';?>

						<div class="container-fluid text-center">
							<div class="album_select_download">
								<?php echo '<h2><label>'.$UserAlbum['name'].'</label></h2>';?>

							</div>
							<div class="album_select_download">
								<?php echo '<label for="' . $UserAlbum['id'] . '" class="btn btn-primary">Select ';
								echo '<input type="checkbox" name="' . $UserAlbum['name'] . '" id="' . $UserAlbum['id'] . '" ng-model="isAlbum[' . $i . ']" ng-true-value="true" ng-false-value="false" ng-change="addAlbum(' . $i . ',\'' . $UserAlbum['name'] . '\',' . $UserAlbum['id'] . ')"/>';
								echo '</label>' ?>

								<?php echo '<button style="cursor:pointer;" class="btn btn-primary" ng-click="singleDownload(\'' . $UserAlbum["name"] . '\',' . $UserAlbum["id"] . ')">';
								echo '<i class="fa fa-download" aria-hidden="true"></i></button>'; ?>

								<?php echo '<button class="btn btn-danger" ng-click="singleShare(\'' . $UserAlbum["name"] . '\',' . $UserAlbum["id"] . ')"';
								if(!isset($_SESSION['token']))
									{echo'disabled  style="cursor:not-allowed;"';}
								echo ' style="cursor:pointer;" ><i class="fa fa-google" aria-hidden="true"></i></button>'; ?>
							</div>
						</div>

					</div>

				</div>
				<?php $i++;
			}
			?>
		</div>
	</div>
</div>

</section>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Ready to download</h5>
		</div>
		<div class="modal-body">
			<h6 id="filename" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"></h6>
		</div>
		<div class="modal-footer">
			<!--<button ng-click="deletefolder(file.filename)" ng-model="file.filename"  class="btn btn-secondary" data-dismiss="modal">Cancle</button>-->
			<button class="btn btn-secondary" data-dismiss="modal">Cancle</button>
			<input type="hidden" id="donwloadfoldername">
			<button onclick="downloadFolder()" type="button"
					class="btn btn-primary">Ready to go
				</button>
	</div>
</div>
</div>
</div>

<div class="modal fade" id="gshareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel1">Success to uploaded</h5>
		</div>
		<div class="modal-body">
			<h6 id="infotouser" style="color: #3b5998;font-family: 'Roboto Condensed', sans-serif;"></h6>
		</div>
		<div class="modal-footer">
			<button class="btn btn-secondary" data-dismiss="modal">Ok</button>
		</div>
	</div>
</div>
</div>
<?php include 'Footer.php' ?>
</div>









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
<script src="js/contact.js"></script>
<script src="js/common.js"></script>
<script src="js/HoldOn.js"></script>



<script type="text/javascript">
function downloadFolder(){
window.location=document.getElementById("donwloadfoldername").value;
}
	angular.module("JazzBook", []).controller("albumController", function ($window, $scope, $http) {
		$scope.SelectedAlbum = [];
		$scope.file = {};
		$scope.file.filename=null;
		$scope.file.folder=null;
		$scope.isAlbum = [];
		$scope.AlbumSelected = 0;
		$scope.isShareAllow=true;
		$scope.AllAlbumJSON=$window.AlbumJSON;
		$scope.addAlbum = function (id, albumname, albumid) {
			if ($scope.isAlbum[id] == true) {
				$scope.SelectedAlbum.push({"useralbumid": albumid + "", "useralbumname": albumname});
				$scope.AlbumSelected += 1;
			} else {
				for (i = 0; i < $scope.SelectedAlbum.length; i++) {
					if ($scope.SelectedAlbum[i].useralbumid == albumid) {
						$scope.SelectedAlbum.splice(i, 1);
					}
				}
				$scope.AlbumSelected -= 1;
			}
			if ($scope.AlbumSelected > 0) {
				$scope.isShareAllow=false;
				document.getElementById("downloadmultiple").disabled = false;
			}
			else {
				$scope.isShareAllow=true;
				document.getElementById("downloadmultiple").disabled = true;
			}
		};
		$scope.singleDownload = function (albumname, albumid) {
			$scope.downloadAlbum({data: [{"useralbumid": albumid + "", "useralbumname": albumname}]});
		};
		$scope.downloadSelectedAlbum = function () {
			$scope.downloadAlbum({data: $scope.SelectedAlbum});
		};
		$scope.downloadAllAlbum = function () {
			var AllAlbumJSON= <?php print_r($AlbumJSON); ?>;
			for(var i in AllAlbumJSON){
				$scope.SelectedAlbum.push({"useralbumid": AllAlbumJSON[i].id+ "", "useralbumname": AllAlbumJSON[i].name});
			}
			$scope.downloadAlbum({data: $scope.SelectedAlbum});
		};
		$scope.downloadFolder = function (foldername) {
			$window.location = foldername;
		};

		//google share functions
		$scope.singleShare = function (albumname, albumid) {
			$scope.shareAlbum({data: [{"useralbumid": albumid + "", "useralbumname": albumname}]});
		};
		$scope.shareSelectedAlbum = function () {
			$scope.shareAlbum({data: $scope.SelectedAlbum});
		};
		$scope.shareAllAlbum = function () {

			for (var i in $scope.AllAlbumJSON) {
				$scope.SelectedAlbum.push({
					"useralbumid": $scope.AllAlbumJSON[i]['id'] + "",
					"useralbumname": $scope.AllAlbumJSON[i]['name']
				});
			}
			$scope.shareAlbum({data: $scope.SelectedAlbum});
		};
		$scope.shareAlbum = function (data) {
			HoldOn.open({
				theme: 'sk-rect',
				message: "<h4>" + " Hi your album's are flying in Google Drive</h4>"
			});
			$http({
				method: "post", url: "ShareToDrive.php", data: data,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (result) {
				console.log(result);    
				HoldOn.close();
				document.getElementById("infotouser").innerHTML = "WoW Your google drive is field with Facebook Pictures";
				$('#gshareModal').modal('show');
			}, function (reason) {
			});
		};
     $scope.downloadAlbum = function (data) {
     	HoldOn.open({
     		theme: 'sk-rect',
     		message: "<h4>" + " Preparing your zip file</h4>"
     	});
     	$http({
     		method: "post", url: "Download.php", data: data,
     		headers: {'Content-Type': 'application/x-www-form-urlencoded'}
     	}).then(function (result) {
     		HoldOn.close();
     		console.log(result);
     		$scope.file.filename = result.data.split("/")[3].toString();
     		$scope.file.folder = result.data;
     		document.getElementById("donwloadfoldername").value=result.data;
     		document.getElementById("filename").innerHTML = result.data.split("/")[3];
     		$('#exampleModal').modal('show');
     	}, function (reason) {
     	});
     };

     // google Authentication 
     $scope.googleAuth = function (url) {
     	$window.location = url;
     };
 });
</script>

</body>
</html>