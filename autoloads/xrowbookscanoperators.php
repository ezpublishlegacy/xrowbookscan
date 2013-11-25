<?php

class xrowBookScanOperators
{
    function xrowBookScanOperators()
    {
    }

    function operatorList()
    {
        return array( 'xbs_secure' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'xbs_secure' => array() );
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        $node_id = $operatorValue;
        switch ( $operatorName )
        {
            case 'xbs_secure':
            {
                $xrowbookscan_ini = eZINI::instance( 'xrowbookscan.ini' );
                $secureToken = $xrowbookscan_ini->variable( 'Settings', 'SecureToken' );
                $operatorValue = base64_encode( $secureToken . $node_id );
            }
            break;
            default:
            break;
        }
    }
}