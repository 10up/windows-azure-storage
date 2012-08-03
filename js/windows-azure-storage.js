function onUpload_ContainerSelectionChanged()
{
    var htmlForm = document.getElementsByName("UploadNewFileForm")[0];
    if (htmlForm.elements["selected_container"].value === "<Create New Container>") {
        htmlForm.elements["uploadFileTag"].disabled = true;
        htmlForm.elements["uploadFileTag"].style.background="gray";
        htmlForm.elements["uploadFileName"].disabled = true;
        htmlForm.elements["uploadFileName"].style.background="gray";
        htmlForm.elements["createContainer"].style.visibility="visible";
        document.getElementById('lblNewContainer').style.display = "block";
        //document.getElementsByName("createContainer")[0].style.display = "block";
        htmlForm.elements["action"].value = 'Create';
        htmlForm.elements["submit"].value='Create';
    } else {
        htmlForm.elements["uploadFileTag"].disabled = false;
        htmlForm.elements["uploadFileTag"].style.background="white";
        htmlForm.elements["uploadFileName"].disabled = false;
        htmlForm.elements["uploadFileName"].style.background="white";
        htmlForm.elements["createContainer"].style.visibility="hidden";
        document.getElementById('lblNewContainer').style.display = "none";
        //document.getElementsByName("createContainer")[0].style.display = "none";
        htmlForm.elements["action"].value = 'Upload';
        htmlForm.elements["submit"].value='Upload';
    }
}

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
