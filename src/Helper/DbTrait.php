<?php
namespace Cake\Codeception\Helper;

trait DbTrait
{

    public function haveRecord($model, $attributes = [])
    {
        return TableRegistry::get($model)
            ->newEntity($attributes)
            ->isNew();
    }

    public function seeRecord($model, $attributes = [])
    {
        $record = $this->findRecord($model, $attributes);
        if (!$record) {
            $this->fail("Couldn't find $model with " . json_encode($attributes));
        }
        $this->debugSection($model, json_encode($record));
    }

    public function dontSeeRecord($model, $attributes = [])
    {
        $record = $this->findRecord($model, $attributes);
        $this->debugSection($model, json_encode($attributes));
        if ($record) {
            $this->fail("Unexpectedly managed to find $model with " . json_encode($attributes));
        }
    }

    public function grabRecord($model, $attributes = [])
    {
        $this->findRecord($model, $attributes);
    }

    public function findRecord($model, $attributes = [])
    {
        return TableRegistry::get($model)
            ->find()
            ->where($attributes)
            ->first();
    }
}
