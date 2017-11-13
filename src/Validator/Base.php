<?php
namespace Puja\Upload\Validator;

class Base extends ValidatorAbstract
{
    protected $errors = array(
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the %s',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk. ',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        'UPLOAD_ERR_EXT_ALLOWS' => 'File extension do not allow, you can only upload file ext in %s',
    );
    
    public function validate($uploadFile = array())
    {
        $config = $this->uploader->getConfig();
        if (!empty($config['allowFileExt'])) {
            if (is_array($config['allowFileExt'])) {
                $config['allowFileExt'] = implode(',', $config['allowFileExt']);
            }

            $fileInfo = new \Puja\Stdlib\File\Info($uploadFile['name']);
            if (!strpos('__' . strtolower($config['allowFileExt']), '.' . strtolower($fileInfo->getExtension()))) {
                $this->uploader->addError(sprintf($this->errors['UPLOAD_ERR_EXT_ALLOWS'], $config['allowFileExt']));
                return false;
            }
        }

        if (!empty($config['maxFileSize'])  && $config['maxFileSize'] * 1024 < $uploadFile['size']) {
            $this->uploader->addError(sprintf($this->errors[UPLOAD_ERR_INI_SIZE], $config['maxFileSize']));
            return false;
        }

        if ($uploadFile['error'] != UPLOAD_ERR_OK) {
            $msg = $this->errors[$uploadFile['error']];
            if ($uploadFile['error'] == UPLOAD_ERR_INI_SIZE) {
                $msg = sprintf($msg, ini_get('upload_max_filesize'));
            }

            $this->uploader->addError($msg);
            return false;
        }
        
    }
}