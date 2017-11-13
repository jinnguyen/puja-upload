<?php
namespace Puja\Upload;
use Puja\Stdlib\File;

class Upload
{
    protected $config;
    protected $validators;
    protected $errors;
    protected $files;
    public function __construct($config = array())
    {
        $this->errors = array();
        $this->config = $config;
        $this->validators = array();
        $this->files = $_FILES;
        $this->addValidator('Puja\\Upload\\Validator\\Base');
        $this->addValidator('Puja\\Upload\\Validator\\UploadDir');
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($errorMsg)
    {
        $this->errors[] = $errorMsg;
        return $this;
    }


    public function addValidator($validatorClass)
    {
        $this->validators[$validatorClass] = $validatorClass;
        return $this;
    }

    public function multiUpload($tagname)
    {
        $files = array();
        if (empty($_FILES[$tagname]['name'])) {
            return $files;
        }
        $tmpFiles = array();
        foreach ($_FILES[$tagname]['name'] as $key => $value) {
            $uploadFile = array(
                'name' => $_FILES[$tagname]['name'][$key],
                'type' => $_FILES[$tagname]['type'][$key],
                'tmp_name' => $_FILES[$tagname]['tmp_name'][$key],
                'error' => $_FILES[$tagname]['error'][$key],
                'size' => $_FILES[$tagname]['size'][$key],
            );
            $tmpFiles[] = $uploadFile;
            $this->validate($uploadFile);
        }
        return $files;
    }

    public function upload($tagname)
    {
        if (empty($_FILES[$tagname]['name'])) {
            return false;
        }
        $this->validate($_FILES[$tagname]);
        return $this->execute($_FILES[$tagname]);
    }

    protected function execute($uploadFile)
    {
        $tmpFileObj = new File\File($uploadFile['tmp_name']);
        $fileObj = new File\Info($uploadFile['name']);
        
        $uploadDir = rtrim($this->config['uploadDir'], '/') . '/';
        $dstFile = $fileObj->getFilename();
        if (file_exists($uploadDir . $dstFile)) {
            $dstFile = $fileObj->getBasenameWithoutExt('-' . $tmpFileObj->getBasename()) . '.' . $fileObj->getExtension();
        }

        try {
            $tmpFileObj->moveUploadedFile($uploadDir . $dstFile);
        } catch (\Exception $e) {
            $dstFile = null;
            $this->errors[] = $e->getMessage();
        }

        return $dstFile;

    }

    protected function validate($uploadFile)
    {
        foreach ($this->validators as $validatorClass) {
            if (!class_exists($validatorClass)) {
                throw new Exception('Class ' . $validatorClass . ' does not exist!');
            }

            $validator = new $validatorClass($this);
            if (!($validator instanceof Validator\ValidatorAbstract)) {
                throw new Exception('Class ' . $validatorClass . ' must be extended of Puja\Upload\Validator\ValidatorAbstract');
            }

            $validator->validate($uploadFile);
        }

        return true;
    }

}