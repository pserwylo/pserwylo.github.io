<?php
require_once( "AttributeFinder.php" );

class RecordService
{
    
    /**
     * This function will pretend to create a record, but will restrict access
     * to users with a position of 'admin' and 'user'.
     * @access admin Requires admin users to create a new record
     * @access user Also must be of type user
     */
    public function createRecord( $name )
    {
        echo "Creating record...\n";
    }

}

$attributeFinder = new AttributeFinder( "RecordService", "createRecord" );
$requiredAccess = $attributeFinder->getAccess();

echo "Only go ahead with the invocation of createRecord() if the current user is in all of these groups:\n";
echo implode( ", ", $requiredAccess ) . "\n";
