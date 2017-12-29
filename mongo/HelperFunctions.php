<?php
function bsonUnserialize($map)
{
	$array = [];

    foreach ( $map as $k => $value )
    {
        $array[$k] = $value;
    }

    return $array;
}
?>