# Video uploader via Rest API
This is a PHP video uploader project in Yii2 framework.

## Requirements
- PHP version:
    Minimum: 5.6
    Recommended: 7.2
- Composer for install the project (If you don't have composer yet, you can download from: https://getcomposer.org)
- FFmpeg for upload and convert videos (If you don't have installed FFmpeg yet, visit: https://ffmpeg.org)

## Installation

### Clone
- Clone this repo to your local machine using `git clone https://github.com/gaborfarkas0211/video-uploader.git`

### Setup (on Windows)
- Install the framework and extensions with `composer install ` command
- Make a database called `videouploader`
- Run command `php yii migrate `

> You can skip the last two steps by import the SQL file from the repository

> Now you have some sample video with converted qualities.

## Usage
### API Url
The video uploader API URL is on your own server/video-uploader/web/api.
For example: http://127.0.0.1/video-uploader/web/api/.

### API Endpoints
This video uploader has three endpoints to manipulating videos
#### "/video": retrieves the video access point
  - Method: GET
  - Query parameters:
    - 'v': this is the video ID
    - 'quality': you can choose quality (360 or 720), if you don't add this param the default quality is 720
    
    For example: http://127.0.0.1/video-uploader/web/api/video?v=pCq08PgrZTK&quality=360
    > Note: The response is a video link of the selected quality
    
    > Note: If the video is under process or the video is not available in the selected quality the response is the default uploaded video
    
    > Note: If the video not found the response is error: 404 Not found
#### "/video/upload"
  - Method: POST
  - Body parameters:
    - In Postman
    
        Set under the Body tag the params to `form-data`
        
    The key have to `file` and the value have to a video file with `mp4 or webm` format
    
    For example: http://127.0.0.1/video-uploader/web/api/video/upload
    - In HTML
    
        Set the form `enctype` attribute to `"multipart/form-data"`
        
    Add an input tag inside the form and set the `type` and `name` attribute to `"file"`
    
    > Note: The response is the uploaded video's id
    
    > Note: If the video's type is invalid or the file is not video the response is error: 400 Bad request
    
    > Note: If the video could not be uploaded or saved the response is error: 500 Internal server error
#### "/video/delete": deletes the video in all qualities
  - Method: DELETE
  - Query parameter:
    - 'v': this is the video ID

    For example: http://127.0.0.1/video-uploader/web/api/video/delete?v=pCq08PgrZTK
    > Note: The response is 200 OK
    
    > Note: If the video not found the response is: 404 Not found

## Commands
### Video convert
You can convert the video to `360p or 720p`. 
- Open a command line
- Navigate to the `project` folder
- Run command: `php yii video/convert`
    > The requested param is a video id
    
    > For example `php yii video/convert JjfGDio4mnp`

## Author
* Gábor Farkas

## License
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
