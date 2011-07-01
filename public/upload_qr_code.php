<?php

include("xml2array.php");

$API_KEY = "";
$EMAIL = "";
$STORE_NAME = "";
$PASSWORD = "";

function send_data ($url, $verbose) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    if($verbose == TRUE) {
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
    }
    
    $result = curl_exec($ch);

    curl_close($ch);
    
    return $result;
}

function send_post($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_VERBOSE,1);
    $response = curl_exec($ch);
    return $response;
}

// And another great function for PHP is xml2array to clean up the XML response values and get to data easier (well for me it was easier). 
// Check out the code at http://www.bin-co.com/php/scripts/xml2array/index.php.

// First off get the user token:

function get_userToken() {
    global $API_KEY, $EMAIL, $PASSWORD;

    $AuthUrl = "http://api.cafepress.com/authentication.getUserToken.cp?v=3&appKey=".$API_KEY;
    $AuthUrl .= "&email=".$EMAIL."&password=".$PASSWORD;
    
    $userToken = send_data($AuthUrl, FALSE);
    $arr = xml2array($userToken);
    return $arr["value"];
}

// One note to check is the appkey variable is case sensitive so make sure you make it appKey. 
// And though the singature here: http://cafepressdn.com/api/content/Authentication.aspx shows username between appKey and password 
// the real parameter for this is email as shown in the Parmameters box....

// Next I take the user token and the application key to upload the images to cafe press

function upload_image($ut,$path) {
    global $API_KEY;

    $postData = array();
    $postData['userToken'] = $ut;
    $postData['appKey'] = $API_KEY;
    $postData['folder'] = "Images"; 
    
    $uploadUrl = "http://upload.cafepress.com/image.upload.cp";

    $postData['cpFile1'] = "@".realpath($path);
    
    //print_r($postData);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_VERBOSE,1);
    $response = curl_exec($ch);
    
    //print_r($response);
    
    curl_close($ch);
    
    $arr = xml2array($response);
    return $arr['values']['value']; //this is the image id
}

// I pass into this function the user token and the path to the image I want to upload. Beacuse this is a POST operation I set up cURL to handle that... 
// I take the response from the upload and pull out the image id value to use for setting the tags and creating a product. 
// NOTE that the folder option is 'folder' and NOT 'folderName' as it shows in the api documents....

// Now to uplaod tags to associate with the image.
function set_tags($ut, $did, $tags){
    global $API_KEY;
    
    $tagUrl = "http://api.cafepress.com/design.tagDesigns.cp";
    
    $postData = array();
    $postData['appKey'] = $API_KEY;
    $postData['userToken'] = $ut;
    $postData['designIds'] = $did;
    $postData['tags'] = $tags;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tagUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_VERBOSE,1);
    $respone = curl_exec($ch);
    curl_close($ch);
}


// I decided that in some cases I had a large number of tags that I would pass the data for this call as a POST instead of a GET
// so the cURL set up is the same as with the image upload. Not much to this one...

// Now creating a product.... This one got me, after looking through the fourms I found that 
// I did not need all that extra crap in the XML just the <PRODUCT> tags filled out... much cleaner .

function create_product($ut,$design_id){
    global $API_KEY, $STORE_NAME;
    
    $prodXML = "<?xml version=\"1.0\"?>";
    $prodXML .= "<product merchandiseId=\"2\"";
    $prodXML .= " sellPrice=\"21.99\"";
    $prodXML .= " description=\"test\"";
    $prodXML .= " storeId=\"$STORE_NAME\"";
    $prodXML .= " sectionId=\"0\">";
    $prodXML .= "<mediaConfiguration dpi=\"100\"";
    $prodXML .= " height=\"2.49\"";
    $prodXML .= " width=\"2.49\"";
    $prodXML .= " name=\"FrontCenter\"";
    $prodXML .= " designId="."\"$design_id\"";
    $prodXML .= " />";
    $prodXML .= "</product>";
    
    $prodXMLurlEncode = urlencode($prodXML);
    
    $url ="http://api.CafePress.com/product.save.cp?v=3&appKey=$API_KEY&userToken=$ut&value=".$prodXMLurlEncode;
    
    $response = send_data($url, TRUE);
    //echo "SAVED:[" .$response."]";
    
    return $response;
}

// This one I ran as a GET.. I just had to remember to encode the XML string to be URL friendly...

// Thats really it. Let me know if I left something out...

$ut = get_userToken();
$image_id = upload_image($ut, "");
//$image_id = "56904926";
set_tags($ut, $image_id, $null);
$product_info = create_product($ut, $image_id);
$prod_arr = xml2array($product_info);

$prod = $prod_arr["product_attr"];
$id = $prod["id"];
$name = $prod["name"];
$price = $prod["sellPrice"];
$image = $prod["defaultProductUri"];
$url = $prod["storeUri"];

?>

{"products": [
    { "id":"<?= $id; ?>", "name":"<?= $name; ?>", "price":"<?= $price; ?>", "image":"<?= $image; ?>", "url":"<?= $url; ?>" }
]}