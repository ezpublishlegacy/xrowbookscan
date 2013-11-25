<?php

class xrowBookScan
{
    static $className = 'bookscan';
    static $classAttributeNameFile = 'file';
    static $classAttributeNameImage = 'image';
    static $parentNode = null;

    function __construct( $defaultVars, $parentNode )
    {
        if( count( $defaultVars ) > 0 )
        {
            if( isset( $defaultVars['className'] ) )
            {
                self::$className = $defaultVars['className'];
            }
            if( isset( $defaultVars['classAttributeNameFile'] ) )
            {
                self::$classAttributeNameFile = $defaultVars['classAttributeNameFile'];
            }
            if( isset( $defaultVars['classAttributeNameImage'] ) )
            {
                self::$classAttributeNameImage = $defaultVars['classAttributeNameImage'];
            }
        }

        if( isset( $parentNode ) && $parentNode instanceof eZContentObjectTreeNode )
        {
            if( self::$parentNode === null )
            {
                self::$parentNode = $parentNode;
            }
            elseif( self::$parentNode instanceof eZContentObjectTreeNode )
            {
                if( self::$parentNode->NodeID != $parentNode->NodeID )
                {
                    self::$parentNode = $parentNode;
                }
            }
        }
       
        return false;
    }

    function create( $index, $data, $userCreatorID, $sectionID )
    {
        // create object with pdf and image
        try
        {
            if( self::$parentNode instanceof eZContentObjectTreeNode )
            {
                $parentContentObjectTreeNode = self::$parentNode;
                $classID = self::$className;
                $name = $parentContentObjectTreeNode->Name . ' ' . $index;
                $locale = eZLocale::instance( $parentContentObjectTreeNode->CurrentLanguage );
                $datetime_create = new eZDateTime( );
                $datetime_modify = new eZDateTime( );
                $datetime_create->setLocale( $locale );
                $datetime_modify->setLocale( $locale );
                $remoteIdString = $parentContentObjectTreeNode->RemoteID . '_' . $classID . '_' . $index;
                $checkObjectExists = eZContentObject::fetchByRemoteID( $remoteIdString );

                $db = eZDB::instance();
                $db->begin();
                if( !$checkObjectExists instanceof eZContentObject )
                {
                    $contentClassID = $classID;
                    $class = eZContentClass::fetchByIdentifier( $classID );
                    if ( ! is_object( $class ) )
                        $class = eZContentClass::fetch( $contentClassID );
                    $parentContentObject = $parentContentObjectTreeNode->attribute( 'object' );
                    $language = $parentContentObject->currentLanguage();
                    $contentObject = null;
                    // neues ContentObject anlegen
                    $contentObject = $class->instantiate( $userCreatorID, $sectionID, false, $language );
                    $contentObject->store();
                    // MainNode anlegen
                    $merged_node_array = array(
                            'contentobject_id' => $contentObject->attribute( 'id' ),
                            'contentobject_version' => $contentObject->attribute( 'current_version' ),
                            'sort_field' => eZContentObjectTreeNode::SORT_FIELD_PUBLISHED,
                            'sort_order' => eZContentObjectTreeNode::SORT_ORDER_DESC,
                            'parent_node' => $parentContentObjectTreeNode->attribute( 'node_id' ),
                            'is_main' => 1
                    );
                    $nodeAssignment = eZNodeAssignment::create( $merged_node_array );
                    $nodeAssignment->store();
                    $version = $contentObject->version( 1 );
                    // if $item[EZ_IMPORT_PRESERVED_KEY_REMOTE_ID] == null ez will generate a remoteid
                    $contentObject->setAttribute( 'remote_id', $remoteIdString );
                    $contentObject->setAttribute( 'published', $datetime_create->timeStamp() );
                }
                else
                {
                    $contentObject = $checkObjectExists;
                    $language = $contentObject->currentLanguage();
                    $version = $contentObject->currentVersion();
                }
                $contentObject->setAttribute( 'modified', $datetime_modify->timeStamp() );
                $contentObject->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
                $contentObject->store();
                $contentObject->setName( $name );
                // get all attributes and modify data if needed, also if video exists
                foreach( $contentObject->contentObjectAttributes() as $attribute )
                {
                    $dataTypeString = $attribute->attribute( 'data_type_string' );
                    $attribute_identifier = $attribute->attribute( 'contentclass_attribute_identifier' );
                    switch ( $dataTypeString )
                    {
                        case 'ezimage':
                        case 'ezbinaryfile':
                            $attribute->fromString( $data[$attribute_identifier] );
                            $attribute->store();
                            break;
                        case 'ezstring':
                            $attribute->setAttribute( 'data_text', $data[$attribute_identifier] );
                            $attribute->store();
                            break;
                        default:
                            break;
                    }
                    $newAttributes[] = $attribute;
                }
                $contentObject->setContentObjectAttributes( $newAttributes, $version->attribute( 'version' ), $language );

                $db->commit();

                $operationResult = eZOperationHandler::execute( 'content', 'publish', array(
                        'object_id' => $contentObject->attribute( 'id' ) ,
                        'version' => $version->attribute( 'version' )
                ) );
                if( $operationResult['status'] != 1 )
                {
                    throw new Exception( $classID . ' ' . $name . ' konnte nicht publiziert werden' );
                }
                return $contentObject;
            }
            else
            {
                throw new Exception( 'ParentNode fehlt' );
            }
        }
        catch( Exception $e )
        {
            throw new Exception( $e );
        }
    }
}