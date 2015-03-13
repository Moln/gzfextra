<?php

namespace Gzfextra\FileStorage\Filter;

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