<?php

namespace BEdita\Core\ORM;

use BEdita\Core\ORM\Query;
use Cake\ORM\Table as CakeTable;

class Table extends CakeTable
{
    /**
     * {@inheritDoc}
     */
    public function query()
    {
        return new Query($this->connection(), $this);
    }
}
