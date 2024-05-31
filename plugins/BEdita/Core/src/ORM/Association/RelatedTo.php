<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Association;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Query;
use Cake\ORM\Table;

/**
 * Plain extension of {@see \Cake\ORM\Association\BelongsToMany} used to detect relations between BEdita objects.
 *
 * @since 4.0.0
 */
class RelatedTo extends BelongsToMany
{
    /**
     * The name of the field that describe an inverse relation.
     * This key is compared to `self::_foreignKey` to know the direction of the relation.
     * Default is `right_id`.
     *
     * @var string|string[]
     */
    protected $inverseKey = 'right_id';

    /**
     * Target object type.
     *
     * @var \BEdita\Core\Model\Entity\ObjectType|null
     */
    private $objectType = null;

    /**
     * @inheritDoc
     */
    protected function _options(array $opts): void
    {
        parent::_options($opts);

        if (!empty($opts['objectType'])) {
            $this->setObjectType($opts['objectType']);
        }
        if (!empty($opts['inverseKey'])) {
            $this->setInverseKey($opts['inverseKey']);
        }
    }

    /**
     * Set target object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType|null $objectType Object type for the target table (if one exists).
     * @return $this
     */
    public function setObjectType(?ObjectType $objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Get target object type.
     *
     * @return \BEdita\Core\Model\Entity\ObjectType|null
     */
    public function getObjectType(): ?ObjectType
    {
        return $this->objectType;
    }

    /** @inheritDoc */
    public function getTarget(): Table
    {
        $targetOT = $this->getObjectType();
        /** @var \Cake\ORM\Table&\BEdita\Core\Model\Behavior\ObjectTypeBehavior&\BEdita\Core\Model\Behavior\RelationsBehavior $target */
        $target = parent::getTarget();
        if ($targetOT === null || !$target->hasBehavior('ObjectType')) {
            return $target;
        }

        $objectType = $target->objectType();
        if ($objectType === null || $objectType->id !== $targetOT->id) {
            $target->setupRelations(
                $this->getTableLocator()->get('ObjectTypes')
                    ->get($targetOT->id)
            );
        }

        return $target;
    }

    /**
     * Set the name of the field used to check if the association represents
     * an inverse relation.
     *
     * @param string|string[] $key The key or keys used for inverse relation check.
     * @return $this
     */
    public function setInverseKey($key)
    {
        $this->inverseKey = $key;

        return $this;
    }

    /**
     * Return the inverse key.
     *
     * @return string|string[]
     */
    public function getInverseKey()
    {
        return $this->inverseKey;
    }

    /**
     * Get sub-query for matching.
     *
     * @param array $options Options array.
     * @return \Cake\ORM\Query
     */
    public function getSubQueryForMatching(array $options)
    {
        if (!isset($options['conditions'])) {
            $options['conditions'] = [];
        }
        $junction = $this->junction();
        $belongsTo = $junction->getAssociation($this->getSource()->getAlias());
        $condition = $belongsTo->_joinCondition(['foreignKey' => $belongsTo->getForeignKey()]);

        $subQuery = $this->find()
            ->select(array_values($condition))
            ->where($options['conditions'])
            ->andWhere($this->junctionConditions());

        if (!empty($options['queryBuilder'])) {
            $subQuery = $options['queryBuilder']($subQuery);
        }

        $assoc = $junction->getAssociation($this->getTarget()->getAlias());
        $conditions = $assoc->_joinCondition([
            'foreignKey' => $this->getTargetForeignKey(),
        ]);
        $subQuery = $this->_appendJunctionJoin($subQuery, $conditions);

        return $subQuery;
    }

    /**
     * Is source table abstract?
     *
     * @return bool
     */
    public function isSourceAbstract()
    {
        return $this->isAbstract($this->getSource());
    }

    /**
     * Is target table abstract?
     *
     * @return bool
     */
    public function isTargetAbstract()
    {
        return $this->isAbstract($this->getTarget());
    }

    /**
     * Given a table says if it describes an abstract object type
     *
     * @param \Cake\ORM\Table $table The table to verify
     * @return bool
     */
    protected function isAbstract(Table $table)
    {
        if (!$table->behaviors()->has('ObjectType')) {
            return false;
        }

        return $table->objectType()->is_abstract;
    }

    /**
     * Say if the association describe an inverse relation.
     * The association is inverse if foreign key and inverse key are the same.
     *
     * @return bool
     */
    public function isInverse(): bool
    {
        return $this->getForeignKey() === $this->getInverseKey();
    }

    /**
     * {@inheritDoc}
     *
     * Use inheritance subquery as table for target that is an inheritance table.
     */
    public function attachTo(Query $query, array $options = []): void
    {
        $targetTable = $this->getTarget();
        if ($targetTable instanceof InheritanceTable) {
            $options['table'] = $targetTable->query()->getInheritanceSubQuery();
        }

        parent::attachTo($query, $options);
    }
}
