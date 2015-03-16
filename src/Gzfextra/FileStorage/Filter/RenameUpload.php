<?php

namespace Gzfextra\FileStorage\Filter;

use Zend\Filter\File\RenameUpload as ZendRenameUpload;

/**
 * Class RenameUpload
 *
 * @author  Moln Xie
 */
class RenameUpload extends ZendRenameUpload
{
    public function getFinalTarget($uploadData)
    {
        return parent::getFinalTarget($uploadData);
    }
}