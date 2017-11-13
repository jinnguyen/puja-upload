<?php
namespace Puja\Upload\Validator;
use Puja\Upload\Upload;

abstract class ValidatorAbstract
{
    protected $uploader;
    public function __construct(Upload $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param array $uploadFile file object [name=><file name>, tmp_name => <tmp_name>, type => <file type>, size => <file size>, error => <file error>]
     * @return boolean
     */
    abstract public function validate($uploadFile = array());

}