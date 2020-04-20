<?php
declare(strict_types=1);

namespace Parrot\Models;

use Phalcon\Http\Request;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Session\Adapter;

/**
 * Parrot\Mvc\Model\Audits
 *
 * @package Parrot\Mvc\Behavior\Blameable
 */
class Audits extends Model implements AuditInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $users_id;

    /**
     * @var string
     */
    public $model_name;

    /**
     * @var int
     */
    public $model_id;

    /**
     * @var string
     */
    public $ip_address;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var ModelInterface
     */
    public $model;

    /**
     * @var array
     */
    public $primary_key;

    /**
     * @var callable
     */
    public $userCallback;

    /**
     * Sets relations between models
     */
    public function initialize()
    {
        $this->hasMany(
            'id',
            AuditDetails::class,
            'audits_id',
            [
                'alias' => 'details',
            ]
        );
    }

    /**
     * Executes code to set audits all needed data, like ipaddress, username, created_at etc
     */
    public function beforeValidation()
    {
        if (empty($this->userCallback)) {
            /** @var Adapter $session */
            $session = $this->getDI()->get('session');

            // Get the userId from session
            $this->users_id = $session->get('userId');
        } else {
            $userCallback = $this->userCallback;

            $this->users_id = $userCallback($this->getDI());
        }

        //The model who performed the action
        $this->model_name = get_class($this->model);

        $this->model_id = $this->model->id;

        /** @var Request $request */
        $request = $this->getDI()->get('request');

        //The client IP address
        $this->ip_address = $request->getClientAddress();

        //Current time
        $this->created_at = date('Y-m-d H:i:s');

        $primaryKeys = $this->getModelsMetaData()->getPrimaryKeyAttributes(
            $this->model
        );

        $columnMap = $this->getModelsMetaData()->getColumnMap(
            $this->model
        );

        $primaryValues = [];
        if (!empty($columnMap)) {
            foreach ($primaryKeys as $primaryKey) {
                $primaryValues[] = $this->model->readAttribute(
                    $columnMap[$primaryKey]
                );
            }
        } else {
            foreach ($primaryKeys as $primaryKey) {
                $primaryValues[] = $this->model->readAttribute($primaryKey);
            }
        }

        $this->primary_key = json_encode($primaryValues);
    }

    public function afterSave()
    {
        $this->primary_key = json_decode(
            $this->primary_key,
            true
        );
    }

    public function afterFetch()
    {
        $this->primary_key = json_decode(
            $this->primary_key,
            true
        );
    }

    /**
     * @param ModelInterface $model
     * @return $this
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param $userCallback
     * @return $this
     */
    public function setUserCallback($userCallback)
    {
        $this->userCallback = $userCallback;

        return $this;
    }
}
