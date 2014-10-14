<?php
/**
 * platform-admin RenameUpload.php
 *
 * @DateTime 13-6-5 下午4:36
 */

namespace Gzfextra\Filter\File;

use Zend\Filter\File\RenameUpload as ZendRenameUpload;

/**
 * Class RenameUpload
 *
 * @package Gzfextra\Filter\File
 * @author  Moln Xie
 * @version $Id$
 */
class RenameUpload extends ZendRenameUpload
{
    public function getFinalTarget($uploadData)
    {
        return parent::getFinalTarget($uploadData);
    }
}