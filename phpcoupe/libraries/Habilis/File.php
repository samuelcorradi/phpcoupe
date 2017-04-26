<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 18/01/15
 * Time: 13:49
 */

namespace Habilis;


class File extends \SplFileObject
{

    public function copy($filepath)
    {
        /*
         * Se pode realizar a cópia, retorna
         * um objeto do tipo File com o novo
         * arquivo.
         */
        if( copy($this->getRealPath(), $filepath) )
        {
            return new self($filepath);
        }

    }

    public function move($path)
    {

        return $this;

    }

}