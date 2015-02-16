<?php
namespace Cake\Codeception\Helper;

use Cake\ORM\TableRegistry;

trait ORMTrait
{
    public function seeValidationErrors($entity, $data, array $expectedErrors = [])
    {
        $errors = $this->tryValidating($entity, $data);

        foreach ($expectedErrors as $field => $error) {
            if (is_numeric($field)) {
                $field = $error;
                $error = null;
            }

            $this->assertContains($field, array_keys($errors), json_encode($errors));

            if (empty($error)) {
                continue;
            }

            $error = (array)$error;

            foreach ($error as $name => $message) {
                if (is_numeric($name)) {
                    $name = $message;
                    $message = null;
                }

                $this->assertContains($name, array_keys($errors[$field]));
                if (empty($message)) {
                    continue;
                }

                $this->assertEquals($message, $errors[$field][$name]);
            }
        }
    }

    public function dontSeeValidationErrors($entity, $data)
    {
        $errors = $this->tryValidating($entity, $data);
        $this->assertEquals(0, count($errors), json_encode($errors));
    }

    public function grabTable($alias)
    {
        return TableRegistry::get($alias);
    }

    public function tryPatching($entity, $data)
    {
        if (!($entity instanceof Entity)) {
            $table = $this->grabTable($entity);
            $entity = $table->newEntity();
        } else {
            $table = $this->grabTable($entity->source());
        }

        return $table->patchEntity($entity, $data);
    }

    public function tryValidating($entity, $data)
    {
        return $this->tryPatching($entity, $data)->errors();
    }
}
