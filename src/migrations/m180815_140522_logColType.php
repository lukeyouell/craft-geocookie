<?php

namespace lukeyouell\geocookie\migrations;

use Craft;
use craft\db\Migration;

class m180815_140522_logColType extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%geocookie_log}}', 'data', $this->text());
    }

    public function safeDown()
    {
        echo "m180815_140522_logColType cannot be reverted.\n";
        return false;
    }
}
