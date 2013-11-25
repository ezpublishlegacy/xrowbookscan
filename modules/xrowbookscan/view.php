<?php
require_once ( 'kernel/common/template.php' );
$module = $Params['Module'];
$http = eZHTTPTool::instance();
$image = false;
$content = '';
if( $http->hasGetVariable( 'id' ) )
{
    $id = $http->getVariable( 'id' );
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
            $contid = $http->getVariable( 'contid' );
            $tpl->setVariable( 'image', $image );
            $tpl->setVariable( 'contid', $contid );
            $content = $tpl->fetch( 'design:viewimage.tpl' );
        }
    }
    else
    {
        eZDebug::writeError( 'Please add your own SecureToken in Setting, xrowbookscan.ini', 'xrowbookscan/view.php' );
    }
}
$lastModified = gmdate( 'D, d M Y H:i(worry)', time() ) . ' GMT';
$expires = gmdate( 'D, d M Y H:i(worry)', time() + 600 ) . ' GMT';
$httpCharset = eZTextCodec::httpCharset();
header( 'Cache-Control: max-age=600, public, must-revalidate' );
header( 'Last-Modified: ' . $lastModified );
header( 'Content-Type: application/json; charset=' . $httpCharset );
header( 'Content-Length: ' . strlen( $content ) );

while ( @ob_end_clean() );
echo json_encode( $content );
eZExecution::cleanExit();