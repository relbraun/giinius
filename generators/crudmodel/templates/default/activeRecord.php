<?php echo "<?php\n";

?>


class GiiniusActiveRecord extends CActiveRecord
{
    protected function beforeValidate()
    {
        foreach($this->attributes as $attr){
            if(is_array($attr) || is_object($attr)){
                $attr = serialize($attr);
            }
            if($file=CUploadedFile::getInstance($this, $key)){
                $this->$key = $this->uploadedFile;
                $file->saveAs('C:\\eeeef.jpg');
            }
        }
        return parent::beforeValidate();
    }

    protected function afterFind()
    {
        parent::afterFind();
        foreach($this->attributes as $attr){
            if(@unserialize($attr)){
                $attr = @unserialize($attr);
            }
        }
    }
}