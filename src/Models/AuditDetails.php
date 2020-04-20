<?php
declare(strict_types=1);

namespace Parrot\Models;

use Phalcon\Mvc\Model;

/**
 * Class AuditDetails
 * @package Parrot\Mvc\Behavior\Blameable
 */
class AuditDetails extends Model implements AuditDetailInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $audits_id;

    /**
     * @var string
     */
    public $field_name;

    /**
     * @var string
     */
    public $old_value;

    /**
     * @var string
     */
    public $new_value;

    /**
     * Sets relations between models
     */
    public function initialize()
    {
        $this->setup([
            'notNullValidations' => false,
        ]);

        $this->belongsTo(
            'audits_id',
            Audits::class,
            'id',
            [
                'alias' => 'audits',
            ]
        );
    }

    /**
     * @param string $fieldName
     * @return $this
     */
    public function setFieldName($fieldName)
    {
        $this->field_name = $fieldName;

        return $this;
    }

    /**
     * @param string $oldValue
     * @return $this
     */
    public function setOldValue($oldValue)
    {
        $this->old_value = $oldValue;

        return $this;
    }

    /**
     * @param string $newValue
     * @return $this
     */
    public function setNewValue($newValue)
    {
        $this->new_value = $newValue;

        return $this;
    }
}
