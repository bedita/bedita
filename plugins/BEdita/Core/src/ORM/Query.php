<?php

namespace BEdita\Core\ORM;

use BEdita\Core\ORM\ResultSet;
use Cake\ORM\Query as CakeQuery;

class Query extends CakeQuery
{
    /**
     * {@inheritDoc}
     */
    protected function _execute()
    {
        $this->triggerBeforeFind();
        if ($this->_results) {
            $decorator = $this->_decoratorClass();

            return new $decorator($this->_results);
        }

        $statement = $this->eagerLoader()->loadExternal($this, $this->execute());

        return new ResultSet($this, $statement);
    }
}
