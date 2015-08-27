<?php
namespace Acelaya\PersistentLogin\Util;

interface ArraySerializableInterface
{
    /**
     * @param array $data
     */
    public function exchangeArray(array $data);

    /**
     * @return array
     */
    public function getArrayCopy();
}
