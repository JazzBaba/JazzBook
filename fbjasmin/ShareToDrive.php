<?php

include "FacebookAPI.php";
include 'GoogleAPI.php';
include "DeleteFolder.php";

$GoogleAPIObject = new GoogleAPI();
$FacebookAPIObject = new FacebookAPI();


$Postdata = file_get_contents("php://input");
$Request = json_decode($Postdata);
$GoogleAPIObject->GoogleClient->setAccessToken($_SESSION["token"]);

$FacebookAPIObject->FacebookObject->setDefaultAccessToken($_SESSION["facebook_access_token"]);
function getRandomString($length = 10){
    $CharacterSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $CharacterSetLength = strlen($CharacterSet);
    $RandomString = '';
    for ($i = 0; $i < $length; $i++) {
        $RandomString .= $CharacterSet[rand(0, $CharacterSetLength - 1)];
    }
    return $RandomString;
}

date_default_timezone_set('UTC');
$RndmString="TempPic/UserPic/".getRandomString(26)."_".date("h-i");
mkdir($RndmString);
try {
    $UserProfileRequest = $FacebookAPIObject->FacebookObject->get('/me?fields=name');
    $UserProfile = $UserProfileRequest->getGraphNode()->asArray();
    $Service = new Google_Service_Drive($GoogleAPIObject->GoogleClient);

    $FolderId=$GoogleAPIObject->getFolderExistsCreate($Service,"facebook_".str_replace(" ", "_", $UserProfile['name'])."_album","");
    foreach ($Request->data as $key => $value) {
        $AlbumID=$value->useralbumid;
        $AlbumName=str_replace("+", " ", $value->useralbumname);
        //$UserAlbumImageResponse = $FacebookAPIObject->FacebookObject->get("/" . $AlbumID . "/photos?fields=source");
        //$UserAlbumImages = $UserAlbumImageResponse->getGraphEdge()->asArray();
           $UserAlbumImages = $FacebookAPIObject->getUserAlbumImages($AlbumID);
            $BKPUserAlbumImages=$UserAlbumImages;
           while($FacebookAPIObject->nextAlbum($BKPUserAlbumImages)!=null)
            {
                $Temp=$FacebookAPIObject->nextAlbum($BKPUserAlbumImages)->asArray();
                $UserAlbumImages=$UserAlbumImages->asArray();
                $UserAlbumImages=array_merge($UserAlbumImages,$Temp);
                $BKPUserAlbumImages=$FacebookAPIObject->nextAlbum($BKPUserAlbumImages);
            }
        $SubFolderId=$GoogleAPIObject->createSubFolder($Service,$FolderId,$AlbumName);
        foreach ($UserAlbumImages as $key => $value) {
            $Data=file_get_contents($value['source']);
            $fp = fopen($RndmString."/".$AlbumName.$key.".jpg","w");
                    if (!$fp) exit;
                    fwrite($fp, $Data);

            $Title=$AlbumName.$key;
            $FileName=$RndmString."/".$AlbumName.$key.".jpg";
            $MimeType=mime_content_type ( $FileName );
            $GoogleAPIObject->insertFile($Service, $Title,  $MimeType, $FileName, $SubFolderId);
        }
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    header("Location: ./");
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
?>