/**
 * Created by eWebify, LLC on 8/4/17.
 */


/**
 * Open the media upload widget (opens as a modal).
 */
function launchPhotoWidget(event) {
    event.preventDefault();

    /**
     * Cloudinary upload widget.
     *
     * Handles media submission to CDN and invokes ajax call to save uploaded media to database.
     */
    cloudinary.openUploadWidget(
        {
            cloud_name: 'dqjrisb8u',
            upload_preset: 'b99xborn',
            sources: ['local', 'camera', 'url', 'facebook', 'google_photos', 'dropbox', 'instagram'],
            max_files: 10,
            resources_type: 'photo',
            multiple: true,
            client_allowed_formats: ['png', 'jpg', 'gif']
        },
        function (error, result) {
            if (error === null) {
                //upload succeeded
                logMessage('info', '[UI]: Cloudinary upload successful. ' + result.length + ' item(s) were uploaded.');

                //save each photo to database
                for (var i = 0; i < result.length; i++) {
                    //get public id
                    var id = result[i]['public_id'];

                    var strParts = id.split('/');
                    var publicId = strParts[strParts.length - 1];

                    var photoUrl = result[i]['secure_url'];

                    //try to save the image to our database.
                    saveCompanyPhoto('saveCompanyPhoto', publicId, photoUrl);
                }

                showAlert('Great! We can use that to enhance your online presence.', 'success');

            } else {
                //log error occurrence
                //not much we can do to debug - perhaps verify config of openUploadWidget()
                //show cloudinary error to user
                logMessage('error', '[UI:] There is an error uploading an image to cloudinary.');
            }
        }
    );

}

function saveCompanyPhoto(url, publicId, photoUrl) {
    $.ajax({
        type: "POST",
        url: baseUrl + "api/media/" + url,
        data: {'publicId': publicId, "photoUrl": photoUrl},
        dataType: "json",
        success: function(data) {

            var message = '[UI]: Cloudinary image ' + publicId + ' saved to company profile.';
            logMessage('info', message);

            //Add image to display
            var accordion = $('#collapsePhotos2');
            var con = $('#collapsePhotos2 .panel-body div:first').clone(true);

            $(con).find('a').attr('href', photoUrl);
            $(con).find('img').attr('src', photoUrl);
            $(accordion).append(con);
            $(con).show();

            $('#collapsePhotos2 .panel-body').find('p').hide();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //TODO - send notification to admin
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);
            logMessage('error', '[UI]: AJAX error in saveCompanyPhoto.');
        }
    });

    logMessage('info', 'Function saveCompanyPhoto complete in cloudinary-widget.js');
}

/**
 *  OnClick handler for photos.
 */
/**
 * TODO: The accordions have numbers, so we need a way of filtering this out so that
 *  rearranging accordions does not cause issues, and we can use the same function
 *  in multiple places - find a better selector, needs to "know" profile or project
 */
$('#collapsePhotos2').find('.image-container>span').on('click', function() {
    var photoUrl = $(this).parent().find('a').attr('href');
    var imageCon = $(this).parent();

    $.ajax({
        type: "POST",
        url: baseUrl + "api/media/deleteCompanyPhoto",
        data: {"photoUrl": photoUrl},
        dataType: "json",
        success: function(data) {
            logMessage('info', '[UI]: Image ' + photoUrl + ' deleted.');

            //Remove image from page
            $(imageCon).remove();

            showAlert('We have deleted that media from our system.', 'success');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //TODO - send notification to admin
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);
            logMessage('error', 'AJAX error in delete photo click handler.');
        }
    });

});


/**
 * Launch video upload widget.
 *
 * @param element
 * @param event
 */
function launchVideoWidget(event) {
    event.preventDefault();

    /**
     * Cloudinary upload widget.
     *
     * Handles media submission to CDN and invokes ajax call to save uploaded media to database.
     */
    cloudinary.openUploadWidget(
        {
            cloud_name: 'dqjrisb8u',
            upload_preset: 'b99xborn',
            sources: ['local', 'url', 'facebook', 'google_photos', 'dropbox', 'instagram'],
            max_files: 1,
            resources_type: 'video',
            multiple: false,
            client_allowed_formats: ['mp4', 'mov']
        },
        function (error, result) {
            if (error === null) {
                //upload succeeded
                logMessage('info', '[UI]: Cloudinary upload successful. ' + result.length + ' item(s) were uploaded.');

                //save each photo to database
                for (var i = 0; i < result.length; i++) {
                    //get public id
                    var id = result[i]['public_id'];

                    var strParts = id.split('/');
                    var publicId = strParts[strParts.length - 1];

                    var videoUrl = result[i]['secure_url'];

                    //try to save the image to our database.
                    saveCompanyVideo(publicId, videoUrl);
                }

                showAlert('Great! We can use that to enhance your online presence.', 'success');


            } else {
                //log error occurrence
                //not much we can do to debug - perhaps verify config of openUploadWidget()
                //show cloudinary error to user
                logMessage('error', '[UI:] There is an error uploading an image to cloudinary.');
                showAlert('Unfortunately that didn\'t work.', 'danger');
            }
        }
    );

}


/**
 * AJAX to save an uploaded video to a company profile.
 * @param publicId
 * @param videoUrl
 */
function saveCompanyVideo(publicId, videoUrl) {
    $.ajax({
        type: "POST",
        url: baseUrl + "api/media/" + 'saveCompanyVideo',
        data: {'publicId': publicId, "videoUrl": videoUrl},
        dataType: "json",
        success: function(data) {

            //log and show user success alert
            logMessage('info', '[UI]: Cloudinary video ' + publicId + ' saved to company profile.');

            //Add image to display
            var accordion = $('#collapseVideos3');
            var con = $('#collapseVideos3 .panel-body div:first').clone(true);

            $(con).find('a').attr('href', videoUrl);

            var thumb = videoUrl.substr(0, videoUrl.length - 4) + '.jpg';

            $(con).find('img').attr('src', thumb);
            $(accordion).append(con);
            $(con).show();

            $('#collapseVideos3 .panel-body').find('p').hide();

        },
        error: function(jqXHR, textStatus, errorThrown) {
            //TODO - send notification to admin
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);
            //log and show user danger alert
            logMessage('error', '[UI]: AJAX error in saveCompanyVideo: ' + errort);
            showAlert('Video could not be saved. <a href="mailto:app@ewebify.com">Report</a>', 'danger', 10000);
        }
    });

    logMessage('info', '[UI]: Function saveCompanyVideo complete in cloudinary-widget.js');
}

/**
 *  Handler for delete company profile video
 */
$('#collapseVideos3').find('.image-container>span').on('click', function() {
    var videoUrl = $(this).parent().find('a').attr('href');
    var imageCon = $(this).parent();

    $.ajax({
        type: "POST",
        url: baseUrl + "api/media/deleteCompanyVideo",
        data: {"videoUrl": videoUrl},
        dataType: "json",
        success: function(data) {
            logMessage('info', '[UI]: Video ' + videoUrl + ' deleted.');

            //Remove image from page
            $(imageCon).remove();

            showAlert('We have deleted that media from our system.', 'success');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //TODO - send notification to admin
            console.log(errorThrown);
            console.log(textStatus);
            console.log(jqXHR);
            logMessage('error', '[UI]: AJAX error in delete photo click handler.');
            showAlert('Video could not be deleted. <a href="mailto:app@ewebify.com">Report</a>', 'danger', 10000);
        }
    });

});