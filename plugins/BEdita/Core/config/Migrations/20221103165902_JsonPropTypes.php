<?php
use BEdita\Core\Migration\ResourcesMigration;

class JsonPropTypes extends ResourcesMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->query("UPDATE property_types SET params = '{}' WHERE name = 'json'");
        parent::up();
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $this->query("UPDATE property_types SET params = '{\"type\":\"object\"}' WHERE name = 'json'");
        parent::down();
    }
}
