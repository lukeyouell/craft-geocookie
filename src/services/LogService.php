<?php
/**
 * Geo Cookie plugin for Craft CMS 3.x
 *
 * Collect information about a visitor's location based on their IP address and store the information as a cookie.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2017 Luke Youell
 */

namespace lukeyouell\geocookie\services;

use lukeyouell\geocookie\GeoCookie;
use lukeyouell\geocookie\models\Log as LogModel;
use lukeyouell\geocookie\records\Log as LogRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\Json;

use yii\base\Exception;
use yii\base\InvalidConfigException;

class LogService extends Component
{
    // Public Methods
    // =========================================================================

    public function getLogs()
    {
        $rows = $this->_createLogQuery()
            ->all();

        $logs = [];

        foreach ($rows as $row) {
            $logs[] = new LogModel($row);
        }

        return $logs;
    }

    public function insertLog($status, $source, $data)
    {
        $log = new LogModel();

        $log->status  = $status;
        $log->source = $source;
        $log->data = Json::encode($data);

        // Save it
        $save = $this->saveLog($log);

        // Delete old logs
        $this->deleteOldLogs();

        return true;
    }

    public function saveLog(LogModel $model, bool $runValidation = true)
    {
        $record = new LogRecord();

        if ($runValidation && !$model->validate()) {
            Craft::info('Log not saved due to a validation error.', __METHOD__);
            return false;
        }

        $record->status   = $model->status;
        $record->source   = $model->source;
        $record->data     = $model->data;

        // Save it
        $record->save(false);

        // Now that we have a record id, save it on the model
        $model->id = $record->id;

        return true;
    }

    public function deleteOldLogs()
    {
        $models = LogRecord::find()
            ->offset(50)
            ->orderBy('dateCreated desc')
            ->all();

        foreach ($models as $model) {
            $model->delete();
        }
    }

    // Private Methods
    // =========================================================================

    private function _createLogQuery()
    {
        return (new Query())
            ->select([
                '{{%geocookie_log}}.id',
                '{{%geocookie_log}}.dateCreated',
                '{{%geocookie_log}}.status',
                '{{%geocookie_log}}.source',
                '{{%geocookie_log}}.data',
            ])
            ->orderBy('dateCreated desc')
            ->from(['{{%geocookie_log}}']);
    }
}
