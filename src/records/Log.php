<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\records;

use craft\db\ActiveRecord;

class Log extends ActiveRecord
{
    // Constants
    // =========================================================================

    const STATUS_SUCCESS = 'success';

    const STATUS_FAIL = 'fail';

    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%geocookie_log}}';
    }
}
