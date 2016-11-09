<?php
namespace RudyMas\FileManager;

use SplFileInfo;

/**
 * Class FileManager
 * Working with files on a server
 *
 * @author      Rudy Mas <rudy.mas@rudymas.be>
 * @copyright   2014 - 2016, rudymas.be. (http://www.rudymas.be/)
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version     1.1.0
 * @package     RudyMas\FileManager
 */
class FileManager
{
    public $data = [];
    public $numberOfFiles = 0;

    /**
     * To create a new folder
     *
     * @param string $folder The folder to create
     * @param bool $allFolders Allows the creation of nested directories specified in the pathname. (Default: FALSE)
     */
    public function createFolder($folder, $allFolders = FALSE)
    {
        if (substr($folder, -1) != '/') $folder = $folder . '/';
        if (!is_dir($folder)) {
            @mkdir($folder, 0777, $allFolders) or die ("Error creating folder '{$folder}'.");
        }
    }

    /**
     * To rename a file
     *
     * @param string $originalFilename The original filename
     * @param string $newFilename The new filename
     */
    public function renameFile($originalFilename, $newFilename)
    {
        @rename($originalFilename, $newFilename) or die("Error renaming '{$originalFilename}' to '{$newFilename}'.");
    }

    /**
     * To move a file
     *
     * @param string $file The file to move
     * @param string $originalFolder The original folder where the file is
     * @param string $newFolder The folder where the file has to go
     */
    public function moveFile($file, $originalFolder, $newFolder)
    {
        if (substr($originalFolder, -1) != '/') $originalFolder = $originalFolder . '/';
        if (substr($newFolder, -1) != '/') $newFolder = $newFolder . '/';
        $this->copyFile($originalFolder . $file, $newFolder . $file);
        $this->deleteFile($originalFolder . $file);
    }

    /**
     * To copy a file
     *
     * @param string $originalFile Original file to copy
     * @param string $newFile The copy of the original file
     */
    public function copyFile($originalFile, $newFile)
    {
        if (is_file($originalFile)) {
            @copy($originalFile, $newFile) or die("File '{$originalFile}' can't be copied to '{$newFile}'.");
        } else {
            die("File '{$originalFile}' doesn't exist.");
        }
    }

    /*
     * Inlezen van alle bestanden in een map (resultaat $data + $aantalBestanden)
     */


    /**
     * Reading the files and folders of a certain folder
     *
     * @param string $folder The folder to read
     * @param int $sort How the data has to be sorted (Default: ascending)
     *                          - SCANDIR_SORT_DESCENDING: Sort descending (z -> a)
     *                          - SCANDIR_SORT_NONE: No sorting done
     *
     * $this->data              An array with all the information of the folder
     * $this->numberOfFiles     The number of items found
     */
    public function readFolder($folder, $sort = 0)
    {
        if (substr($folder, -1) != '/') $folder = $folder . '/';
        $this->data = @scandir($folder, $sort) or die("Error reading folder: '{$folder}'.");
        $this->numberOfFiles = count($this->data);
    }

    /**
     * Retrieving the extension of a file
     *
     * @param string $file The file to retrieve the file extension from
     * @return string           Returns the extension of the file
     */
    public function fileExtension($file)
    {
        $fileInfo = new SplFileInfo($file) or die("Error opening file: '{$file}'.");
        return $fileInfo->getExtension();
    }

    /**
     * Retrieving the name of the file
     *
     * @param string $file The file to retrieve the name from
     * @return string           Returns the name of the file
     */
    public function filename($file)
    {
        $fileInfo = new SplFileInfo($file) or die("Error opening file: '{$file}'.");
        return $fileInfo->getFilename();
    }


    /**
     * Process an uploaded file and moved to a folder on the server
     *
     * @param string $originalFile The original file inside the temporary folder
     * @param string $newFile The new name for the file
     * @param string $folder The folder where the file has to go
     */
    public function processUploadedFile($originalFile, $newFile, $folder)
    {
        if (substr($folder, -1) != '/') $folder = $folder . '/';
        @move_uploaded_file($originalFile, $folder . $newFile) or die("File: '{$originalFile}' couldn't be moved '{$folder}'.");
        @chmod($folder . $newFile, 0777) or die("Error with chmod() function on file: '{$newFile}' in folder '{$folder}'.");
    }

    /**
     * Delete a file from the server
     *
     * @param string $file The file to be deleted
     */
    public function deleteFile($file)
    {
        @unlink($file) or die("File '{$file}' couldn't be removed from the server.");
    }

    /**
     * Saving data into a file (This method is used for huge files)
     *
     * @param string $data The data to be written into the file
     * @param string $file The file to be used
     */
    public function saveFile($data, $file)
    {
        $openfile = @fopen($file, "wb") or die("saveFile: Error opening file '{$file}'.");
        @fwrite($openfile, $data) or die("saveFile: Error writing data into file '{$file}'.");
        @fclose($openfile);
    }

    /**
     * Loading data from a file (This method is used for huge files)
     *
     * @param string $file The file to read data from
     * @return string           Returns the data of the file
     */
    public function loadFile($file)
    {
        $openfile = @fopen($file, "rb") or die("loadFile: Error opening file '{$file}'.");
        $contents = @fread($openfile, filesize($file)) or die("loadFile: Error reading data from file '{$file}'.");
        @fclose($openfile);
        return $contents;
    }

    /**
     * Saving data into a file
     *
     * @param string $string The data to be written into the file
     * @param string $file The file to be used
     */
    public function saveLittleFile($string, $file)
    {
        @file_put_contents($file, $string) or die("saveLittleFile: Error writing data to file '{$file}'.");
    }

    /**
     * Loading data from a file
     *
     * @param string $file The file to read data from
     * @return string           Returns the data of the file
     */
    public function loadLittleFile($file)
    {
        $file = @file_get_contents($file) or die("loadLittleFile: Error reading data from file '{$file}'.");
        return $file;
    }
}
/** End of File: FileManager.php **/