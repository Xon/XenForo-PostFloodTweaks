<?php

class SV_PostFloodTweaks_Listener
{
    const AddonNameSpace = 'SV_PostFloodTweaks_';

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.$class;
    }
}
