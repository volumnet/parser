<?php
namespace VolumNet\Parser;

use \InvalidArgumentException;

/**
 * Image copier
 * @property array $data Stored data
 */
class ImageCopier
{
    /**
     * Base directory
     * @var string
     */
    protected $baseDir;

    /**
     * Constructor
     * @param string $baseDir Base directory
     */
    public function __construct($baseDir)
    {
        if (is_file($baseDir)) {
            throw new InvalidArgumentException($baseDir . ' already exists');
        }
        $this->baseDir = $baseDir;
    }


    /**
     * Copies file
     * @param string $src Source file
     * @param string $destDir Destination directory, relative to $baseDir
     * @return string actual path
     */
    public function copy($src, $destDir = '')
    {
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }
        if ($destDir) {
            $dir = $this->baseDir . '/' . $destDir;
            if (is_file($dir)) {
                throw new InvalidArgumentException($dir . ' already exists');
            }
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        } else {
            $dir = $this->baseDir;
        }
        $text = @file_get_contents($src);
        if (!$text) {
            throw new InvalidArgumentException($src . ' is empty');
        }
        $basename = basename($src);
        if (is_file($dir . '/' . $basename)) {
            throw new InvalidArgumentException($dir . '/' . $basename . ' already exists');
        }
        $tmpname = tempnam(sys_get_temp_dir(), '');
        file_put_contents($tmpname, $text);
        $arr = getimagesize($tmpname);
        if (!$arr) {
            unlink($tmpname);
            throw new InvalidArgumentException($src . ' is not an image');
        }
        $ext = image_type_to_extension($arr[2]);
        $ext = preg_replace('/(p)?jpeg/umi', 'jpg', $ext);
        $filename = pathinfo($src, PATHINFO_FILENAME) . $ext;
        if (is_file($dir . '/' . $filename)) {
            throw new InvalidArgumentException($dir . '/' . $filename . ' already exists');
        }
        copy($tmpname, $dir . '/' . $filename);
        return $dir . '/' . $filename;
    }
}
