<?php

namespace Reven\DBAL\Utils;

/**
 * Class ClassUtils
 * @package Reven\DBAL\Utils
 */
class ClassUtils
{

    /**
     * @param $argument
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public static function getReflectionClass($argument): \ReflectionClass
    {
        return new \ReflectionClass($argument);
    }

    /**
     * @param $argument
     * @return string
     */
    public static function getShortName($argument): string
    {
        try {
            $reflection = self::getReflectionClass($argument);
            return $reflection->getShortName();
        } catch (\ReflectionException $ex) {
            return '';
        }
    }

    /**
     * @param $argument
     * @param string $types
     * @return array
     * @throws \ReflectionException
     */
    public static function getProperties($argument, string $types = 'public'): array
    {
        $properties = self::getReflectionClass($argument)->getProperties();
        $propertiesArray = [];

        foreach ($properties as $property) {
            if ($property->isPublic() and (stripos($types, 'public') === false)) continue;
            if ($property->isPrivate() and (stripos($types, 'private') === false)) continue;
            if ($property->isProtected() and (stripos($types, 'protected') === false)) continue;
            if ($property->isStatic() and (stripos($types, 'static') === false)) continue;

            $propertiesArray[$property->getName()] = $property;
        }

        return $propertiesArray;
    }

    /**
     * @param $object
     * @param array $properties [property => value, [...]]
     */
    public static function callSetters($object, array $properties)
    {
        foreach ($properties as $property => $value) {
            $setterName = 'set' . StringUtils::convertToStudlyCaps($property);

            if (method_exists($object, $setterName)) {
                call_user_func([$object, $setterName], $value);
            }
        }
    }

}