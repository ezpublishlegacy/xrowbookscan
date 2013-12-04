<?php
require_once ( 'kernel/common/template.php' );

class xrowBookScanJscoreFunctions extends ezjscServerFunctions
{
    public static function viewPage( $params )
    {
        $image = false;
        $content = '';
        if( isset( $params[0] ) && isset( $params[1] ) )
        {
            $id = $params[0];
            $contid = $params[1];
            $xrowbookscan_ini = eZINI::instance( 'xrowbookscan.ini' );
            if( $xrowbookscan_ini->hasVariable( 'Settings', 'SecureToken' ) )
            {
                $secureToken = $xrowbookscan_ini->variable( 'Settings', 'SecureToken' );
                $imageAttrName = $xrowbookscan_ini->variable( 'Settings', 'ClassAttributeNameForConvertedImage' );
                $nodeID = preg_replace( '/' . $secureToken . '/', '', base64_decode( $id ) );
                $node = eZContentObjectTreeNode::fetch( $nodeID );
                if( !( $node instanceof eZContentObjectTreeNode ) )
                {
                    eZDebug::writeError( "Node with NodeID '$nodeID' does not exist", 'xrowbookscan/view.php' );
                    return null;
                }
                else
                {
                    $dataMap = $node->dataMap();
                    $imageAttrContent = $dataMap[$imageAttrName]->content();
                    $image = $imageAttrContent->attribute( 'original' );
                    $tpl = templateInit();
                    $tpl->setVariable( 'image', $image );
                    $tpl->setVariable( 'contid', $contid );
                    if( isset( $params[2] ) )
                    {
                        $href = preg_replace( '/' . $secureToken . '/', '', base64_decode( $params[2] ) );
                        $tpl->setVariable( 'href', $href );
                    }
                    return $tpl->fetch( 'design:viewimage.tpl' );
                }
            }
            else
            {
                eZDebug::writeError( 'Please add your own SecureToken in Setting, xrowbookscan.ini', 'xrowbookscan/view.php' );
            }
        }
    }
}