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

function uploadImage($file, $targetDir = "assets/img/") {
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
    
    // Check if image exists in the assets/img folder
    if (!empty($image)) {
        // Check relative to the includes directory
        if (file_exists(__DIR__ . "/../assets/img/" . $image)) {
            return SITE_URL . "assets/img/" . $image;
        }
    }
    
    // Fallback to the first available image in assets/img if it exists
    $imgDir = __DIR__ . "/../assets/img/";
    if (is_dir($imgDir)) {
        $files = glob($imgDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        if (!empty($files)) {
            // For a "proper linking mechanism", we could use the name to pick a semi-consistent image
            // but for now let's just use the first one or a random one from the folder
            $index = abs(crc32($name)) % count($files);
            return SITE_URL . "assets/img/" . basename($files[$index]);
        }
    }
    
    // Final fallback to a working placeholder service
    $query = urlencode($name);
    return "https://loremflickr.com/800/600/" . $query . ",product";
}
?>
