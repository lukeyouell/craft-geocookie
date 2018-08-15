<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\migrations;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class m180815_135218_logs extends Migration
{
    // Public Properties
    // =========================================================================

    public $driver;

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        // support_tickets table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%geocookie_log}}');
        if ($tableSchema === null) {
            $tablesCreated = true;

            $this->createTable(
                '{{%geocookie_log}}',
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns in the table
                    'status'      => $this->string(),
                    'source'      => $this->string(),
                    'data'        => $this->string(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function dropTables()
    {
        $this->dropTable('{{%geocookie_log}}');
    }
}
