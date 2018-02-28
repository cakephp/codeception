<?php
namespace Cake\Codeception\Helper;

use Cake\ORM\TableRegistry;

trait DbTrait
{
    protected $insertedRecords = [];

    /**
     * Cleans up inserted records.
     *
     * @param string $model Model alias.
     * @return void
     */
    public function cleanUpInsertedRecords($model = null)
    {
        $records = $this->insertedRecords;

        if (empty($records)) {
            return;
        }

        if (!empty($model) && !empty($records[$model])) {
            $this->deleteAllRecords($model, $records[$model]);

            return;
        }

        foreach ($records as $model => $data) {
            $this->deleteAllRecords($model, $data);
        }
    }

    /**
     * Delete inserted records with constraints disabled.
     *
     * @param string $model Model alias.
     * @param array $data Delete target conditions.
     * @return void
     */
    protected function deleteAllRecords($model, $data)
    {
        $table = TableRegistry::get($model);
        $table->connection()
            ->disableConstraints(function () use ($table, $data) {
                $table->deleteAll($data);
            });
    }

    /**
     * Inserts record into the database.
     *
     * @param string $model Model alias.
     * @param Cake\ORM\Entity|array $data Entity data.
     * @return string Record's primary key.
     */
    public function haveRecord($model, $data = [])
    {
        $table = TableRegistry::get($model);

        if (!($data instanceof Entity)) {
            $data = $table->newEntity($data, ['validate' => false]);
        }

        if (!$table->save($data, ['checkRules' => false])) {
            $this->fail('Could not insert record into table ' . $model);
        }

        if (empty($this->insertedRecords[$model])) {
            $this->insertedRecords[$model] = [];
        }

        $this->insertedRecords[$model][$table->primaryKey() . ' IN'][] = $data->{$table->primaryKey()};
        return $data->{$table->primaryKey()};
    }

    /**
     * Checks that record exists in database.
     *
     * @param string $model Model alias.
     * @param array $conditions Conditions to find the record.
     */
    public function seeRecord($model, $conditions = [])
    {
        $record = $this->findRecord($model, $conditions);
        $this->debugSection($model, json_encode($record));
        if (!$record) {
            $this->fail("Couldn't find $model with " . json_encode($conditions));
        }
    }

    /**
     * Checks that record does not exist in database.
     *
     * @param string $model Model alias.
     * @param array $conditions Conditions to find the record.
     */
    public function dontSeeRecord($model, $conditions = [])
    {
        $record = $this->findRecord($model, $conditions);
        $this->debugSection($model, json_encode($conditions));
        if ($record) {
            $this->fail("Unexpectedly managed to find $model with " . json_encode($conditions));
        }
    }

    /**
     * Retrieves record from database.
     *
     * @param string $model Model alias.
     * @param array $conditions Conditions to find the record.
     * @return \Cake\ORM\Entity Record.
     */
    public function grabRecord($model, $conditions = [])
    {
        return $this->findRecord($model, $conditions);
    }

    /**
     * Wraps the ORM finder query used by the other methods.
     *
     * @param string $model Model alias.
     * @param array $conditions Conditions to find the record.
     */
    protected function findRecord($model, $conditions = [])
    {
        return TableRegistry::get($model)
            ->find()
            ->where($conditions)
            ->first();
    }
}
