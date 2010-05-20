function insertImageTag(imgURL, containsSignature) 
{
    var imageFullURL = imgURL;
    var imageTag = "";
    if (containsSignature == "true")
    {
        var st = imageFullURL.indexOf("?st=");
        if (st != -1)
        {
            imgURL = imageFullURL.substr(0, st); 
        }
    }

    var dot = imgURL.lastIndexOf(".");
    if (dot == -1) 
    {
        imageTag = '<a href="' + imgURL + '" >' + imgURL + '</a> ';
    }
    else 
    {
        var extension = imgURL.substr(dot, imgURL.length);
        switch (extension.toLowerCase())
        {
            case ".jpg":
            case ".jpeg":
            case ".gif":
            case ".bmp":
            case ".png":
            case ".tiff":
                imageTag = '<img src="' + imageFullURL + '" /> ';
                break;

            default:
                imageTag = '<a href="' + imageFullURL + '" >' + imageFullURL + '</a> ';
                break;   
        }
    }

    var win = window.dialogArguments || opener || parent || top;

    if (typeof win.send_to_editor == 'function') 
    {
        win.send_to_editor(imageTag);
        if (typeof win.tb_remove == 'function') 
        {
            win.tb_remove();
        }

        return false;
    }
    
    tinyMCE = win.tinyMCE;
    if (typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content'))
    {
        tinyMCE.selectedInstance.getWin().focus();
        tinyMCE.execCommand('mceInsertContent', false, imageTag);
    }
    else 
    {
        win.edInsertContent(win.edCanvas, imageTag);
    }

    return false;
}
