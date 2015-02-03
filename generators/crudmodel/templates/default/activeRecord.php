<?php echo "<?php\n";

?>


class GiiniusActiveRecord extends CActiveRecord
{
    protected function beforeValidate()
    {
        foreach($this->attributes as $key => $attr){
            if(is_array($attr) || is_object($attr)){
                $attr = serialize($attr);
            }
            if($file=CUploadedFile::getInstance($this, $key)){
                $this->$key = Yii::app()->baseUrl . '/uploads/'.$file->name;
                $uploadsPath = Yii::app()->basePath.'/../uploads';
                if(!is_file($uploadsPath)){
                    @mkdir($uploadsPath);
                }
                $file->saveAs($uploadsPath . '/'.$file->name);
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
 
    function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}