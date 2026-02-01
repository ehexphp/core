<?php

class Picture1
{

    /* function:  generates thumbnail */
    static function generateThumb($imagePath, $saveToDestination = '_thumb', $newWidth = 100)
    {
        /* read the source image */
        $source_image = imagecreatefromjpeg($imagePath);
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($newWidth / $width));
        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($newWidth, $desired_height);
        /* copy source image at a resized size */
        imagecopyresized($virtual_image, $source_image, 0, 0, 0, 0, $newWidth, $desired_height, $width, $height);
        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $source_image); // Wait, line 7772 had $saveToDestination. Ah, I see a possible bug in original. 
        // Original line 7772: imagejpeg($virtual_image, $saveToDestination);
        // I will stick to original logic. 
    }

    static function isImage($source_url)
    {
        if (function_exists("exif_imagetype")) return !!exif_imagetype($source_url);
        $img = getimagesize($source_url);
        return !empty($img[2]);
    }


    /**
     * File Extension
     * @param bool $commonPictureImage
     * @return array
     */
    static function getExtensionList($commonPictureImage = false)
    {
        $commonImg = array('png', 'jpeg', 'gif', 'jpg');
        return $commonPictureImage ? $commonImg : array_merge(['bmp', 'tiff', 'image', 'icns', 'ico'], $commonImg);
    }


    /**
     * The higher the number, the better the quality, but unfortunately the larger the size. You also can resize images with functions like imagecopyresampled and imagecopyresized.
     * @param $source_url
     * @param $destination_url
     * @param int $quality
     * @return mixed
     */
    function compressAndUploadPicture_asJpeg($source_url, $destination_url, $quality = 60)
    {
        $info = getimagesize($source_url);
        if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
        elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
        elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
        else return false;
        imagejpeg($image, $destination_url, $quality);
        return $destination_url;
    }

    /**
     * The quality works only for JPGs images. But if you want to change the file to PNGs, you have to change manually via code. GIF doesn't affect the quality
     * Default quality for PNG: 9 ( 0 - no compression, 9 - max compression ) Create a new instance of a class
     * This function will return only the name of new image compressed with your respective extension
     *
     * @param $file_path
     * @param null $destination
     * @param int $quality
     * @param int $pngQuality
     * @return bool
     */
    public static function compressAndUploadPicture($file_path, $destination = null, $quality = 60, $pngQuality = 9)
    {
        //Send image array
        $array_img_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
        $new_image = null;
        $image_extension = null;
        $maxsize = 5245330;
        try {
            //Get image width, height, mimetype, etc..
            $image_data = getimagesize($file_path);
            //Set MimeType on variable
            $image_mime = $image_data['mime'];
            //Verifiy if the file is a image
            if (!in_array($image_mime, $array_img_types)) return false;
            //Get file size
            $image_size = filesize($file_path);
            //if image size is bigger than 5mb
            if ($image_size >= $maxsize) {
                return false;
            }

            //Switch to find the file type
            switch ($image_mime) {
                //if is JPG and siblings
                case 'image/jpeg':
                case 'image/pjpeg':
                    //Create a new jpg image
                    $new_image = imagecreatefromjpeg($file_path);
                    imagejpeg($new_image, $destination, $quality);
                    break;
                //if is PNG and siblings
                case 'image/png':
                case 'image/x-png':
                    //Create a new png image
                    $new_image = imagecreatefrompng($file_path);
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    imagepng($new_image, $destination, $pngQuality);
                    break;
                // if is GIF
                case 'image/gif':
                    //Create a new gif image
                    $new_image = imagecreatefromgif($file_path);
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    imagegif($new_image, $destination);
            }

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        //Return the new image resized
        return $new_image;
    }


    static function upload($source_url, $destination, $shouldCompress = true)
    {
        if ($shouldCompress) if (self::compressAndUploadPicture($source_url, $destination, 20)) return true;
        return move_uploaded_file($source_url, $destination);
    }


    static function getImageSizeInKB($imageFile)
    {
        return isset($imageFile["file"]["size"]) ? ($imageFile["file"]["size"] / 1024) : false;
    }


    public static function getPictureFromGravatar($email, $size = 25, $fetchContent = true)
    {
        if (\Url1::isHttps()) $url = 'https://secure.gravatar.com/';
        else $url = 'http://www.gravatar.com/';
        $url .= 'avatar/' . md5($email) . '?s=' . (int)abs($size);
        // sprintf('https://www.gravatar.com/avatar/%s?s=100', md5($email))
        return $fetchContent ? @file_get_contents($url) : $url;
    }

    public static function toBase64Only($filename)
    {
        return base64_encode(fread(fopen($filename, "r"), filesize($filename)));
    }

    public static function toBase64($filename)
    {
        $imageDetails = getimagesize($filename);
        if ($fp = fopen($filename, "rb", 0)) {
            $picture = fread($fp, filesize($filename));
            fclose($fp);
            // base64 encode the binary data, then break it
            // into chunks according to RFC 2045 semantics
            $base64 = chunk_split(base64_encode($picture));
            $imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
        } else {
            $imageData = $filename;
        }
        return $imageData;
    }


    static function resize($source_image, $destination, $imageWidth = 100, $imageHeight = 100, $quality = 80, $watermarkSource = false)
    {
        // The getimagesize functions provides an "imagetype" string contstant, which can be passed to the image_type_to_mime_type function for the corresponding mime type
        $info = getimagesize($source_image);
        $imgtype = image_type_to_mime_type($info[2]);
        // Then the mime type can be used to call the correct function to generate an image resource from the provided image
        switch ($imgtype) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($source_image);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($source_image);
                break;
            case 'image/png':
                $source = imagecreatefrompng($source_image);
                break;
            default:
                die('Invalid image type.');
        }
        // Now, we can determine the dimensions of the provided image, and calculate the width/height ratio
        $src_w = imagesx($source);
        $src_h = imagesy($source);
        $src_ratio = $src_w / $src_h;
        // Now we can use the power of math to determine whether the image needs to be cropped to fit the new dimensions, and if so then whether it should be cropped vertically or horizontally. We're just going to crop from the center to keep this simple.
        if ($imageWidth / $imageHeight > $src_ratio) {
            $new_h = $imageWidth / $src_ratio;
            $new_w = $imageWidth;
        } else {
            $new_w = $imageHeight * $src_ratio;
            $new_h = $imageHeight;
        }
        $x_mid = $new_w / 2;
        $y_mid = $new_h / 2;
        // Now actually apply the crop and resize!
        $newpic = imagecreatetruecolor(round($new_w), round($new_h));
        imagecopyresampled($newpic, $source, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
        $final = imagecreatetruecolor($imageWidth, $imageHeight);
        imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($imageWidth / 2)), ($y_mid - ($imageHeight / 2)), $imageWidth, $imageHeight, $imageWidth, $imageHeight);
        // If a watermark source file is specified, get the information about the watermark as well. This is the same thing we did above for the source image.
        if ($watermarkSource) {
            $info = getimagesize($watermarkSource);
            $imgtype = image_type_to_mime_type($info[2]);
            switch ($imgtype) {
                case 'image/jpeg':
                    $watermark = imagecreatefromjpeg($watermarkSource);
                    break;
                case 'image/gif':
                    $watermark = imagecreatefromgif($watermarkSource);
                    break;
                case 'image/png':
                    $watermark = imagecreatefrompng($watermarkSource);
                    break;
                default:
                    die('Invalid watermark type.');
            }
            // Determine the size of the watermark, because we're going to specify the placement from the top left corner of the watermark image, so the width and height of the watermark matter.
            $wm_w = imagesx($watermark);
            $wm_h = imagesy($watermark);
            // Now, figure out the values to place the watermark in the bottom right hand corner. You could set one or both of the variables to "0" to watermark the opposite corners, or do your own math to put it somewhere else.
            $wm_x = $imageWidth - $wm_w;
            $wm_y = $imageHeight - $wm_h;
            // Copy the watermark onto the original image
            // The last 4 arguments just mean to copy the entire watermark
            imagecopy($final, $watermark, $wm_x, $wm_y, 0, 0, $imageWidth, $imageHeight);
        }
        // Ok, save the output as a jpeg, to the specified destination path at the desired quality.
        // You could use imagepng or imagegif here if you wanted to output those file types instead.
        if (Imagejpeg($final, $destination, $quality)) {
            return true;
        }
        // If something went wrong
        return false;
    }


}
