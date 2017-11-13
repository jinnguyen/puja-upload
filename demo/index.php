<?php
include __DIR__ . '/../vendor/autoload.php';
use Puja\Upload\Upload;
if (!empty($_POST)) {
    $uploader = new Upload(array(
        'uploadDir' => __DIR__ . '/',
        'transaction' => true,
        'allowFileExt' => '.jpg,.png',
        
    ));//'', '.jpg,.png', 1 * 1024 * 1024);
    echo 'Uploaded File: ' . $uploader->upload('uploadname');
    $errors = $uploader->getErrors();
    print_r($errors);exit;

    $uploadFiles = $uploader->multiUpload('multiname');
    $errors = $uploader->getErrors();
    if ($errors) {
        echo 'Upload Error: ' . PHP_EOL;
        print_r($errors);
    }
    print_r($uploadFiles);
}
?>
<form method="POST" enctype="multipart/form-data">
    <input name="name" value="hehe" />
    <input type="file" name="uploadname" />
    <input type="file" name="multiname[]" />
    <input type="file" name="multiname[]" />
    <input type="submit" valu="Submit" />
</form>
