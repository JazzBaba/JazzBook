<?php

require_once 'FacebookAPI.php';
include "DeleteFolder.php";


$FacebookAPIObject = new FacebookAPI();

function getRandomString($length = 10){
    $CharacterSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $CharacterSetLength = strlen($CharacterSet);
    $RandomString = '';
    for ($i = 0; $i < $length; $i++) {
        $RandomString .= $CharacterSet[rand(0, $CharacterSetLength - 1)];
    }
    return $RandomString;
}

$PostData = file_get_contents("php://input");
$AlbumRequest = json_decode($PostData);

$FacebookAPIObject->FacebookObject->setDefaultAccessToken($_SESSION["facebook_access_token"]);

$zip = new ZipArchive;
date_default_timezone_set('UTC');
$RndmString="TempPic/UserPic/".getRandomString(26)."_".date("h-i");
mkdir($RndmString);
if ($zip->open($RndmString . '/Album.zip', ZipArchive::CREATE) === TRUE) {
    try {
        foreach ($AlbumRequest->data as $key => $Value) {
            $albumID = $Value->useralbumid;
            $AlbumName = str_replace("+", " ", $Value->useralbumname);
            $UserAlbumImages = $FacebookAPIObject->getUserAlbumImages($albumID);
            $BKPUserAlbumImages=$UserAlbumImages;
           while($FacebookAPIObject->nextAlbum($BKPUserAlbumImages)!=null)
            {
                $Temp=$FacebookAPIObject->nextAlbum($BKPUserAlbumImages)->asArray();
                $UserAlbumImages=$UserAlbumImages->asArray();
                $UserAlbumImages=array_merge($UserAlbumImages,$Temp);
                $BKPUserAlbumImages=$FacebookAPIObject->nextAlbum($BKPUserAlbumImages);
            }
            foreach ($UserAlbumImages as $key => $Value) {
                $Data = file_get_contents($Value['source']);
                $fp = fopen($RndmString . "/" . $AlbumName . $key . ".jpg", "w");
                     if (!$fp) exit;
                     fwrite($fp, $Data);
                $FileName = $RndmString . "/" . $AlbumName . $key . ".jpg";
                $path = $AlbumName . '/' . $key . '.jpg';
                $zip->addFile($FileName, $path);
            }

            
        }
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        // redirecting user back to app login page
        header("Location: ./");
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other lqocal issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $zip->close();
}
echo $RndmString . "/Album.zip";

?>