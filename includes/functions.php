<?php
function formatPrice($price) {
    return '$' . number_get_formatted_price($price);
}

function number_get_formatted_price($price) {
    return number_format((float)$price, 2, '.', ',');
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, info
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function uploadImage($file, $targetDir = "assets/images/") {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $fileName = time() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) return null;
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return null;
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $fileName;
    }
    return null;
}

function getProductImage($image, $name, $isAdmin = false) {
    // If it's a full URL, return it
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }
    
    // Check if image exists in the assets/images folder
    if (!empty($image)) {
        // Check relative to the includes directory
        if (file_exists(__DIR__ . "/../assets/images/" . $image)) {
            return SITE_URL . "assets/images/" . $image;
        }
    }
    
    // Fallback to a working placeholder service
    $query = urlencode($name);
    return "https://loremflickr.com/800/600/" . $query . ",product";
}
?>
