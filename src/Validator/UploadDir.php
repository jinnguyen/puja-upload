<?php
namespace Puja\Upload\Validator;
use Puja\Stdlib\Folder\Folder;

class UploadDir extends ValidatorAbstract
{
    protected $errors = array(
        'UPLOAD_DIR_NOT_WRITABLE' => 'Folder %s is not writable!',
        'UPLOAD_DIR_INVALID' => '%s is not a directory.',
        'UPLOAD_DIR_NOT_EMPTY' => '$confg[uploadDir] is required'
    );

    public function validate($uploadFile = array())
    {
        $config = $this->uploader->getConfig();

        if (empty($config['uploadDir'])) {
            $this->uploader->addError($this->errors['UPLOAD_DIR_NOT_EMPTY']);
            return false;
        }

        $dir = new Folder($config['uploadDir']);

        if (!$dir->isDir()) {
            $this->uploader->addError(sprintf($this->errors['UPLOAD_DIR_INVALID'], $config['uploadDir']));
            return false;
        }

        if (!$dir->isWritable()) {
            $this->uploader->addError(sprintf($this->errors['UPLOAD_DIR_NOT_WRITABLE'], $config['uploadDir']));
            return false;
        }

        return true;
    }
}