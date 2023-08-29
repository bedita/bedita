<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class UpdateDateRanges extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function change()
    {
        $this->table('date_ranges')
            ->changeColumn('start_date', 'datetime', [
                'comment' => 'range start date time',
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->update();
    }
}
