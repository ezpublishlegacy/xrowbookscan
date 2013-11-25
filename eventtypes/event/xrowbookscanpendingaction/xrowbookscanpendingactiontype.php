<?php

/**
 * Save an action in ezpending after publish a PDF
 */
class xrowBookScanPendingActionType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "xrowbookscanpendingaction";

    function xrowBookScanPendingActionType()
    {
        $this->eZWorkflowEventType( xrowBookScanPendingActionType::WORKFLOW_TYPE_STRING, "xrow Save bookscan action into ezpending" );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $obj = eZContentObject::fetch( $parameters['object_id'] );
        $filterConds = array( 'action' => 'bookscan', 'param' => $parameters['object_id'] );
        $result = eZPersistentObject::fetchObjectList( eZPendingActions::definition(), null, $filterConds );
        if( count( $result ) == 0 )
        {
            $addToPendingAction = false;
            if ( $obj instanceof eZContentObject )
            {
                $xrowbookscan_ini = eZINI::instance( 'xrowbookscan.ini' );
                // check if object should be added to pending action
                if( $xrowbookscan_ini->hasVariable( 'Settings', 'ClassNameParentForBookscan' ) )
                {
                    $classNames = $xrowbookscan_ini->variable( 'Settings', 'ClassNameParentForBookscan' );
                    if( count( $classNames ) > 0 )
                    {
                        foreach( $classNames as $className )
                        {
                            if( $className == $obj->ClassIdentifier )
                            {
                                if( $xrowbookscan_ini->hasSection( 'ParentForBookscan_' . $className ) )
                                {
                                    $settingsBlock = $xrowbookscan_ini->BlockValues['ParentForBookscan_' . $className];
                                    if( isset( $settingsBlock['AttributeNameAddToSolrIndex'] ) && $settingsBlock['AttributeNameAddToSolrIndex'] != '' )
                                    {
                                        $checkValue = $settingsBlock['AttributeNameAddToSolrIndex'];
                                        $dataMap = $obj->dataMap();
                                        if( isset( $dataMap[$checkValue] ) )
                                        {
                                            $attributeContent = $dataMap[$checkValue]->content();
                                            if( $attributeContent == 1 )
                                            {
                                                $addToPendingAction = true;
                                            }
                                        }
                                    }
                                    else
                                        $addToPendingAction = true;
                                    if( $addToPendingAction !== false )
                                    {
                                        $ezpending = new eZPendingActions();
                                        $obj_id = $obj->attribute( 'id' );
                                        $ezpending->setAttribute( 'action', 'bookscan' );
                                        $ezpending->setAttribute( 'param', $obj_id );
                                        $ezpending->store();
                                    }
                                    return eZWorkflowEventType::STATUS_ACCEPTED;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                eZDebug::writeError( 'object id ' . $parameters['object_id'] . ' does not exist', 'xrowBookScanPendingActionType' );
                return eZWorkflowEventType::STATUS_WORKFLOW_CANCELLED;
            }
        }
    }
}
eZWorkflowEventType::registerEventType( xrowBookScanPendingActionType::WORKFLOW_TYPE_STRING, "xrowBookScanPendingActionType" );