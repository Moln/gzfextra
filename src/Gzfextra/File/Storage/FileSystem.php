<?php
/**
 * platform-admin FileSystem.php
 *
 * @DateTime 13-5-30 下午1:39
 */

namespace Gzfextra\File\Storage;

/**
 * Class FileSystem
 *
 * @package Gzfextra\File\Storage
 * @author  Xiemaomao
 * @version $Id$
 */
class FileSystem extends AbstractStorage
{
    protected $defaultPath = '/tmp';

    public function move($source, $target)
    {
        return move_uploaded_file($source, $target);
    }

    /**
     * @param      $directory
     * @param bool $showDetail
     *
     * @return array|FileInfo[]
     */
    public function readDirectory($directory, $showDetail = false)
    {
        $list = glob($this->getDefaultPath() . trim($directory, '\\/') . '/*');
        if ($showDetail) {
            foreach ($list as &$file) {
                $file = new FileInfo(array(
                    'name'  => basename($file),
                    'size'  => filesize($file),
                    'mtime' => filemtime($file),
                    'type'  => is_dir($file) ? 'directory' : 'file',
                ));
            }

            return $list;
        } else {
            return $list;
        }
    }

    public function mkdirs($path, $mode = 0777)
    {
        $path = $this->getDefaultPath() . ltrim(str_replace(array('./', '../'), '', $path), '\\/');

        $paths = explode(
            DIRECTORY_SEPARATOR,
            str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, trim($path, '\\/'))
        );
        $root  = '';
        foreach ($paths as $dir) {
            $root .= '/' . $dir;
            if (!is_dir($root) && !mkdir($root, $mode)) {
                return false;
            }
        }
        return true;
    }

    public function upload($value)
    {
        $filter = $this->getFilter('renameupload');
        $target = $filter->getTarget();
        $filter->setTarget($this->getDefaultPath() . ltrim($target, '\\/'));

        return $this->getFilterChain()->filter($value);
    }

    /**
     * Delete directory or file
     *
     * @param string $path
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function delete($path)
    {
        $path = realpath($path);
        if (strpos($path, $this->getDefaultPath()) === 0) {
            if (is_dir($path)) {
                foreach (glob($path . '/*') as $file) {
                    $this->delete($path);
                }
                return @rmdir($path);
            } else {
                return @unlink($path);
            }
        } else {
            throw new \InvalidArgumentException("Path not allowed '$path'");
        }
    }

    /**
     * @param string $defaultPath
     *
     * @throws \InvalidArgumentException
     * @return AbstractStorage
     */
    public function setDefaultPath($defaultPath)
    {
        $defaultPath = realpath($defaultPath);
        if (!$defaultPath) {
            throw new \InvalidArgumentException('Error default path!');
        }
        $this->defaultPath = $defaultPath . DIRECTORY_SEPARATOR;
        return $this;
    }
}