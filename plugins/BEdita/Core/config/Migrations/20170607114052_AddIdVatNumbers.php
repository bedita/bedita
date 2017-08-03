<?php
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

use Migrations\AbstractMigration;

/**
 * Add VAT ID number to `profiles` table.
 *
 * @since 4.0.0
 */
class AddIdVatNumbers extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('profiles')
            ->addColumn('national_id_number', 'string', [
                'comment' => 'national identification number (like SSN in USA or NI number in UK)',
                'null' => true,
                'default' => null,
                'limit' => 32,
            ])
            ->addColumn('vat_number', 'string', [
                'comment' => 'value added tax identification number (VAT)',
                'null' => true,
                'default' => null,
                'limit' => 32,
            ])
            ->addIndex(
                [
                    'national_id_number',
                ],
                [
                    'name' => 'profiles_nationalidnumber_idx',
                ]
            )
            ->addIndex(
                [
                    'vat_number',
                ],
                [
                    'name' => 'profiles_vatnumber_idx',
                ]
            )
            ->update();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->table('profiles')
            ->removeIndexByName('profiles_nationalidnumber_idx')
            ->removeIndexByName('profiles_vatnumber_idx')
            ->removeColumn('national_id_number')
            ->removeColumn('vat_number')
            ->update();
    }
}
