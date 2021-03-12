<?php

/**
 * Class ArrayMatch
 */
class ArrayMatch
{
    /**
     * @param string $pattern
     * @param array $array
     * @param array|null $matches
     * @return bool
     */
    public static function match(string $pattern, array $array, array &$matches = null)
    {
        $matches = [];
        $parts = explode('.', $pattern);

        self::_match($parts, $array, $matches);

        if ($matches) {
            return true;
        }

        return false;
    }

    /**
     * @param array $path
     * @param $replaceWith
     * @param $array
     */
    public static function replace(array $path, $replaceWith, &$array)
    {
        $currentKey = current($path);
        $nextKey = next($path);

        if (false === $nextKey) {
            $array[$currentKey] = $replaceWith;
        } else {
            self::replace($path, $replaceWith, $array[$currentKey]);
        }
    }

    /**
     * @param array $parts
     * @param array $data
     * @param array|null $matches
     * @param array $track
     * @param int $level
     * @return false
     */
    private static function _match(array $parts, array $data, array &$matches = null, &$track = [], $level = 0)
    {
        $currentPart = current($parts);
        $nextPart = next($parts);

        foreach ($data as $key => $value) {
            $tempParts = $parts;

            $track = array_slice($track, 0, $level);
            $track[] = $key;

            if ($nextPart && is_array($value)) {
                if ($currentPart === '*') {
                    if (array_key_exists($nextPart, $value)) {
                        array_shift($tempParts);
                    } else {
                        reset($tempParts);
                    }

                    self::_match($tempParts, $value, $matches, $track, $level + 1);
                } elseif ($currentPart === $key) {
                    array_shift($tempParts);
                    self::_match($tempParts, $value, $matches, $track, $level + 1);
                }
            } elseif (!$nextPart && ($key === $currentPart || $currentPart === '*')) {
                $matches[] = ['track' => $track, 'value' => $value];
            }
        }

        return false;
    }
}
