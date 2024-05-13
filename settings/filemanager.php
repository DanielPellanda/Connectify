<?php 

require_once ROOT.'/init.php';

// Here we implement function for upload and download of files into the server

class FileManager {

    // Max file size allowed for the upload
    private static $max_size = 52428800; // 50 MB

    // Checks whether a specific file name respects the formats specified
    public static function IsFileFormatValid($file, $formats_allowed = array()) {

        if (empty($formats_allowed)) {
            return true;
        }

        $file_format = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        foreach ($formats_allowed as $f) {
            if (!strcmp($f, $file_format)) {
                return true;
            }   
        }
        return false;
    }

    // Uploads a file to the server
    // # Params
    // $dir : Server directory to store the file
    // $override_name (optional) : New name for the file uploaded (excluding the format)
    // $formats (optional) : File formats allowed for the upload
    public static function Upload($file, $dir, $override_name = null, $formats = array()) {
        // Basic checks
        if (empty($dir)) {
            return false;
        }

        // Check file size
        if ($file['size'] > FileManager::$max_size) {
            return false;
        }

        // Allow certain file formats
        if (!FileManager::IsFileFormatValid($file['name'], $formats)) {
            return false;
        }

        // Normalize the directory path to avoid problems
        $dir = str_replace('\\', '/', $dir);
        if (substr($dir, strlen($dir)-1, 1) !== '/') {
            $dir = $dir.'/';
        }
        $local_dir = ROOT.$dir;

        // Apply the overridden name if present
        $filename = $file['name'];
        if (!empty($override_name)) {
            $file_format = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = $override_name.'.'.$file_format;
        }

        // Create the file path
        $path = $dir.$filename;
        $local_path = ROOT.$path;

        if (!is_dir($local_dir)) {
            mkdir($local_dir);
        }
        
        // Try to upload the file
        if (move_uploaded_file($file['tmp_name'], $local_path)) {
            // Upload successful
            return $path;
        }
        return false;
    }
} 

?>