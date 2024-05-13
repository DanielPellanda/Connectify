
// Shows the preview image in the "Add new post" form
function CheckAttachment(idimg, idboxprev, idboxadd, idboxrm, idimgprev) {
    let previewForm = document.getElementById(idimg);
    let previewBox = document.getElementById(idboxprev);
    let formBox = document.getElementById(idboxadd);
    let rmBox = document.getElementById(idboxrm);
    let imgBox = document.getElementById(idimgprev);

    if (previewForm != null) {
        if ('files' in previewForm) {
            if (previewForm.files.length > 0) {
                let fileUploaded = previewForm.files[0] ;
                if (fileUploaded != null) {
                    imgBox.setAttribute('src', URL.createObjectURL(fileUploaded));
                    previewBox.removeAttribute('hidden');
                    formBox.setAttribute('hidden', true);
                    rmBox.removeAttribute('hidden');
                    return;
                }
            }
        }
    }
    imgBox.setAttribute('src', '#');
    previewBox.setAttribute('hidden', true);
    formBox.removeAttribute('hidden');
    rmBox.setAttribute('hidden', true);
}

function RemoveAttachedImage(idimg, idboxprev, idboxadd, idboxrm, idimgprev) {
    let previewForm = document.getElementById(idimg);
    previewForm.value = '';
    CheckAttachment(idimg, idboxprev, idboxadd, idboxrm, idimgprev);
}