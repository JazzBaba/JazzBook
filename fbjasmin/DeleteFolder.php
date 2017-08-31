<?php 
$Dir="TempPic/UserPic/";
if (is_dir($Dir)) {
    if ($dh = opendir($Dir)) {
        while (($Folder = readdir($dh)) !== false) {
        	if($Folder!="."&& $Folder!=".."){
        		$Intime=strtotime(str_replace("-", ":",explode("_",$Folder)[1]));
        		$AddMinutesIntime=date("h:i",strtotime("+5 minutes",$Intime));
        		if($AddMinutesIntime<=date("h:i")){
        			Delete($Dir.$Folder);
        		}
        	}
        }
        closedir($dh);
    }
}
function Delete($Path)
{
    if (is_dir($Path) === true)
    {
        $Folders = array_diff(scandir($Path), array('.', '..'));
        foreach ($Folders as $Folder)
        {
            Delete(realpath($Path) . '/' . $Folder);
        }
        rmdir($Path);
    }
    else if (is_file($Path) === true)
    {
        unlink($Path);
    }
}
?>