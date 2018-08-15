<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\models;

use lukeyouell\geocookie\records\Log as LogRecord;

use Craft;
use craft\base\Model;
use craft\helpers\Json;

class Log extends Model
{
    // Public Properties
    // =========================================================================

    public $id;

    public $dateCreated;

    public $status;

    public $source;

    public $data;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        $this->data = $this->setData();
    }

    public function __toString()
    {
        return (string) $this->source;
    }

    public function rules()
    {
        return [
            [['status', 'source', 'data'], 'required'],
            [['status'], 'in', 'range' => [LogRecord::STATUS_SUCCESS, LogRecord::STATUS_FAIL]],
        ];
    }

    public function setData()
    {
        return Json::decode($this->data);
    }
}
