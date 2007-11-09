<?php
class File extends BEAppObjectModel {

    var $name = 'File';

    function findByPath ($path, $name) {
        return $this->find("name = '$name' and path = '$path'");
    }

}
?>