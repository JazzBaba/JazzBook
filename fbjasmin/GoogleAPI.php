<?php
include_once 'lib/google-api-php-client/src/Google/Client.php';
include_once 'lib/google-api-php-client/src/Google/Auth/OAuth2.php';

class GoogleAPI
{

    private $ClientId = '99526189653-i54unaujh37nggti9c4105n7d3ak4vrj.apps.googleusercontent.com'; //Google client ID
    private $ClientSecret = 'l3qx1R6WEZyUkqqgKTVYXSQF'; //Google client secret
    private $RedirectURL = 'https://jazzbook.herokuapp.com/Album.php'; //Callback URL
    private $Scope = array(
        'https://www.googleapis.com/auth/drive.file',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile');
        var $GoogleClient = "";
    function __construct()
    {   
        $this->GoogleClient = new Google_Client();
        $this->GoogleClient->setApplicationName('JazzBook');
        $this->GoogleClient->setClientId($this->ClientId);
        $this->GoogleClient->setRedirectUri($this->RedirectURL);
        $this->GoogleClient->setClientSecret($this->ClientSecret);
        $this->GoogleClient->setAccessType('offline');
        $this->GoogleClient->setScopes($this->Scope);
    }

    function getUserInfo()
    {
        return (new Google_Service_Oauth2($this->GoogleClient))->userinfo->get();
    }

    function auth($CredentialsCode)
    {
        try {
            $this->GoogleClient->authenticate($CredentialsCode);
            $_SESSION['token'] = $this->GoogleClient->getAccessToken();
            header('Location: ./');
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }
    }
    function isAuth()
    {
        if (isset($_SESSION['token'])) {
            $this->GoogleClient->setAccessToken($_SESSION['token']);
        }
        if ($this->GoogleClient->getAccessToken()) {
            return true;
        } else {
            return false;   
        }
    }

    function createSubFolder($Service, $FolderId, $FolderName)
    {
        $Files = $Service->files->listFiles(array('q' => "'$FolderId' in parents"));
        $isFound = false;
        foreach ($Files['items'] as $Item) {
            if ($Item['title'] == $FolderName) {
                $isFound = true;
                return $Item['id'];
                break;
            }
        }
        if (!$isFound) {
            $SubFolder = new Google_Service_Drive_DriveFile();
            $SubFolder->setTitle($FolderName);
            $SubFolder->setMimeType('application/vnd.google-apps.folder');
            $Parent = new Google_Service_Drive_ParentReference();
            $Parent->setId($FolderId);
            $SubFolder->setParents(array($Parent));
            try {
                $SubFolderMeataData = $Service->files->insert($SubFolder, array(
                    'mimeType' => 'application/vnd.google-apps.folder',
                ));
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
            return $SubFolderMeataData->id;
        }
    }

    function getFolderExistsCreate($Service, $FolderName, $FolderDesc)
    {
        // List all user files (and folders) at Drive root
        $Files = $Service->files->listFiles(array('q' => "trashed=false"));
        $isFound = false;

        // Go through each one to see if there is already a folder with the specified name
        foreach ($Files['items'] as $Item) {
            if ($Item['title'] == $FolderName) {
                $isFound = true;
                return $Item['id'];
                break;
            }
        }
        // If not, create one
        if ($isFound == false) {
            $Folder = new Google_Service_Drive_DriveFile();
            //Setup the folder to create
            $Folder->setTitle($FolderName);
            if (!empty($FolderDesc))
                $Folder->setDescription($FolderDesc);
            $Folder->setMimeType('application/vnd.google-apps.folder');
            //Create the Folder
            try {
                $CreatedFile = $Service->files->insert($Folder, array(
                    'mimeType' => 'application/vnd.google-apps.folder',
                ));
                // Return the created folder's id
                return $CreatedFile->id;
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
            }
        }
    }

    function insertFile($Service, $Title, $MimeType, $FileName, $FolderID)
    {
        $File = new Google_Service_Drive_DriveFile();

        // Set the metadata
        $File->setTitle($Title);
        $File->setDescription("");
        $File->setMimeType($MimeType);

        // Setup the folder you want the file in, if it is wanted in a folder
        $Parent = new Google_Service_Drive_ParentReference();
        $Parent->setId($FolderID);
        $File->setParents(array($Parent));
        try {
            // Get the contents of the file uploaded
            $Data = file_get_contents($FileName);

            // Try to upload the file, you can add the parameters e.g. if you want to convert a .doc to editable google format, add 'convert' = 'true'
            $CreatedFile = $Service->files->insert($File, array(
                'data' => $Data,
                'mimeType' => $MimeType,
                'uploadType' => 'multipart'
            ));
            // Return a bunch of data including the link to the file we just uploaded
            //return $CreatedFile;
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }
}